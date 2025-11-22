<?php
// Trang home cơ bản (nếu index.php không dùng template riêng)
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/functions.php';
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Quanan - Nội thất cao cấp</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>
  <?php include __DIR__ . '/layouts/header.php'; ?>

  <main class="container">
    <section style="padding:40px 0">
      <h1>Chào mừng đến với Quanan</h1>
      <p>Website mẫu - giao diện modern, tối giản. Mở file `index.php` để tuỳ chỉnh nội dung.</p>
      <p><a class="btn" href="<?= BASE_URL ?>/index.php?page=products">Xem sản phẩm</a></p>
    </section>
  </main>

  <footer style="padding:40px;text-align:center;background:#111;color:#fff">© <?= date('Y') ?> Quanan</footer>
  <!-- JS removed per request; site works without JavaScript -->
</body>
</html>
