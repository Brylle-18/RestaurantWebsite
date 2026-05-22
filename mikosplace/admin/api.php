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
function bookingStatusNeedsPrice(string $status): bool {
    return in_array($status, ['confirmed', 'in_progress', 'completed'], true);
}

// ── router ──────────────────────────────────────────────────
switch ($action) {

    // ── DASHBOARD STATS ─────────────────────────────────────
    case 'stats':
        $bookings  = $db->query('SELECT COUNT(*) FROM bookings WHERE WEEK(created_at)=WEEK(NOW())')->fetchColumn();
        $pending   = $db->query("SELECT COUNT(*) FROM bookings WHERE status='pending'")->fetchColumn();
        $revenue   = $db->query("SELECT COALESCE(SUM(total_amount),0) FROM bookings WHERE status='completed' AND MONTH(created_at)=MONTH(NOW())")->fetchColumn();
        $dishes    = $db->query('SELECT COUNT(*) FROM menu_items WHERE is_available=1')->fetchColumn();
        jsonOK(['weekly_bookings'=>$bookings,'pending'=>$pending,'monthly_revenue'=>$revenue,'active_dishes'=>$dishes]);

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
        jsonOK(['bookings' => $rows->fetchAll(), 'page' => $page, 'total_pages' => $totalPages, 'total_count' => $totalCount]);

    // ── SINGLE BOOKING ───────────────────────────────────────
    case 'booking_get':
        $id  = (int)($_GET['id'] ?? 0);
        $row = $db->prepare('SELECT b.*, v.name AS venue_name FROM bookings b LEFT JOIN venues v ON b.venue_id=v.id WHERE b.id=?');
        $row->execute([$id]);
        $booking = $row->fetch();
        if (!$booking) jsonErr('Not found', 404);
        $items = $db->prepare('SELECT bi.*, m.name AS dish FROM booking_items bi JOIN menu_items m ON bi.menu_item_id=m.id WHERE bi.booking_id=?');
        $items->execute([$id]);
        $addons = $db->prepare('SELECT * FROM booking_addons WHERE booking_id=? ORDER BY addon_type, addon_name');
        $addons->execute([$id]);
        jsonOK(['booking' => $booking, 'items' => $items->fetchAll(), 'addons' => $addons->fetchAll()]);

    // ── ADD BOOKING ──────────────────────────────────────────
    case 'booking_add':
        $name    = sanitize($_POST['customer_name'] ?? '');
        $email   = sanitize($_POST['customer_email'] ?? '');
        $phone   = sanitize($_POST['customer_phone'] ?? '');
        $service = $_POST['service_type'] ?? 'restaurant';
        $pax     = (int)($_POST['pax'] ?? 1);
        $date    = $_POST['event_date'] ?: null;
        $time    = normalizeBookingTime($_POST['event_time'] ?? null);
        $amount  = (float)($_POST['total_amount'] ?? 0);
        $notes   = sanitize($_POST['notes'] ?? '');
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
        $pricingNotes = sanitize($_POST['pricing_notes'] ?? '');
        $notes = sanitize($_POST['notes'] ?? '');
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
        // Current month revenue
        $revenue_month = $db->query("SELECT COALESCE(SUM(total_amount),0) FROM bookings WHERE status='completed' AND MONTH(created_at)=MONTH(NOW()) AND YEAR(created_at)=YEAR(NOW())")->fetchColumn();
        
        // Revenue by service
        $by_service = $db->query("SELECT service_type, COUNT(*) AS cnt, COALESCE(SUM(total_amount),0) AS total FROM bookings GROUP BY service_type")->fetchAll();
        
        // Top dishes
        $top_dishes = $db->query("SELECT m.name, SUM(bi.quantity) AS qty FROM booking_items bi JOIN menu_items m ON bi.menu_item_id=m.id GROUP BY m.name ORDER BY qty DESC LIMIT 5")->fetchAll();
        
        // Status counts
        $status_counts = $db->query("SELECT status, COUNT(*) AS cnt FROM bookings GROUP BY status")->fetchAll();
        
        // Recent bookings
        $recent = $db->query("SELECT ticket_no,customer_name,service_type,total_amount,status,created_at FROM bookings ORDER BY created_at DESC LIMIT 8")->fetchAll();
        
        // Revenue Trend (Last 7 days)
        $daily_revenue = $db->query("
            SELECT DATE(created_at) as date, COALESCE(SUM(total_amount), 0) as total 
            FROM bookings 
            WHERE status='completed' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            GROUP BY DATE(created_at)
            ORDER BY DATE(created_at) ASC
        ")->fetchAll();

        // Revenue Trend (Last 6 months)
        $monthly_trends = $db->query("
            SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COALESCE(SUM(total_amount), 0) as total 
            FROM bookings 
            WHERE status='completed' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH)
            GROUP BY DATE_FORMAT(created_at, '%Y-%m')
            ORDER BY month ASC
        ")->fetchAll();

        jsonOK([
            'revenue_month' => (float)$revenue_month,
            'by_service' => $by_service,
            'top_dishes' => $top_dishes,
            'status_counts' => $status_counts,
            'recent' => $recent,
            'daily_revenue' => $daily_revenue,
            'monthly_trends' => $monthly_trends
        ]);

    // ── SALES REPORT (with discounts) ──────────────────────
    case 'sales_report':
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        $sql = "SELECT 
                    b.id, 
                    b.ticket_no, 
                    b.customer_name, 
                    b.event_date, 
                    b.total_amount,
                    b.discount_percent,
                    b.final_amount,
                    b.status,
                    b.created_at,
                    COUNT(bi.id) as item_count
                FROM bookings b
                LEFT JOIN booking_items bi ON b.id = bi.booking_id
                WHERE b.status = 'completed' AND DATE(b.created_at) BETWEEN ? AND ?
                GROUP BY b.id
                ORDER BY b.created_at DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$startDate, $endDate]);
        $bookings = $stmt->fetchAll();
        
        // Calculate summary
        $totalRevenue = 0;
        $totalDiscount = 0;
        $finalRevenue = 0;
        
        foreach ($bookings as $booking) {
            $totalRevenue += (float)$booking['total_amount'];
            $discount = (float)$booking['total_amount'] * ((float)$booking['discount_percent'] / 100);
            $totalDiscount += $discount;
            $finalRevenue += (float)$booking['final_amount'];
        }
        
        jsonOK([
            'bookings' => $bookings,
            'summary' => [
                'total_bookings' => count($bookings),
                'total_revenue' => $totalRevenue,
                'total_discount' => $totalDiscount,
                'final_revenue' => $finalRevenue,
                'average_discount_percent' => count($bookings) > 0 ? array_sum(array_map(fn($b) => $b['discount_percent'], $bookings)) / count($bookings) : 0
            ]
        ]);

    default:
        jsonErr('Unknown action', 404);
}
