<?php
require_once 'config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $st = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $st->execute([trim($_POST['username'] ?? '')]);
    $u = $st->fetch();
    if ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = $u['username'];
        header('Location: index.php'); exit;
    }
    $error = 'Wrong username or password.';
}
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Log in | Campus</title>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;800&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head><body class="login-page">
<form class="login-card form" method="post">
  <h1>Campus</h1>
  <p class="muted">Sign in to manage students and courses.</p>
  <?php if ($error): ?><p class="alert"><?= e($error) ?></p><?php endif; ?>
  <label>Username <input name="username" autocomplete="username" required autofocus></label>
  <label>Password <input name="password" type="password" autocomplete="current-password" required></label>
  <button class="btn btn-primary" type="submit">Sign in</button>
  <p class="muted small">Demo login: admin / admin123</p>
</form>
</body></html>
