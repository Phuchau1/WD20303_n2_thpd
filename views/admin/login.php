<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/database_new.php';
require_once __DIR__ . '/../../models/User.php';

session_start();
$db = new Database();
$userModel = new User($db);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    $user = $userModel->findAdminByUsername($u);
    if ($user && password_verify($p, $user['password_hash'])) {
        $_SESSION['admin_user'] = $user;
        header('Location: ' . BASE_URL . '/index.php?page=admin/dashboard');
        exit;
    } else {
        $error = 'Sai tên đăng nhập hoặc mật khẩu.';
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Login</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body class="admin-login">
  <form method="post" class="admin-login-box">
    <h2>Đăng nhập Admin</h2>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <label>Tên đăng nhập<input name="username" required></label>
    <label>Mật khẩu<input type="password" name="password" required></label>
    <button class="btn" type="submit">Đăng nhập</button>
  </form>
</body>
</html>
