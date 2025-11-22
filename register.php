<?php
require_once __DIR__ . '/core/config.php';
// Simple registration placeholder — adjust to your auth flow if/when available
if (session_status() === PHP_SESSION_NONE) session_start();
include __DIR__ . '/views/layouts/header.php';
?>
<main class="container" style="padding-top:120px; padding-bottom:80px;">
  <div style="max-width:720px;margin:0 auto;background:white;padding:28px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,0.06);">
    <h2>Đăng ký tài khoản</h2>
    <p>Nút đăng ký tạm thời chưa được kích hoạt. Vui lòng liên hệ quản trị để tạo tài khoản hoặc chờ cập nhật.</p>
    <form method="post" action="#">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:12px;">
        <input name="first_name" placeholder="Họ" style="padding:10px;border:1px solid #eee;border-radius:8px;">
        <input name="last_name" placeholder="Tên" style="padding:10px;border:1px solid #eee;border-radius:8px;">
      </div>
      <input name="email" placeholder="Email" style="width:100%;padding:10px;border:1px solid #eee;border-radius:8px;margin-top:12px;">
      <input name="password" type="password" placeholder="Mật khẩu" style="width:100%;padding:10px;border:1px solid #eee;border-radius:8px;margin-top:12px;">
      <div style="margin-top:16px;display:flex;gap:12px;">
        <button class="btn" type="submit">Đăng ký (tạm)</button>
        <a class="btn" href="<?= BASE_URL ?>/login.php">Đăng nhập</a>
      </div>
    </form>
  </div>
</main>
<?php include __DIR__ . '/views/layouts/footer.php'; ?>
