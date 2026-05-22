<?php
// customer/api.php — Public JSON API (no auth required)
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';

$db     = getDB();
$action = $_REQUEST['action'] ?? '';

function jsonOK(array $data): void { echo json_encode(['ok' => true] + $data); exit; }
function jsonErr(string $msg, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg]); exit;
}
function sanitize(string $s): string { return htmlspecialchars(trim($s), ENT_QUOTES); }
function plainText(string $s): string { return trim($s); }
function decodeEntityFields(array $row, array $fields): array {
    foreach ($fields as $field) {
        if (array_key_exists($field, $row) && $row[$field] !== null) {
            $row[$field] = html_entity_decode((string)$row[$field], ENT_QUOTES, 'UTF-8');
        }
    }
    return $row;
}
function parseJsonArray(string $raw): array {
    if ($raw === '') {
        return [];
    }
    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}
function bookingConfig(): array {
    return [
        'restaurant' => ['restaurant'],
        'catering' => ['catering'],
        'cafe' => ['cafe', 'pastry'],
        'venue' => [],
    ];
}
function normalizeMenuSelections(array $items): array {
    $normalized = [];
    foreach ($items as $item) {
        $id = (int)($item['menu_item_id'] ?? 0);
        $qty = (int)($item['quantity'] ?? 0);
        if ($id > 0 && $qty > 0) {
            $normalized[] = ['menu_item_id' => $id, 'quantity' => $qty];
        }
    }
    return $normalized;
}
function normalizeAddonSelections(array $addons): array {
    $normalized = [];
    foreach ($addons as $addon) {
        $name = plainText((string)($addon['name'] ?? ''));
        $type = plainText((string)($addon['type'] ?? 'addon'));
        $code = plainText((string)($addon['code'] ?? ''));
        $qty = (int)($addon['quantity'] ?? 0);
        $price = (float)($addon['unit_price'] ?? 0);
        if ($name !== '' && $qty > 0) {
            $normalized[] = [
                'code' => $code,
                'name' => $name,
                'type' => $type,
                'quantity' => $qty,
                'unit_price' => max(0, $price),
            ];
        }
    }
    return $normalized;
}
function fetchMenuCatalog(PDO $db, array $categories): array {
    if (!$categories) {
        return [];
    }
    $placeholders = implode(',', array_fill(0, count($categories), '?'));
    $stmt = $db->prepare("SELECT id,name,category,price,is_available FROM menu_items WHERE is_available=1 AND category IN ($placeholders)");
    $stmt->execute($categories);
    $catalog = [];
    foreach ($stmt->fetchAll() as $row) {
        $catalog[(int)$row['id']] = $row;
    }
    return $catalog;
}
function computeSelectionTotals(array $menuSelections, array $menuCatalog, array $addons): array {
    $menuTotal = 0.0;
    $addonTotal = 0.0;
    $hasCustomQuote = false;

    foreach ($menuSelections as $selection) {
        $menuItem = $menuCatalog[$selection['menu_item_id']] ?? null;
        if (!$menuItem) {
            continue;
        }
        $price = (float)$menuItem['price'];
        if ($price <= 0) {
            $hasCustomQuote = true;
            continue;
        }
        $menuTotal += $price * $selection['quantity'];
    }

    foreach ($addons as $addon) {
        $price = (float)$addon['unit_price'];
        if ($price <= 0) {
            $hasCustomQuote = true;
            continue;
        }
        $addonTotal += $price * $addon['quantity'];
    }

    return [
        'menu_total' => $menuTotal,
        'addon_total' => $addonTotal,
        'total' => $menuTotal + $addonTotal,
        'has_custom_quote' => $hasCustomQuote,
    ];
}
function persistBookingSelections(PDO $db, int $bookingId, array $menuSelections, array $menuCatalog, array $addons): void {
    if ($menuSelections) {
        $stmt = $db->prepare('INSERT INTO booking_items (booking_id, menu_item_id, quantity, unit_price) VALUES (?,?,?,?)');
        foreach ($menuSelections as $selection) {
            $menuItem = $menuCatalog[$selection['menu_item_id']] ?? null;
            if (!$menuItem) {
                continue;
            }
            $stmt->execute([$bookingId, $selection['menu_item_id'], $selection['quantity'], (float)$menuItem['price']]);
        }
    }

    if ($addons) {
        $stmt = $db->prepare('INSERT INTO booking_addons (booking_id, addon_code, addon_name, addon_type, quantity, unit_price) VALUES (?,?,?,?,?,?)');
        foreach ($addons as $addon) {
            $stmt->execute([
                $bookingId,
                $addon['code'] ?: null,
                $addon['name'],
                $addon['type'],
                $addon['quantity'],
                $addon['unit_price'],
            ]);
        }
    }
}

