<?php
require_once __DIR__ . '/core/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_GET['logout'])) {
    unset($_SESSION['user']);
    header('Location: ' . BASE_URL);
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '') ;
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    if ($email || $phone) {
        $_SESSION['user'] = ['name'=>$name,'email'=>$email,'phone'=>$phone];
        $_SESSION['flash_message'] = 'Đã lưu thông tin tài khoản';
        header('Location: ' . BASE_URL . '/views/my-orders.php');
        exit;
    } else {
        $message = 'Vui lòng nhập email hoặc số điện thoại để tiếp tục';
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Đăng nhập / Tài khoản</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/views/layouts/header.php'; ?>
<main class="container" style="padding-top:80px;max-width:700px;">
  <h2 style="margin-bottom:18px;font-size:1.6rem;font-weight:700;">Đăng nhập </h2>
  <?php if ($message): ?><p class="flash-message" style="background:transparent;color:#b00020;box-shadow:none;padding:0;margin-bottom:12px;"><?= htmlspecialchars($message) ?></p><?php endif; ?>
  <form method="post" style="background:#fff;padding:18px;border-radius:12px;box-shadow:0 8px 24px rgba(15,23,36,0.06);">
    <label style="display:block;margin-bottom:12px;">Họ và tên
      <input name="name" placeholder="Nguyễn Văn A" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e7e7e7;margin-top:6px;">
    </label>
    <label style="display:block;margin-bottom:12px;">Email
      <input name="email" type="email" placeholder="you@example.com" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e7e7e7;margin-top:6px;">
    </label>
    <label style="display:block;margin-bottom:12px;">Số điện thoại
      <input name="phone" placeholder="0901234567" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e7e7e7;margin-top:6px;">
    </label>
    <div style="display:flex;gap:12px;align-items:center;margin-top:8px;">
      <button class="btn" type="submit">Lưu thông tin</button>
      <a class="btn" href="<?= BASE_URL ?>/?guest=1" style="background:#f7f7f7;color:var(--color-text);">Tiếp tục như khách</a>
    </div>
  </form>
</main>
  <?php include __DIR__ . '/layouts/footer.php'; ?>
</body>
</html>
<?php
require_once __DIR__ . '/core/config.php';
// Redirect to admin login view for now (no separate frontend auth implemented)
