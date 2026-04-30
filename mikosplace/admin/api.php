<?php
// admin/api.php — JSON API for admin dashboard actions
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$db     = getDB();
$action = $_REQUEST['action'] ?? '';

// ── helper ──────────────────────────────────────────────────
function jsonOK(array $data): void { echo json_encode(['ok' => true] + $data); exit; }
function jsonErr(string $msg, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg]); exit;
}
function sanitize(string $s): string { return htmlspecialchars(trim($s), ENT_QUOTES); }

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
        $sql    = 'SELECT * FROM bookings WHERE (ticket_no LIKE ? OR customer_name LIKE ? OR customer_email LIKE ?)';
        $params = [$q, $q, $q];
        if ($status) { $sql .= ' AND status = ?'; $params[] = $status; }
        $sql .= ' ORDER BY created_at DESC LIMIT 100';
        $rows = $db->prepare($sql);
        $rows->execute($params);
        jsonOK(['bookings' => $rows->fetchAll()]);

    // ── SINGLE BOOKING ───────────────────────────────────────
    case 'booking_get':
        $id  = (int)($_GET['id'] ?? 0);
        $row = $db->prepare('SELECT b.*, v.name AS venue_name FROM bookings b LEFT JOIN venues v ON b.venue_id=v.id WHERE b.id=?');
        $row->execute([$id]);
        $booking = $row->fetch();
        if (!$booking) jsonErr('Not found', 404);
        $items = $db->prepare('SELECT bi.*, m.name AS dish FROM booking_items bi JOIN menu_items m ON bi.menu_item_id=m.id WHERE bi.booking_id=?');
        $items->execute([$id]);
        jsonOK(['booking' => $booking, 'items' => $items->fetchAll()]);

    // ── ADD BOOKING ──────────────────────────────────────────
    case 'booking_add':
        $name    = sanitize($_POST['customer_name'] ?? '');
        $email   = sanitize($_POST['customer_email'] ?? '');
        $phone   = sanitize($_POST['customer_phone'] ?? '');
        $service = $_POST['service_type'] ?? 'restaurant';
        $pax     = (int)($_POST['pax'] ?? 1);
        $date    = $_POST['event_date'] ?: null;
        $amount  = (float)($_POST['total_amount'] ?? 0);
        $notes   = sanitize($_POST['notes'] ?? '');
        if (!$name) jsonErr('Customer name required');
        // Generate ticket number
        $last = $db->query("SELECT id FROM bookings ORDER BY id DESC LIMIT 1")->fetchColumn();
        $ticket = '#MP-' . str_pad(($last ? $last + 1 : 300), 3, '0', STR_PAD_LEFT);
        $db->prepare('INSERT INTO bookings (ticket_no,customer_name,customer_email,customer_phone,service_type,pax,event_date,total_amount,notes) VALUES (?,?,?,?,?,?,?,?,?)')
           ->execute([$ticket,$name,$email,$phone,$service,$pax,$date,$amount,$notes]);
        jsonOK(['id' => $db->lastInsertId(), 'ticket_no' => $ticket]);

    // ── UPDATE BOOKING STATUS ────────────────────────────────
    case 'booking_status':
        $id     = (int)($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $allowed = ['pending','confirmed','in_progress','completed','cancelled'];
        if (!in_array($status, $allowed)) jsonErr('Invalid status');
        $db->prepare('UPDATE bookings SET status=? WHERE id=?')->execute([$status, $id]);
        jsonOK(['message' => 'Status updated']);

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
        if (!$name || !$cat) jsonErr('Name and category required');
        $db->prepare('INSERT INTO menu_items (name,category,price,description) VALUES (?,?,?,?)')->execute([$name,$cat,$price,$desc]);
        jsonOK(['id' => $db->lastInsertId(), 'message' => 'Item added']);

    // ── UPDATE MENU ITEM ─────────────────────────────────────
    case 'menu_update':
        $id    = (int)($_POST['id'] ?? 0);
        $name  = sanitize($_POST['name'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $desc  = sanitize($_POST['description'] ?? '');
        $avail = (int)($_POST['is_available'] ?? 1);
        $db->prepare('UPDATE menu_items SET name=?,price=?,description=?,is_available=? WHERE id=?')->execute([$name,$price,$desc,$avail,$id]);
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
        $revenue_month = $db->query("SELECT COALESCE(SUM(total_amount),0) FROM bookings WHERE status='completed' AND MONTH(created_at)=MONTH(NOW())")->fetchColumn();
        $by_service    = $db->query("SELECT service_type, COUNT(*) AS cnt, COALESCE(SUM(total_amount),0) AS total FROM bookings GROUP BY service_type")->fetchAll();
        $top_dishes    = $db->query("SELECT m.name, SUM(bi.quantity) AS qty FROM booking_items bi JOIN menu_items m ON bi.menu_item_id=m.id GROUP BY m.name ORDER BY qty DESC LIMIT 5")->fetchAll();
        $status_counts = $db->query("SELECT status, COUNT(*) AS cnt FROM bookings GROUP BY status")->fetchAll();
        $recent        = $db->query("SELECT ticket_no,customer_name,service_type,total_amount,status,created_at FROM bookings ORDER BY created_at DESC LIMIT 8")->fetchAll();
        jsonOK(['revenue_month'=>$revenue_month,'by_service'=>$by_service,'top_dishes'=>$top_dishes,'status_counts'=>$status_counts,'recent'=>$recent]);

    default:
        jsonErr('Unknown action', 404);
}
