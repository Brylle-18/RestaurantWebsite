<?php
// includes/auth.php — session-based authentication for admin and customers

session_start();

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

