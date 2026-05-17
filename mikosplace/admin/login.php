<?php
// admin/login.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Already logged in? Redirect
if (!empty($_SESSION['admin_id'])) {
    header('Location: /mikosplace/admin/index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    requireCsrf();
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';
    if (adminLogin($user, $pass)) {
        header('Location: /mikosplace/admin/index.php');
        exit;
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — Miko's Place</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/mikosplace/assets/login-styles.css">
</head>
<body>
<div class="login-wrap">
  <div class="brand">
    <div class="brand-badge">🍽 Admin Portal</div>
    <h1>Miko's Place</h1>
    <p>Seafoods, Grill and Catering Services</p>
  </div>
  <div class="card">
    <h2>Welcome back</h2>
    <p class="sub">Sign in to your admin dashboard</p>
    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(getCsrfToken(), ENT_QUOTES) ?>">
      <div class="field">
        <label>Username</label>
        <input type="text" name="username" placeholder="Enter username" required autocomplete="username">
      </div>
      <div class="field">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter password" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn-login">Sign In →</button>
    </form>
  </div>
  <p class="back-link"><a href="/mikosplace/customer/index.php">← Back to customer page</a></p>
</div>
</body>
</html>
