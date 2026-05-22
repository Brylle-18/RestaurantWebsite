<?php
// admin/api.php — JSON API for admin dashboard actions
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

// Enforce CSRF protection for POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
}

$db     = getDB();
$action = $_REQUEST['action'] ?? '';

// ── helper ──────────────────────────────────────────────────
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
function bookingStatusNeedsPrice(string $status): bool {
    return in_array($status, ['confirmed', 'in_progress', 'completed'], true);
}
function bookingRevenueExpression(): string {
    return 'CASE WHEN b.final_amount > 0 THEN b.final_amount ELSE b.total_amount END';
}
function toFloat(mixed $value): float {
    return round((float)$value, 2);
}
function laborerCount(): int { return 4; }
function laborerDailyWage(): float { return 500.00; }
function laborWorkDaysPerWeek(): int { return 6; }
function workingDaysForPeriod(int $periodDays): int {
    $fullWeeks = intdiv(max(0, $periodDays), 7);
    $remainingDays = max(0, $periodDays % 7);
    return ($fullWeeks * laborWorkDaysPerWeek()) + min($remainingDays, laborWorkDaysPerWeek());
}
function staffCostBreakdown(PDO $db, int $periodDays): array {
    $rows = array_slice($db->query('SELECT name, role FROM staff ORDER BY id ASC')->fetchAll(), 0, laborerCount());
    $staff = [];
    $dailyCostPerLaborer = laborerDailyWage();
    $workingDays = workingDaysForPeriod($periodDays);

    for ($index = 0; $index < laborerCount(); $index++) {
        $row = $rows[$index] ?? null;
        $staff[] = [
            'name' => $row ? html_entity_decode((string)$row['name'], ENT_QUOTES, 'UTF-8') : 'Laborer ' . ($index + 1),
            'role' => $row ? html_entity_decode((string)$row['role'], ENT_QUOTES, 'UTF-8') : 'Operations Staff',
            'status' => 'scheduled',
            'daily_rate' => toFloat($dailyCostPerLaborer),
            'estimated_daily_cost' => toFloat($dailyCostPerLaborer),
            'estimated_period_cost' => toFloat($dailyCostPerLaborer * $workingDays),
        ];
    }

    $dailyTotal = laborerCount() * $dailyCostPerLaborer;

    return [
        'daily_total' => toFloat($dailyTotal),
        'period_total' => toFloat($dailyTotal * $workingDays),
        'working_days' => $workingDays,
        'staff' => $staff,
    ];
}
function serviceFoodCostRatio(string $serviceType): float {
    return match ($serviceType) {
        'restaurant' => 0.42,
        'catering' => 0.50,
        'cafe' => 0.33,
        'venue' => 0.18,
        default => 0.35,
    };
}
function estimateBookingFoodCost(array $booking): float {
    $revenue = (float)($booking['recognized_revenue'] ?? 0);
    $menuSubtotal = (float)($booking['menu_subtotal'] ?? 0);
    $addonSubtotal = (float)($booking['addon_subtotal'] ?? 0);
    $serviceRatio = serviceFoodCostRatio((string)($booking['service_type'] ?? 'restaurant'));

    if ($menuSubtotal <= 0 && $addonSubtotal <= 0) {
        return toFloat($revenue * $serviceRatio);
    }

    $itemCost = ($menuSubtotal * 0.45) + ($addonSubtotal * 0.35);
    $unpricedRemainder = max(0, $revenue - $menuSubtotal - $addonSubtotal);
    $supportCost = $unpricedRemainder * ($serviceRatio * 0.65);

    return toFloat($itemCost + $supportCost);
}
function buildPieDataset(array $items): array {
    return array_values(array_filter(array_map(static function (array $item): ?array {
        $value = (float)($item['value'] ?? 0);
        if ($value <= 0) {
            return null;
        }
        return [
            'label' => $item['label'],
            'value' => toFloat($value),
            'color' => $item['color'],
        ];
    }, $items)));
}
function parsePeriodDays(string $startDate, string $endDate): int {
    try {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        return max(1, (int)$start->diff($end)->days + 1);
    } catch (Throwable) {
        return 1;
    }
}

