<?php
// customer/api.php — Public JSON API (no auth required)
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$db     = getDB();
$action = $_REQUEST['action'] ?? '';

function jsonOK(array $data): void { echo json_encode(['ok' => true] + $data); exit; }
function jsonErr(string $msg, int $code = 400): void {
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $msg]); exit;
}
function sanitize(string $s): string { return htmlspecialchars(trim($s), ENT_QUOTES); }

switch ($action) {

    // ── CUSTOMER REGISTRATION ───────────────────────────────
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonErr('POST required');
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        $result = customerRegister($email, $password, $fullName, $phone, $address);
        
        if ($result['success']) {
            jsonOK(['message' => $result['message']]);
        } else {
            jsonErr($result['error']);
        }

    // ── CUSTOMER LOGIN ──────────────────────────────────────
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonErr('POST required');
        
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (customerLogin($email, $password)) {
            jsonOK(['message' => 'Login successful', 'customer_id' => $_SESSION['customer_id']]);
        } else {
            jsonErr('Invalid email or password', 401);
        }

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
        $rows = $db->query('SELECT id,name,type,capacity,rate,description,is_available FROM venues ORDER BY rate')->fetchAll();
        jsonOK(['venues' => $rows]);

    // ── CHECK BOOKING STATUS ─────────────────────────────────
    case 'track':
        $ticket = trim($_GET['ticket'] ?? '');
        if (!$ticket) jsonErr('Ticket number required');
        $stmt = $db->prepare('SELECT ticket_no,customer_name,service_type,event_date,pax,total_amount,status,notes,created_at FROM bookings WHERE ticket_no = ? LIMIT 1');
        $stmt->execute([$ticket]);
        $booking = $stmt->fetch();
        if (!$booking) jsonErr('Booking not found', 404);
        jsonOK(['booking' => $booking]);

    // ── SUBMIT BOOKING INQUIRY ───────────────────────────────
    case 'inquire':
        $name    = sanitize($_POST['customer_name'] ?? '');
        $email   = sanitize($_POST['customer_email'] ?? '');
        $phone   = sanitize($_POST['customer_phone'] ?? '');
        $service = $_POST['service_type'] ?? 'restaurant';
        $pax     = (int)($_POST['pax'] ?? 1);
        $date    = $_POST['event_date'] ?: null;
        $notes   = sanitize($_POST['notes'] ?? '');

        if (!$name || !$phone) jsonErr('Name and phone number are required');
        if (!in_array($service, ['restaurant','catering','cafe','venue'])) jsonErr('Invalid service type');

        // Auto-generate ticket number
        $last   = $db->query("SELECT id FROM bookings ORDER BY id DESC LIMIT 1")->fetchColumn();
        $ticket = '#MP-' . str_pad(($last ? $last + 1 : 300), 3, '0', STR_PAD_LEFT);

        // Venue amount auto-fill
        $amount = 0.00;
        if ($service === 'venue') {
            $venue_id = (int)($_POST['venue_id'] ?? 0);
            if ($venue_id) {
                $v = $db->prepare('SELECT rate FROM venues WHERE id=?');
                $v->execute([$venue_id]);
                $vr = $v->fetch();
                if ($vr) $amount = $vr['rate'];
            }
        }

        $db->prepare('INSERT INTO bookings (ticket_no,customer_name,customer_email,customer_phone,service_type,venue_id,pax,event_date,total_amount,notes,status) VALUES (?,?,?,?,?,?,?,?,?,?,?)')
           ->execute([$ticket,$name,$email,$phone,$service,$venue_id??null,$pax,$date,$amount,$notes,'pending']);

        jsonOK(['ticket_no' => $ticket, 'message' => 'Inquiry submitted successfully! Save your ticket number.']);

    default:
        jsonErr('Unknown action', 404);
}
