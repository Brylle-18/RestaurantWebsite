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

// ============================================================
// CUSTOMER FUNCTIONS
// ============================================================

function requireCustomer(): void {
    if (empty($_SESSION['customer_id'])) {
        header('Location: /mikosplace/customer/login.php');
        exit;
    }
}

function customerRegister(string $email, string $password, string $fullName, string $phone = '', string $address = ''): array {
    // Validate inputs
    if (empty($email) || empty($password) || empty($fullName)) {
        return ['success' => false, 'error' => 'Email, password, and name are required.'];
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Invalid email format.'];
    }

    if (strlen($password) < 6) {
        return ['success' => false, 'error' => 'Password must be at least 6 characters.'];
    }

    $db = getDB();

    // Check if email already exists
    $stmt = $db->prepare('SELECT id FROM customers WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'Email is already registered.'];
    }

    // Hash password and insert
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    try {
        $stmt = $db->prepare(
            'INSERT INTO customers (email, password, full_name, phone, address) 
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$email, $hashedPassword, $fullName, $phone, $address]);
        return ['success' => true, 'message' => 'Registration successful!'];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => 'Registration failed. Please try again.'];
    }
}

function customerLogin(string $email, string $password): bool {
    $db   = getDB();
    $stmt = $db->prepare('SELECT id, password, full_name FROM customers WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['customer_id']   = $user['id'];
        $_SESSION['customer_name'] = $user['full_name'];
        $_SESSION['customer_email'] = $email;
        return true;
    }
    return false;
}

function customerLogout(): void {
    session_destroy();
    header('Location: /mikosplace/customer/login.php');
    exit;
}