// ── router ──────────────────────────────────────────────────
switch ($action) {

    // ── DASHBOARD STATS ─────────────────────────────────────
    case 'stats':
        $bookings  = $db->query('SELECT COUNT(*) FROM bookings WHERE WEEK(created_at)=WEEK(NOW())')->fetchColumn();
        $pending   = $db->query("SELECT COUNT(*) FROM bookings WHERE status='pending'")->fetchColumn();
        $revenue   = $db->query("SELECT COALESCE(SUM(CASE WHEN final_amount > 0 THEN final_amount ELSE total_amount END),0) FROM bookings WHERE status='completed' AND MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())")->fetchColumn();
        $dishes    = $db->query('SELECT COUNT(*) FROM menu_items WHERE is_available=1')->fetchColumn();
        $daysInMonth = (int)date('t');
        $staffCosts = staffCostBreakdown($db, $daysInMonth);
        $estimatedFoodCost = (float)$db->query("
            SELECT COALESCE(SUM(
                (CASE WHEN final_amount > 0 THEN final_amount ELSE total_amount END) *
                CASE service_type
                    WHEN 'restaurant' THEN 0.42
                    WHEN 'catering' THEN 0.50
                    WHEN 'cafe' THEN 0.33
                    WHEN 'venue' THEN 0.18
                    ELSE 0.35
                END
            ),0)
            FROM bookings
            WHERE status='completed' AND MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())
        ")->fetchColumn();
        $netProfit = (float)$revenue - $estimatedFoodCost - $staffCosts['period_total'];
        jsonOK([
            'weekly_bookings'=>$bookings,
            'pending'=>$pending,
            'monthly_revenue'=>(float)$revenue,
            'monthly_net_profit'=>toFloat($netProfit),
            'active_dishes'=>$dishes
        ]);

    // ── BOOKINGS LIST / SEARCH ───────────────────────────────
    case 'bookings':
        $q      = '%' . trim($_GET['q'] ?? '') . '%';
        $status = $_GET['status'] ?? '';
        $today  = (int)($_GET['today'] ?? 0);
        $page   = max(1, (int)($_GET['page'] ?? 1));
        $limit  = 5;
        $offset = ($page - 1) * $limit;

        $sql    = 'SELECT b.*, v.name AS venue_name FROM bookings b LEFT JOIN venues v ON b.venue_id = v.id WHERE (b.ticket_no LIKE ? OR b.customer_name LIKE ? OR b.customer_email LIKE ?)';
        $params = [$q, $q, $q];
        
        if ($status) { $sql .= ' AND b.status = ?'; $params[] = $status; }
        if ($today) { $sql .= ' AND DATE(b.event_date) = CURDATE()'; }
        
        // Get total count
        $countSql = 'SELECT COUNT(*) FROM bookings b WHERE (b.ticket_no LIKE ? OR b.customer_name LIKE ? OR b.customer_email LIKE ?)';
        $countParams = [$q, $q, $q];
        if ($status) { $countSql .= ' AND b.status = ?'; $countParams[] = $status; }
        if ($today) { $countSql .= ' AND DATE(b.event_date) = CURDATE()'; }
        $countStmt = $db->prepare($countSql);
        $countStmt->execute($countParams);
        $totalCount = (int)$countStmt->fetchColumn();
        
        $sql .= ' ORDER BY b.created_at DESC LIMIT ? OFFSET ?';
        $params[] = $limit;
        $params[] = $offset;
        $rows = $db->prepare($sql);
        $rows->execute($params);
        $totalPages = ceil($totalCount / $limit);
        $bookings = array_map(
            static fn(array $booking): array => decodeEntityFields($booking, ['customer_name', 'customer_email', 'customer_phone', 'notes', 'pricing_notes', 'venue_name']),
            $rows->fetchAll()
        );
        jsonOK(['bookings' => $bookings, 'page' => $page, 'total_pages' => $totalPages, 'total_count' => $totalCount]);

    // ── SINGLE BOOKING ───────────────────────────────────────
    case 'booking_get':
        $id  = (int)($_GET['id'] ?? 0);
        $row = $db->prepare('SELECT b.*, v.name AS venue_name FROM bookings b LEFT JOIN venues v ON b.venue_id=v.id WHERE b.id=?');
        $row->execute([$id]);
        $booking = $row->fetch();
        if (!$booking) jsonErr('Not found', 404);
        $booking = decodeEntityFields($booking, ['customer_name', 'customer_email', 'customer_phone', 'notes', 'pricing_notes', 'venue_name']);
        $items = $db->prepare('SELECT bi.*, m.name AS dish FROM booking_items bi JOIN menu_items m ON bi.menu_item_id=m.id WHERE bi.booking_id=?');
        $items->execute([$id]);
        $addons = $db->prepare('SELECT * FROM booking_addons WHERE booking_id=? ORDER BY addon_type, addon_name');
        $addons->execute([$id]);
        $addonRows = array_map(
            static fn(array $addon): array => decodeEntityFields($addon, ['addon_code', 'addon_name', 'addon_type', 'notes']),
            $addons->fetchAll()
        );
        jsonOK(['booking' => $booking, 'items' => $items->fetchAll(), 'addons' => $addonRows]);

    // ── ADD BOOKING ──────────────────────────────────────────
    case 'booking_add':
        $name    = plainText($_POST['customer_name'] ?? '');
        $email   = plainText($_POST['customer_email'] ?? '');
        $phone   = plainText($_POST['customer_phone'] ?? '');
        $service = $_POST['service_type'] ?? 'restaurant';
        $pax     = (int)($_POST['pax'] ?? 1);
        $date    = $_POST['event_date'] ?: null;
        $time    = normalizeBookingTime($_POST['event_time'] ?? null);
        $amount  = (float)($_POST['total_amount'] ?? 0);
        $notes   = plainText($_POST['notes'] ?? '');
        if (!$name) jsonErr('Customer name required');
        if ($date && $time === null) jsonErr('Please select a valid booking time');
        
        $db->beginTransaction();
        try {
            $db->prepare('INSERT INTO bookings (ticket_no,customer_name,customer_email,customer_phone,service_type,pax,event_date,event_time,total_amount,notes) VALUES (?,?,?,?,?,?,?,?,?,?)')
               ->execute(['TEMP',$name,$email,$phone,$service,$pax,$date,$time,$amount,$notes]);
            $id = $db->lastInsertId();
            $ticket = '#MP-' . str_pad($id + 299, 3, '0', STR_PAD_LEFT);
            $db->prepare('UPDATE bookings SET ticket_no=? WHERE id=?')->execute([$ticket, $id]);
            $db->commit();
            jsonOK(['id' => $id, 'ticket_no' => $ticket]);
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            jsonErr('Failed to add booking');
        }

    // ── UPDATE BOOKING STATUS ────────────────────────────────
    case 'booking_status':
        $id     = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $allowed = ['pending','confirmed','in_progress','completed','cancelled'];
        if (!in_array($status, $allowed)) jsonErr('Invalid status');
        
        $row = $db->prepare('SELECT service_type, venue_id, event_date, event_time, total_amount FROM bookings WHERE id=?');
        $row->execute([$id]);
        $booking = $row->fetch();
        if (!$booking) jsonErr('Booking not found');

        if (bookingStatusNeedsPrice($status) && (float)$booking['total_amount'] <= 0) {
            jsonErr('Set a booking price before confirming this reservation');
        }

        // Availability check when confirming a venue booking
        if ($booking['service_type'] === 'venue' && $booking['venue_id'] && $booking['event_date'] && in_array($status, ['confirmed', 'in_progress', 'completed'])) {
            $conflict = findVenueBookingConflict($db, (int)$booking['venue_id'], $booking['event_date'], normalizeBookingTime($booking['event_time'] ?? null), $id);
            if ($conflict) {
                jsonErr(buildVenueConflictMessage($conflict, $booking['event_date'], $booking['event_time'] ?? null), 409);
            }
        }

        $db->prepare('UPDATE bookings SET status=? WHERE id=?')->execute([$status, $id]);
        jsonOK(['message' => 'Status updated']);

    // ── REVIEW / UPDATE BOOKING ──────────────────────────────
    case 'booking_update':
        $id = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? 'pending';
        $amount = (float)($_POST['total_amount'] ?? 0);
        $discountPercent = (float)($_POST['discount_percent'] ?? 0);
        $pricingNotes = plainText($_POST['pricing_notes'] ?? '');
        $notes = plainText($_POST['notes'] ?? '');
        $allowed = ['pending','confirmed','in_progress','completed','cancelled'];
        if (!in_array($status, $allowed, true)) {
            jsonErr('Invalid status');
        }
        
        // Validate discount percentage
        if ($discountPercent < 0 || $discountPercent > 100) {
            jsonErr('Discount percentage must be between 0 and 100');
        }
        
        $row = $db->prepare('SELECT service_type, venue_id, event_date, event_time FROM bookings WHERE id=?');
        $row->execute([$id]);
        $booking = $row->fetch();
        if (!$booking) jsonErr('Booking not found');

        if (bookingStatusNeedsPrice($status) && $amount <= 0) {
            jsonErr('Please set the booking price before confirming');
        }

        // Availability check when confirming a venue booking
        if ($booking['service_type'] === 'venue' && $booking['venue_id'] && $booking['event_date'] && in_array($status, ['confirmed', 'in_progress', 'completed'])) {
            $conflict = findVenueBookingConflict($db, (int)$booking['venue_id'], $booking['event_date'], normalizeBookingTime($booking['event_time'] ?? null), $id);
            if ($conflict) {
                jsonErr(buildVenueConflictMessage($conflict, $booking['event_date'], $booking['event_time'] ?? null), 409);
            }
        }

        // Calculate final amount with discount
        $finalAmount = $amount * (1 - ($discountPercent / 100));

        $db->prepare('UPDATE bookings SET total_amount=?, discount_percent=?, final_amount=?, pricing_notes=?, notes=?, status=? WHERE id=?')
           ->execute([$amount, $discountPercent, $finalAmount, $pricingNotes ?: null, $notes, $status, $id]);
        jsonOK(['message' => 'Booking updated', 'final_amount' => $finalAmount, 'discount_percent' => $discountPercent]);

    // ── DELETE BOOKING ───────────────────────────────────────
    case 'booking_delete':
        $id = (int)($_POST['id'] ?? 0);
        $db->prepare('DELETE FROM bookings WHERE id=?')->execute([$id]);
        jsonOK(['message' => 'Booking deleted']);

    // ── MENU LIST / SEARCH ───────────────────────────────────
    case 'menu':
        $q    = '%' . trim($_GET['q'] ?? '') . '%';
        $cat  = $_GET['category'] ?? '';
        $sql  = 'SELECT * FROM menu_items WHERE (name LIKE ? OR description LIKE ?)';
        $p    = [$q, $q];
        if ($cat) { $sql .= ' AND category = ?'; $p[] = $cat; }
        $sql .= ' ORDER BY category, name';
        $stmt = $db->prepare($sql);
        $stmt->execute($p);
        jsonOK(['items' => $stmt->fetchAll()]);

    // ── ADD MENU ITEM ────────────────────────────────────────
    case 'menu_add':
        $name  = sanitize($_POST['name'] ?? '');
        $cat   = $_POST['category'] ?? '';
        $price = (float)($_POST['price'] ?? 0);
        $desc  = sanitize($_POST['description'] ?? '');
        $img   = sanitize($_POST['image_path'] ?? 'default-dish.jpg');
        if (!$name || !$cat) jsonErr('Name and category required');
        $db->prepare('INSERT INTO menu_items (name,category,price,description,image_path) VALUES (?,?,?,?,?)')->execute([$name,$cat,$price,$desc,$img]);
        jsonOK(['id' => $db->lastInsertId(), 'message' => 'Item added']);

    // ── UPDATE MENU ITEM ─────────────────────────────────────
    case 'menu_update':
        $id    = (int)($_POST['id'] ?? 0);
        $name  = sanitize($_POST['name'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $desc  = sanitize($_POST['description'] ?? '');
        $img   = sanitize($_POST['image_path'] ?? '');
        $avail = (int)($_POST['is_available'] ?? 1);

        // If name is '_', it's a toggle-only update from the dashboard
        if ($name === '_') {
            $db->prepare('UPDATE menu_items SET is_available=? WHERE id=?')->execute([$avail, $id]);
        } else {
            $sql = 'UPDATE menu_items SET name=?, price=?, description=?, is_available=?';
            $params = [$name, $price, $desc, $avail];
            if ($img) {
                $sql .= ', image_path=?';
                $params[] = $img;
            }
            $sql .= ' WHERE id=?';
            $params[] = $id;
            $db->prepare($sql)->execute($params);
        }
        jsonOK(['message' => 'Updated']);

    // ── DELETE MENU ITEM ─────────────────────────────────────
    case 'menu_delete':
        $id = (int)($_POST['id'] ?? 0);
        $db->prepare('DELETE FROM menu_items WHERE id=?')->execute([$id]);
        jsonOK(['message' => 'Deleted']);

    // ── VENUES LIST ──────────────────────────────────────────
    case 'venues':
        $rows = $db->query('SELECT * FROM venues ORDER BY rate')->fetchAll();
        jsonOK(['venues' => $rows]);

    // ── UPDATE VENUE AVAILABILITY ────────────────────────────
    case 'venue_toggle':
        $id   = (int)($_POST['id'] ?? 0);
        $avail= (int)($_POST['is_available'] ?? 1);
        $db->prepare('UPDATE venues SET is_available=? WHERE id=?')->execute([$avail, $id]);
        jsonOK(['message' => 'Updated']);

    // ── STAFF LIST / SEARCH ──────────────────────────────────
    case 'staff':
        $q    = '%' . trim($_GET['q'] ?? '') . '%';
        $stmt = $db->prepare('SELECT * FROM staff WHERE name LIKE ? OR role LIKE ? OR assignment LIKE ? ORDER BY role');
        $stmt->execute([$q, $q, $q]);
        jsonOK(['staff' => $stmt->fetchAll()]);

    // ── ADD STAFF ────────────────────────────────────────────
    case 'staff_add':
        $name   = sanitize($_POST['name'] ?? '');
        $role   = sanitize($_POST['role'] ?? '');
        $assign = sanitize($_POST['assignment'] ?? '');
        $s_start= $_POST['shift_start'] ?? '08:00:00';
        $s_end  = $_POST['shift_end']   ?? '17:00:00';
        $status = $_POST['status']      ?? 'on_duty';
        if (!$name || !$role) jsonErr('Name and role required');
        $db->prepare('INSERT INTO staff (name,role,assignment,shift_start,shift_end,status) VALUES (?,?,?,?,?,?)')->execute([$name,$role,$assign,$s_start,$s_end,$status]);
        jsonOK(['id' => $db->lastInsertId()]);

    // ── UPDATE STAFF STATUS ──────────────────────────────────
    case 'staff_status':
        $id     = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? 'on_duty';
        $db->prepare('UPDATE staff SET status=? WHERE id=?')->execute([$status, $id]);
        jsonOK(['message' => 'Updated']);

    // ── DELETE STAFF ─────────────────────────────────────────
    case 'staff_delete':
        $id = (int)($_POST['id'] ?? 0);
        $db->prepare('DELETE FROM staff WHERE id=?')->execute([$id]);
        jsonOK(['message' => 'Deleted']);

    // ── REPORTS ──────────────────────────────────────────────
    case 'reports':
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-d');
        $periodDays = parsePeriodDays($startDate, $endDate);
        $revenueExpr = bookingRevenueExpression();
        $staffCosts = staffCostBreakdown($db, $periodDays);
        
        // Current month revenue
        $revenue_month = $db->query("SELECT COALESCE(SUM(CASE WHEN final_amount > 0 THEN final_amount ELSE total_amount END),0) FROM bookings WHERE status='completed' AND MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())")->fetchColumn();
        
        // Revenue by service
        $by_service = $db->query("SELECT service_type, COUNT(*) AS cnt, COALESCE(SUM(CASE WHEN final_amount > 0 THEN final_amount ELSE total_amount END),0) AS total FROM bookings WHERE status='completed' GROUP BY service_type")->fetchAll();
        
        // Top dishes
        $top_dishes = $db->query("SELECT m.name, SUM(bi.quantity) AS qty FROM booking_items bi JOIN menu_items m ON bi.menu_item_id=m.id GROUP BY m.name ORDER BY qty DESC LIMIT 5")->fetchAll();
        
        // Status counts
        $status_counts = $db->query("SELECT status, COUNT(*) AS cnt FROM bookings GROUP BY status")->fetchAll();
        
        // Recent bookings
        $recent = array_map(
            static fn(array $booking): array => decodeEntityFields($booking, ['customer_name']),
            $db->query("SELECT ticket_no,customer_name,service_type,CASE WHEN final_amount > 0 THEN final_amount ELSE total_amount END AS total_amount,status,created_at FROM bookings ORDER BY created_at DESC LIMIT 8")->fetchAll()
        );
        
        // Revenue Trend (Last 7 days)
        $daily_revenue = $db->query("
            SELECT DATE(created_at) as date, COALESCE(SUM(CASE WHEN final_amount > 0 THEN final_amount ELSE total_amount END), 0) as total 
            FROM bookings 
            WHERE status='completed' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at) ASC
        ")->fetchAll();

        // Revenue Trend (Last 6 months)
        $monthly_trends = $db->query("
            SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COALESCE(SUM(CASE WHEN final_amount > 0 THEN final_amount ELSE total_amount END), 0) as total 
            FROM bookings 
            WHERE status='completed' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ")->fetchAll();

        $costStmt = $db->prepare("
            SELECT 
                b.id,
                b.service_type,
                {$revenueExpr} AS recognized_revenue,
                COALESCE(menu_totals.menu_subtotal, 0) AS menu_subtotal,
                COALESCE(addon_totals.addon_subtotal, 0) AS addon_subtotal
            FROM bookings b
            LEFT JOIN (
                SELECT booking_id, SUM(quantity * unit_price) AS menu_subtotal
                FROM booking_items
                GROUP BY booking_id
            ) menu_totals ON menu_totals.booking_id = b.id
            LEFT JOIN (
                SELECT booking_id, SUM(quantity * unit_price) AS addon_subtotal
                FROM booking_addons
                GROUP BY booking_id
            ) addon_totals ON addon_totals.booking_id = b.id
            WHERE b.status='completed' AND DATE(b.created_at) BETWEEN ? AND ?
        ");
        $costStmt->execute([$startDate, $endDate]);
        $completedBookings = $costStmt->fetchAll();
        $estimatedFoodCost = 0.0;
        foreach ($completedBookings as $booking) {
            $estimatedFoodCost += estimateBookingFoodCost($booking);
        }
        $estimatedFoodCost = toFloat($estimatedFoodCost);
        $netProfit = toFloat((float)$revenue_month - $estimatedFoodCost - $staffCosts['period_total']);
        $expensePie = buildPieDataset([
            ['label' => 'Recipe / Food Cost', 'value' => $estimatedFoodCost, 'color' => '#d97706'],
            ['label' => 'Staff Labor', 'value' => $staffCosts['period_total'], 'color' => '#2563eb'],
            ['label' => 'Net Profit', 'value' => max(0, $netProfit), 'color' => '#168a24'],
        ]);
        $servicePie = buildPieDataset(array_map(static function (array $service): array {
            $colors = [
                'restaurant' => '#168a24',
                'venue' => '#b45309',
                'catering' => '#2563eb',
                'cafe' => '#9333ea',
            ];
            return [
                'label' => ucfirst((string)$service['service_type']),
                'value' => (float)$service['total'],
                'color' => $colors[$service['service_type']] ?? '#6b7280',
            ];
        }, $by_service));

        jsonOK([
            'revenue_month' => (float)$revenue_month,
            'net_profit_month' => $netProfit,
            'by_service' => $by_service,
            'top_dishes' => $top_dishes,
            'status_counts' => $status_counts,
            'recent' => $recent,
            'daily_revenue' => $daily_revenue,
            'monthly_trends' => $monthly_trends,
            'financial_summary' => [
                'gross_revenue' => (float)$revenue_month,
                'staff_labor_cost' => $staffCosts['period_total'],
                'food_cost' => $estimatedFoodCost,
                'net_profit' => $netProfit,
                'profit_margin_percent' => (float)$revenue_month > 0 ? toFloat(($netProfit / (float)$revenue_month) * 100) : 0.0,
                'avg_daily_staff_cost' => $staffCosts['daily_total'],
                'period_days' => $periodDays,
                'working_days' => $staffCosts['working_days'],
                'cost_model' => [
                    'labor' => 'Labor uses 4 workers at P500 each per day, scheduled 6 days per week.',
                    'food' => 'Estimated from booked menu/add-on values when available, with service-based fallback recipe cost ratios.',
                ],
            ],
            'charts' => [
                'expense_breakdown' => $expensePie,
                'service_revenue' => $servicePie,
            ],
            'staff_costs' => $staffCosts['staff'],
        ]);

    // ── SALES REPORT (with discounts) ──────────────────────
    case 'sales_report':
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $periodDays = parsePeriodDays($startDate, $endDate);
        $staffCosts = staffCostBreakdown($db, $periodDays);
        
        $sql = "SELECT 
                    b.id, 
                    b.ticket_no, 
                    b.customer_name, 
                    b.service_type,
                    b.event_date, 
                    b.total_amount,
                    b.discount_percent,
                    b.final_amount,
                    b.status,
                    b.created_at,
                    COALESCE(menu_totals.menu_subtotal, 0) AS menu_subtotal,
                    COALESCE(addon_totals.addon_subtotal, 0) AS addon_subtotal,
                    COALESCE(menu_totals.item_count, 0) as item_count,
                    CASE WHEN b.final_amount > 0 THEN b.final_amount ELSE b.total_amount END AS recognized_revenue
                FROM bookings b
                LEFT JOIN (
                    SELECT booking_id, SUM(quantity * unit_price) AS menu_subtotal, COUNT(*) AS item_count
                    FROM booking_items
                    GROUP BY booking_id
                ) menu_totals ON menu_totals.booking_id = b.id
                LEFT JOIN (
                    SELECT booking_id, SUM(quantity * unit_price) AS addon_subtotal
                    FROM booking_addons
                    GROUP BY booking_id
                ) addon_totals ON addon_totals.booking_id = b.id
                WHERE b.status = 'completed' AND DATE(b.created_at) BETWEEN ? AND ?
                ORDER BY b.created_at DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $bookings = array_map(
            static fn(array $booking): array => decodeEntityFields($booking, ['customer_name']),
            $stmt->fetchAll()
        );
        
        // Calculate summary
        $totalRevenue = 0;
        $totalDiscount = 0;
        $finalRevenue = 0;
        $estimatedFoodCost = 0.0;
        
        foreach ($bookings as $booking) {
            $totalRevenue += (float)$booking['total_amount'];
            $discount = (float)$booking['total_amount'] * ((float)$booking['discount_percent'] / 100);
            $totalDiscount += $discount;
            $finalRevenue += (float)$booking['recognized_revenue'];
            $estimatedFoodCost += estimateBookingFoodCost($booking);
        }
        $estimatedFoodCost = toFloat($estimatedFoodCost);
        $netProfit = toFloat($finalRevenue - $estimatedFoodCost - $staffCosts['period_total']);
        
        jsonOK([
            'bookings' => $bookings,
            'summary' => [
                'total_bookings' => count($bookings),
                'total_revenue' => toFloat($totalRevenue),
                'total_discount' => toFloat($totalDiscount),
                'final_revenue' => toFloat($finalRevenue),
                'food_cost' => $estimatedFoodCost,
                'staff_labor_cost' => $staffCosts['period_total'],
                'net_profit' => $netProfit,
                'profit_margin_percent' => $finalRevenue > 0 ? toFloat(($netProfit / $finalRevenue) * 100) : 0.0,
                'average_discount_percent' => count($bookings) > 0 ? array_sum(array_map(fn($b) => $b['discount_percent'], $bookings)) / count($bookings) : 0
            ],
            'charts' => [
                'revenue_breakdown' => buildPieDataset([
                    ['label' => 'Recipe / Food Cost', 'value' => $estimatedFoodCost, 'color' => '#d97706'],
                    ['label' => 'Staff Labor', 'value' => $staffCosts['period_total'], 'color' => '#2563eb'],
                    ['label' => 'Discounts Given', 'value' => $totalDiscount, 'color' => '#dc2626'],
                    ['label' => 'Net Profit', 'value' => max(0, $netProfit), 'color' => '#168a24'],
                ]),
            ],
            'assumptions' => [
                'labor' => 'Labor uses 4 workers at P500 each per day, scheduled 6 days per week.',
                'food' => 'Recipe cost is estimated from menu/add-on totals when priced, otherwise by service-type food cost ratio.',
            ],
        ]);

    default:
        jsonErr('Unknown action', 404);
}
