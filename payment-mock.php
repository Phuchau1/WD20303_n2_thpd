<?php
// Simple mock payment gateway page for testing
require_once __DIR__ . '/core/config.php';
require_once __DIR__ . '/core/database_new.php';
require_once __DIR__ . '/models/Order.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$pending = $_SESSION['pending_order'] ?? null;
if (!$pending){
    echo "No pending payment found."; exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])){
    $db = new Database();
    $orderModel = new Order($db);
    $orderId = $orderModel->create($pending['customer_name'],$pending['phone'],$pending['address'],$pending['total_price'],$pending['items']);
    if ($orderId){
        // mark as completed
        $orderModel->updateStatus($orderId, 'completed');
        // clear
        unset($_SESSION['pending_order']);
        $_SESSION['cart'] = [];
        header('Location: ' . BASE_URL . '/order-success.php?id=' . $orderId); exit;
    } else {
        $error = 'Failed to create order';
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Mock Payment</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
  <?php include __DIR__ . '/views/layouts/header.php'; ?>
  <main class="container" style="padding-top:100px">
    <h2>Thanh toán trực tuyến (Mock)</h2>
    <p>Khách hàng: <?= htmlspecialchars($pending['customer_name']) ?></p>
    <p>Tổng: <strong><?= number_format($pending['total_price'],0,',','.') ?> ₫</strong></p>
    <?php if (!empty($error)): ?><p style="color:red"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="post">
      <button class="btn" name="pay" type="submit">Thanh toán (Mô phỏng)</button>
      <a class="btn" href="<?= BASE_URL ?>/checkout.php">Quay lại</a>
    </form>
  </main>
</body>
</html>
