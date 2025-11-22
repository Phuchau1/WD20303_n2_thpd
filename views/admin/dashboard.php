<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/database_new.php';
require_once __DIR__ . '/../../models/Product.php';
require_once __DIR__ . '/../../models\Category.php';
require_once __DIR__ . '/../../models/Order.php';

session_start();
if (empty($_SESSION['admin_user'])){
    header('Location: ' . BASE_URL . '/index.php?page=admin/login'); exit;
}

$db = new Database();
$productModel = new Product($db);
$categoryModel = new Category($db);
$orderModel = new Order($db);

$products = $productModel->getAll(100);
$categories = $categoryModel->getAll();
$orders = $orderModel->all();

$revenue = 0; foreach ($orders as $o) $revenue += $o['total_price'] ?? 0;
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body class="admin">
  <header class="admin-header">
    <h1>Admin Dashboard</h1>
    <div><a href="<?= BASE_URL ?>">Xem site</a> | <a href="<?= BASE_URL ?>/index.php?page=admin/products">Sản phẩm</a> | <a href="<?= BASE_URL ?>/index.php?page=admin/orders">Đơn hàng</a></div>
  </header>
  <main class="admin-main">
    <div class="stats">
      <div class="card"><h3>Doanh thu</h3><p><?= number_format($revenue,0,',','.') ?> ₫</p></div>
      <div class="card"><h3>Sản phẩm</h3><p><?= count($products) ?></p></div>
      <div class="card"><h3>Danh mục</h3><p><?= count($categories) ?></p></div>
      <div class="card"><h3>Đơn hàng</h3><p><?= count($orders) ?></p></div>
    </div>
  </main>
</body>
</html>
