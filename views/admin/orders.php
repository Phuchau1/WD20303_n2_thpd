<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/database_new.php';
require_once __DIR__ . '/../../models/Order.php';

session_start();
if (empty($_SESSION['admin_user'])){
    header('Location: ' . BASE_URL . '/index.php?page=admin/login'); exit;
}
$db = new Database();
$orderModel = new Order($db);

if (isset($_GET['id'])){
    $order = $orderModel->find((int)$_GET['id']);
}
else {
    $orders = $orderModel->all();
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Đơn hàng</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body class="admin">
  <header class="admin-header"><h1>Đơn hàng</h1></header>
  <main class="admin-main">
    <?php if (!empty($order)): ?>
      <h2>Đơn #<?= $order['id'] ?></h2>
      <p>Khách: <?= htmlspecialchars($order['customer_name']) ?></p>
      <p>Địa chỉ: <?= nl2br(htmlspecialchars($order['address'])) ?></p>
      <ul>
        <?php foreach ($order['items'] as $it): ?>
          <li><?= htmlspecialchars($it['name']) ?> x<?= $it['quantity'] ?> - <?= number_format($it['price'],0,',','.') ?> ₫</li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <table class="admin-table"><thead><tr><th>ID</th><th>Khách</th><th>Tổng</th><th>Ngày</th><th>Hành động</th></tr></thead><tbody>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td><?= $o['id'] ?></td>
            <td><?= htmlspecialchars($o['customer_name']) ?></td>
            <td><?= number_format($o['total_price'],0,',','.') ?> ₫</td>
            <td><?= $o['created_at'] ?></td>
            <td><a href="<?= BASE_URL ?>/index.php?page=admin/orders&id=<?= $o['id'] ?>">Xem</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody></table>
    <?php endif; ?>
  </main>
</body>
</html>
