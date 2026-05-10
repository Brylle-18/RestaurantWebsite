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
        $name = sanitize((string)($addon['name'] ?? ''));
        $type = sanitize((string)($addon['type'] ?? 'addon'));
        $code = sanitize((string)($addon['code'] ?? ''));
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
        $addonTotal += ((float)$addon['unit_price']) * $addon['quantity'];
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
        $sql = 'SELECT id,name,category,price,description FROM menu_items WHERE is_available=1 AND (name LIKE ? OR description LIKE ?)';
        $p   = [$q, $q];
        if ($cat) { $sql .= ' AND category = ?'; $p[] = $cat; }
        $sql .= ' ORDER BY category, name';
        $stmt = $db->prepare($sql);
        $stmt->execute($p);
        jsonOK(['items' => $stmt->fetchAll()]);

    // ── PUBLIC VENUES ────────────────────────────────────────
    case 'venues':
        $rows = $db->query('SELECT id,name,type,capacity,rate,description,is_available FROM venues WHERE is_available=1 ORDER BY rate')->fetchAll();
        jsonOK(['venues' => $rows]);

    // ── CHECK BOOKING STATUS ─────────────────────────────────
    case 'track':
        $ticket = trim($_GET['ticket'] ?? '');
        if (!$ticket) jsonErr('Ticket number required');
        $stmt = $db->prepare('SELECT ticket_no,customer_name,service_type,event_date,pax,total_amount,status,notes,pricing_notes,created_at FROM bookings WHERE ticket_no = ? LIMIT 1');
        $stmt->execute([$ticket]);
        $booking = $stmt->fetch();
        if (!$booking) jsonErr('Booking not found', 404);
        $itemStmt = $db->prepare('SELECT bi.quantity, bi.unit_price, m.name FROM booking_items bi JOIN menu_items m ON m.id = bi.menu_item_id WHERE bi.booking_id = (SELECT id FROM bookings WHERE ticket_no = ? LIMIT 1)');
        $itemStmt->execute([$ticket]);
        $addonStmt = $db->prepare('SELECT addon_name, addon_type, quantity, unit_price FROM booking_addons WHERE booking_id = (SELECT id FROM bookings WHERE ticket_no = ? LIMIT 1)');
        $addonStmt->execute([$ticket]);
        jsonOK([
            'booking' => $booking,
            'items' => $itemStmt->fetchAll(),
            'addons' => $addonStmt->fetchAll(),
        ]);

    // ── SUBMIT BOOKING INQUIRY ───────────────────────────────
    case 'inquire':
        $name    = sanitize($_POST['customer_name'] ?? '');
        $email   = sanitize($_POST['customer_email'] ?? '');
        $phone   = sanitize($_POST['customer_phone'] ?? '');
        $service = $_POST['service_type'] ?? 'restaurant';
        $pax     = (int)($_POST['pax'] ?? 1);
        $date    = $_POST['event_date'] ?: null;
        $notes   = sanitize($_POST['notes'] ?? '');
        $menuSelections = normalizeMenuSelections(parseJsonArray($_POST['selected_items'] ?? ''));
        $addonSelections = normalizeAddonSelections(parseJsonArray($_POST['selected_addons'] ?? ''));

        if (!$name || !$phone) jsonErr('Name and phone number are required');
        if (!in_array($service, ['restaurant','catering','cafe','venue'])) jsonErr('Invalid service type');
        if ($pax < 1) jsonErr('Guest count must be at least 1');
        if (!$date) jsonErr('Please select your preferred date');

        $allowedCategories = bookingConfig()[$service];
        $menuCatalog = fetchMenuCatalog($db, $allowedCategories);

        if (in_array($service, ['restaurant', 'catering', 'cafe'], true) && !$menuSelections) {
            jsonErr('Please select at least one menu choice for this booking');
        }

        // Auto-generate ticket number
        $last   = $db->query("SELECT id FROM bookings ORDER BY id DESC LIMIT 1")->fetchColumn();
        $ticket = '#MP-' . str_pad(($last ? $last + 1 : 300), 3, '0', STR_PAD_LEFT);

        // Venue amount auto-fill
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
            $db->prepare('INSERT INTO bookings (ticket_no,customer_name,customer_email,customer_phone,service_type,venue_id,details,pax,event_date,total_amount,notes,status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)')
               ->execute([$ticket,$name,$email,$phone,$service,$venue_id,$details,$pax,$date,$amount,$notes,'pending']);
            $bookingId = (int)$db->lastInsertId();
            persistBookingSelections($db, $bookingId, $menuSelections, $menuCatalog, $addonSelections);
            $db->commit();
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            jsonErr('Could not save your booking right now', 500);
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
