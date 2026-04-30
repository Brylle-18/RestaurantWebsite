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
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{
  --green:#168a24;--green-dk:#0a5616;--red:#b61217;
  --bg:#f4fbe9;--card:#fff;--text:#12311a;--muted:#58705e;
  --border:rgba(18,98,33,.14);--shadow:0 24px 60px rgba(10,50,18,.14);
}
body{
  font-family:'DM Sans',sans-serif;background:var(--bg);
  min-height:100vh;display:grid;place-items:center;
  background:radial-gradient(circle at 30% 20%,rgba(129,214,123,.28) 0,transparent 45%),
             radial-gradient(circle at 80% 80%,rgba(182,18,23,.10) 0,transparent 35%),
             linear-gradient(150deg,#f4fbe9,#eef7e5 60%,#fcfef7);
}
.login-wrap{width:100%;max-width:440px;padding:20px;}
.brand{text-align:center;margin-bottom:36px;}
.brand-badge{
  display:inline-flex;align-items:center;gap:10px;
  background:var(--green-dk);color:#fff;padding:10px 20px;
  border-radius:999px;font-size:12px;letter-spacing:.1em;
  text-transform:uppercase;font-weight:700;margin-bottom:16px;
}
.brand h1{font-family:'Playfair Display',serif;font-size:42px;color:var(--text);line-height:1;}
.brand p{color:var(--muted);margin-top:6px;font-size:14px;}
.card{
  background:var(--card);border-radius:28px;padding:36px;
  box-shadow:var(--shadow);border:1px solid rgba(255,255,255,.8);
}
.card h2{font-family:'Playfair Display',serif;font-size:26px;margin-bottom:6px;}
.card .sub{color:var(--muted);font-size:13px;margin-bottom:28px;}
.field{margin-bottom:18px;}
.field label{display:block;font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--muted);margin-bottom:8px;}
.field input{
  width:100%;padding:14px 16px;border:2px solid var(--border);border-radius:16px;
  font-family:'DM Sans',sans-serif;font-size:15px;color:var(--text);
  background:#fafff7;transition:.2s;outline:none;
}
.field input:focus{border-color:var(--green);background:#fff;box-shadow:0 0 0 4px rgba(22,138,36,.10);}
.btn-login{
  width:100%;padding:16px;border:none;border-radius:16px;cursor:pointer;
  background:linear-gradient(135deg,var(--green),var(--green-dk));color:#fff;
  font-family:'DM Sans',sans-serif;font-size:16px;font-weight:700;
  box-shadow:0 12px 28px rgba(10,86,22,.25);transition:.25s;margin-top:6px;
}
.btn-login:hover{transform:translateY(-2px);box-shadow:0 18px 36px rgba(10,86,22,.30);}
.error{
  background:rgba(182,18,23,.08);border:1px solid rgba(182,18,23,.18);
  color:var(--red);border-radius:14px;padding:12px 16px;font-size:13px;
  font-weight:600;margin-bottom:20px;
}
.back-link{text-align:center;margin-top:20px;font-size:13px;color:var(--muted);}
.back-link a{color:var(--green);font-weight:700;text-decoration:none;}
</style>
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
