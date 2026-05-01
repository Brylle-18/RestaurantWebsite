<?php
// customer/register.php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

// Already logged in? Redirect
if (!empty($_SESSION['customer_id'])) {
    header('Location: /mikosplace/customer/index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';    
    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    // Validate passwords match
    if ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        // Attempt registration
        $result = customerRegister($email, $password, $fullName, $phone, $address);
        
        if ($result['success']) {
            $success = $result['message'];
            // Clear form
            $email = $fullName = $phone = $address = '';
            // Redirect to login after 2 seconds
            echo '<meta http-equiv="refresh" content="2; url=/mikosplace/customer/login.php">';
        } else {
            $error = $result['error'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account — Miko's Place</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
:root{
  --green:#168a24;--green-dk:#0a5616;--red:#b61217;--blue:#1e40af;
  --bg:#f4fbe9;--card:#fff;--text:#12311a;--muted:#58705e;
  --border:rgba(18,98,33,.14);--shadow:0 24px 60px rgba(10,50,18,.14);
}
body{
  font-family:'DM Sans',sans-serif;background:var(--bg);
  min-height:100vh;display:grid;place-items:center;padding:20px;
  background:radial-gradient(circle at 30% 20%,rgba(129,214,123,.28) 0,transparent 45%),
             radial-gradient(circle at 80% 80%,rgba(182,18,23,.10) 0,transparent 35%),
             linear-gradient(150deg,#f4fbe9,#eef7e5 60%,#fcfef7);
}
.register-wrap{width:100%;max-width:500px;}
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
.field input,.field textarea{
  width:100%;padding:14px 16px;border:2px solid var(--border);border-radius:16px;
  font-family:'DM Sans',sans-serif;font-size:15px;color:var(--text);
  background:#fafff7;transition:.2s;outline:none;resize:vertical;
}
.field input:focus,.field textarea:focus{border-color:var(--green);background:#fff;box-shadow:0 0 0 4px rgba(22,138,36,.10);}
.field-group{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.field-group .field{margin-bottom:0;}
@media (max-width:600px) {
  .field-group{grid-template-columns:1fr;}
}
.btn-register{
  width:100%;padding:16px;border:none;border-radius:16px;cursor:pointer;
  background:linear-gradient(135deg,var(--green),var(--green-dk));color:#fff;
  font-family:'DM Sans',sans-serif;font-size:16px;font-weight:700;
  box-shadow:0 12px 28px rgba(10,86,22,.25);transition:.25s;margin-top:6px;
}
.btn-register:hover{transform:translateY(-2px);box-shadow:0 18px 36px rgba(10,86,22,.30);}
.btn-register:active{transform:translateY(0);}
.error{
  background:rgba(182,18,23,.08);border:1px solid rgba(182,18,23,.18);
  color:var(--red);border-radius:14px;padding:12px 16px;font-size:13px;
  font-weight:600;margin-bottom:20px;
}
.success{
  background:rgba(22,138,36,.08);border:1px solid rgba(22,138,36,.18);
  color:var(--green-dk);border-radius:14px;padding:12px 16px;font-size:13px;
  font-weight:600;margin-bottom:20px;
}
.login-link{text-align:center;margin-top:20px;font-size:13px;color:var(--muted);}
.login-link a{color:var(--green);font-weight:700;text-decoration:none;}
.terms{font-size:12px;color:var(--muted);line-height:1.6;margin-top:20px;padding-top:20px;border-top:1px solid var(--border);}
</style>
</head>
<body>
<div class="register-wrap">
  <div class="brand">
    <div class="brand-badge">🍽 Customer Portal</div>
    <h1>Miko's Place</h1>
    <p>Seafoods, Grill and Catering Services</p>
  </div>
  <div class="card">
    <h2>Create Account</h2>
    <p class="sub">Join us and start booking your next event</p>

    <?php if ($error): ?>
      <div class="error">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="success">✓ <?= htmlspecialchars($success) ?> Redirecting to login...</div>
    <?php endif; ?>

    <form method="POST">
      <div class="field">
        <label>Full Name *</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($fullName) ?>" required>
      </div>

      <div class="field">
        <label>Email Address *</label>
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
      </div>

      <div class="field-group">
        <div class="field">
          <label>Password *</label>
          <input type="password" name="password" minlength="6" required>
        </div>
        <div class="field">
          <label>Confirm Password *</label>
          <input type="password" name="confirm_password" minlength="6" required>
        </div>
      </div>

      <div class="field">
        <label>Phone Number</label>
        <input type="tel" name="phone" value="<?= htmlspecialchars($phone) ?>">
      </div>

      <div class="field">
        <label>Address</label>
        <textarea name="address" rows="3"><?= htmlspecialchars($address) ?></textarea>
      </div>

      <button type="submit" class="btn-register">Create Account</button>

      <p class="terms">
        By signing up, you agree to our Terms of Service. We'll send booking confirmations to your email.
      </p>
    </form>

    <p class="login-link">
      Already have an account? <a href="/mikosplace/customer/login.php">Sign in</a>
    </p>
  </div>
</div>
</body>
</html>
