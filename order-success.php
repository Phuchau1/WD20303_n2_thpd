<?php
require_once __DIR__ . '/core/config.php';
require_once __DIR__ . '/core/database_new.php';
require_once __DIR__ . '/models/Order.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$db = new Database();
$orderModel = new Order($db);
$order = $orderModel->find($id);
if (!$order){ echo 'Order not found'; exit; }
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Đặt hàng thành công</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
  <?php include __DIR__ . '/views/layouts/header.php'; ?>
  <main class="container" style="padding-top:100px">
    <div class="order-success">
      <h2>Đặt hàng thành công</h2>
      <p>Mã đơn hàng: <strong>#<?= $order['id'] ?></strong></p>
      <p>Khách hàng: <?= htmlspecialchars($order['customer_name']) ?></p>
      <p>Trạng thái: <?= htmlspecialchars($order['status']) ?></p>
      <h3>Chi tiết</h3>
      <ul>
        <?php foreach ($order['items'] as $it): ?>
          <li><?= htmlspecialchars($it['name']) ?> x<?= $it['quantity'] ?> - <?= number_format($it['price'],0,',','.') ?> ₫</li>
        <?php endforeach; ?>
      </ul>
      <div>Tổng: <strong><?= number_format($order['total_price'],0,',','.') ?> ₫</strong></div>
      <div style="margin-top:16px"><a class="btn" href="<?= BASE_URL ?>">Tiếp tục mua sắm</a> <a class="btn" href="<?= BASE_URL ?>/views/cart.php">Xem giỏ hàng</a></div>
    </div>
  </main>
</body>
</html>
