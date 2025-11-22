<?php
require_once __DIR__ . '/../../core/config.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$user = $_SESSION['user'] ?? null;
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $_SESSION['user'] = ['name'=>$name,'email'=>$email,'phone'=>$phone];
    $message = 'Thông tin đã được cập nhật.';
    $user = $_SESSION['user'];
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Tài khoản của tôi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/layouts/header.php'; ?>
<main class="container" style="padding-top:80px;max-width:900px;">
  <h2 style="margin-bottom:18px;font-size:1.6rem;font-weight:700;">Hồ sơ của tôi</h2>
  <?php if ($message): ?><div class="flash-message" style="background:transparent;color:#0f5132;box-shadow:none;padding:0;margin-bottom:12px;"><?= htmlspecialchars($message) ?></div><?php endif; ?>
  <div style="display:grid;grid-template-columns:240px 1fr;gap:20px;">
    <nav style="background:#fff;padding:12px;border-radius:8px;">
      <ul style="list-style:none;padding:0;margin:0;">
        <li style="margin-bottom:10px;"><a href="<?= BASE_URL ?>/views/account.php">Hồ Sơ</a></li>
        <li style="margin-bottom:10px;"><a href="<?= BASE_URL ?>/views/my-orders.php">Đơn Mua</a></li>
        <li style="margin-bottom:10px;"><a href="<?= BASE_URL ?>/?logout=1">Đăng xuất</a></li>
      </ul>
    </nav>
    <section style="background:#fff;padding:18px;border-radius:12px;">
      <form method="post">
        <label style="display:block;margin-bottom:12px;">Họ và tên
          <input name="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e7e7e7;margin-top:6px;">
        </label>
        <label style="display:block;margin-bottom:12px;">Email
          <input name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e7e7e7;margin-top:6px;">
        </label>
        <label style="display:block;margin-bottom:12px;">Số điện thoại
          <input name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e7e7e7;margin-top:6px;">
        </label>
        <div style="display:flex;gap:12px;margin-top:8px;"><button class="btn" type="submit">Lưu</button><a class="btn" href="<?= BASE_URL ?>/views/my-orders.php" style="background:#f7f7f7;color:var(--color-text);">Xem đơn hàng</a></div>
      </form>
    </section>
  </div>
</main>
</body>
</html>