switch ($action) {

    // ── PUBLIC MENU ──────────────────────────────────────────
    case 'menu':
        $q   = '%' . trim($_GET['q'] ?? '') . '%';
        $cat = $_GET['category'] ?? '';
        $sql = 'SELECT id,name,category,price,description,image_path FROM menu_items WHERE is_available=1 AND (name LIKE ? OR description LIKE ?)';
        $p   = [$q, $q];
        if ($cat) { $sql .= ' AND category = ?'; $p[] = $cat; }
        $sql .= ' ORDER BY category, name';
        $stmt = $db->prepare($sql);
        $stmt->execute($p);
        jsonOK(['items' => $stmt->fetchAll()]);
        break;

    // ── PUBLIC VENUES ────────────────────────────────────────
    case 'venues':
        $rows = $db->query('SELECT id,name,type,capacity,rate,description,is_available FROM venues ORDER BY is_available DESC, rate')->fetchAll();
        jsonOK(['venues' => $rows]);
        break;

    // ── CHECK BOOKING STATUS ─────────────────────────────────
    case 'track':
        $ticket = trim($_GET['ticket'] ?? '');
        if (!$ticket) jsonErr('Ticket number required');
        $stmt = $db->prepare('SELECT ticket_no,customer_name,service_type,event_date,event_time,pax,total_amount,discount_percent,final_amount,status,notes,pricing_notes,created_at FROM bookings WHERE ticket_no = ? LIMIT 1');
        $stmt->execute([$ticket]);
        $booking = $stmt->fetch();
        if (!$booking) jsonErr('Booking not found', 404);
        $booking = decodeEntityFields($booking, ['customer_name', 'notes', 'pricing_notes']);
        $itemStmt = $db->prepare('SELECT bi.quantity, bi.unit_price, m.name FROM booking_items bi JOIN menu_items m ON m.id = bi.menu_item_id WHERE bi.booking_id = (SELECT id FROM bookings WHERE ticket_no = ? LIMIT 1)');
        $itemStmt->execute([$ticket]);
        $addonStmt = $db->prepare('SELECT addon_name, addon_type, quantity, unit_price FROM booking_addons WHERE booking_id = (SELECT id FROM bookings WHERE ticket_no = ? LIMIT 1)');
        $addonStmt->execute([$ticket]);
        $addons = array_map(
            static fn(array $addon): array => decodeEntityFields($addon, ['addon_name', 'addon_type']),
            $addonStmt->fetchAll()
        );
        jsonOK([
            'booking' => $booking,
            'items' => $itemStmt->fetchAll(),
            'addons' => $addons,
        ]);
        break;

    // ── SUBMIT BOOKING INQUIRY ───────────────────────────────
    case 'inquire':
        // Spam protection: check honeypot
        if (!empty($_POST['honey'])) {
            jsonErr('Spam detected');
        }

        $name    = plainText($_POST['customer_name'] ?? '');
        $email   = filter_var(trim($_POST['customer_email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $phone   = trim($_POST['customer_phone'] ?? '');
        $service = $_POST['service_type'] ?? 'restaurant';
        $pax     = (int)($_POST['pax'] ?? 1);
        $date    = $_POST['event_date'] ?: null;
        $time    = normalizeBookingTime($_POST['event_time'] ?? null);
        $notes   = plainText($_POST['notes'] ?? '');
        $menuSelections = normalizeMenuSelections(parseJsonArray($_POST['selected_items'] ?? ''));
        $addonSelections = normalizeAddonSelections(parseJsonArray($_POST['selected_addons'] ?? ''));

        if (!$name || !$phone) jsonErr('Name and phone number are required');
        
        // Basic phone validation (at least 7-15 digits, allows +, -, spaces)
        if (!preg_match('/^[0-9\-\+\s]{7,15}$/', $phone)) {
            jsonErr('Please enter a valid phone number');
        }
        
        if ($_POST['customer_email'] && !$email) {
            jsonErr('Please enter a valid email address');
        }

        if (!in_array($service, ['restaurant','catering','cafe','venue'])) jsonErr('Invalid service type');
        if ($pax < 1) jsonErr('Guest count must be at least 1');
        
        if (!$date) {
            jsonErr('Please select your preferred date');
        } else {
            try {
                $bookingDate = new DateTime($date);
                $today = new DateTime('today');
                if ($bookingDate < $today) {
                    jsonErr('The booking date cannot be in the past');
                }
            } catch (Exception $e) {
                jsonErr('Please enter a valid date');
            }
        }

        if ($time === null) {
            jsonErr('Please select a valid booking time');
        }

        $allowedCategories = bookingConfig()[$service];
        $menuCatalog = fetchMenuCatalog($db, $allowedCategories);

        if (in_array($service, ['restaurant', 'catering', 'cafe'], true) && !$menuSelections) {
            jsonErr('Please select at least one menu choice for this booking');
        }

        // Venue amount auto-fill & Availability Check
        $venue_id = null;
        $amount = 0.00;
        if ($service === 'venue') {
            $venue_id = (int)($_POST['venue_id'] ?? 0);
            if (!$venue_id) {
                jsonErr('Please choose a venue');
            }
            if ($venue_id) {
                $v = $db->prepare('SELECT id,name,rate FROM venues WHERE id=? AND is_available=1');
                $v->execute([$venue_id]);
                $vr = $v->fetch();
                if (!$vr) {
                    jsonErr('Selected venue is unavailable');
                }
                $amount = (float)$vr['rate'];

                $conflict = findVenueBookingConflict($db, $venue_id, $date, $time);
                if ($conflict) {
                    jsonErr(buildVenueConflictMessage($conflict, $date, $time), 409);
                }
            }
        }

        foreach ($menuSelections as $selection) {
            $menuItem = $menuCatalog[$selection['menu_item_id']] ?? null;
            if (!$menuItem) {
                jsonErr('One or more selected menu items are unavailable');
            }
        }

        $totals = computeSelectionTotals($menuSelections, $menuCatalog, $addonSelections);
        $amount += $totals['total'];
        $details = json_encode([
            'service' => $service,
            'custom_quote_required' => $totals['has_custom_quote'],
            'menu_item_count' => count($menuSelections),
            'addon_count' => count($addonSelections),
        ], JSON_UNESCAPED_UNICODE);

        $db->beginTransaction();
        try {
            $tempTicket = 'TEMP-' . bin2hex(random_bytes(4));
            $db->prepare('INSERT INTO bookings (ticket_no,customer_name,customer_email,customer_phone,service_type,venue_id,details,pax,event_date,event_time,total_amount,notes,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)')
               ->execute([$tempTicket, $name,$email,$phone,$service,$venue_id,$details,$pax,$date,$time,$amount,$notes,'pending']);
            
            $bookingId = (int)$db->lastInsertId();
            $ticket = '#MP-' . str_pad($bookingId + 299, 3, '0', STR_PAD_LEFT);
            $db->prepare('UPDATE bookings SET ticket_no=? WHERE id=?')->execute([$ticket, $bookingId]);

            persistBookingSelections($db, $bookingId, $menuSelections, $menuCatalog, $addonSelections);
            $db->commit();
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("Booking error: " . $e->getMessage());
            jsonErr('Could not save your booking right now. Please try again later.', 500);
        }

        jsonOK([
            'ticket_no' => $ticket,
            'message' => 'Inquiry submitted successfully! Save your ticket number.',
            'estimated_amount' => $amount,
            'custom_quote_required' => $totals['has_custom_quote'],
        ]);

    default:
        jsonErr('Unknown action', 404);
}
