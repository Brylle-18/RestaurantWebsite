<?php
// includes/auth.php — session-based authentication for admin and customers

// Secure session settings
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure'   => isset($_SERVER['HTTPS']),
        'cookie_samesite' => 'Lax',
    ]);
}

// ============================================================
// CSRF PROTECTION
// ============================================================

function getCsrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCsrfToken(?string $token): bool {
    return !empty($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function requireCsrf(): void {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    if (!validateCsrfToken($token)) {
        http_response_code(403);
        die(json_encode(['ok' => false, 'error' => 'Invalid CSRF token']));
    }
}

// ============================================================
// ADMIN FUNCTIONS
// ============================================================

function requireAdmin(): void {
    if (empty($_SESSION['admin_id'])) {
        header('Location: /mikosplace/admin/login.php');
        exit;
    }
}

function adminLogin(string $username, string $password): bool {
    $db   = getDB();
    $stmt = $db->prepare('SELECT id, password, full_name FROM admin_users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id']   = $user['id'];
        $_SESSION['admin_name'] = $user['full_name'];
        return true;
    }
    return false;
}

function adminLogout(): void {
    session_destroy();
    header('Location: /mikosplace/admin/login.php');
    exit;
}

