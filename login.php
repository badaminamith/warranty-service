<?php
session_start();
$error = '';
if (isset($_POST['login'])) {
    if ($_POST['user'] === 'Namith' && $_POST['pass'] === '1984') {
        $_SESSION['user'] = 'admin';
        header("Location: dashboard.php");
        exit();
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login — NBCareDesk</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="login-page">
  <div class="login-left">
    <div class="shield-icon">🛡️</div>
    <h1>Streamline Your<br><span>Service Center</span></h1>
    <p>Manage customers, track warranties, and handle service records from one unified enterprise platform.</p>
  </div>

  <div class="login-right">
    <h2>Welcome Back</h2>
    <p class="login-subtitle">Sign in to NBCareDesk Admin Portal</p>

    <?php if ($error): ?>
      <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="form-group">
        <label>Username</label>
        <div class="input-group">
          <span class="input-icon">👤</span>
          <input name="user" type="text" class="form-control" placeholder="admin" required>
        </div>
      </div>
      <div class="form-group">
        <label>Password</label>
        <div class="input-group">
          <span class="input-icon">🔒</span>
          <input name="pass" type="password" class="form-control" placeholder="••••" required>
        </div>
      </div>
      <button type="submit" name="login" class="btn-login">Sign In →</button>
    </form>
  </div>
</div>
</body>
</html>