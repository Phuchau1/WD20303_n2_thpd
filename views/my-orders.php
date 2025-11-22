<?php
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/database_new.php';
require_once __DIR__ . '/../models/Order.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$db = new Database();
$orderModel = new Order($db);
// load all orders then optionally filter by logged-in user (email or phone)
$orders_all = $orderModel->all();
$user = $_SESSION['user'] ?? null;
if ($user) {
  $orders = array_filter($orders_all, function($o) use ($user) {
    $email = $user['email'] ?? '';
    $phone = $user['phone'] ?? '';
    return ($email && isset($o['email']) && $o['email'] === $email) || ($phone && isset($o['phone']) && $o['phone'] === $phone);
  });
} else {
  $orders = $orders_all;
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Lịch sử đơn hàng</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <style>
    .orders-list { display: grid; gap: 18px; }
    .order-card { background:#fff;border-radius:12px;padding:18px;border:1px solid #f1ece6; }
    .order-shop { display:flex;align-items:center;gap:12px;margin-bottom:12px }
    .order-shop .shop-name{font-weight:700;color:#c33}
    .order-items li{display:flex;gap:12px;align-items:center;padding:10px 0;border-top:1px solid #f3efe9}
    .order-items li:first-child{border-top:0}
    .order-item-thumb{width:56px;height:56px;border-radius:8px;overflow:hidden;background:#f7f7f7}
    .order-item-thumb img{width:100%;height:100%;object-fit:cover}
    .order-actions{display:flex;gap:12px;justify-content:flex-end;margin-top:12px}
    .order-meta{display:flex;gap:12px;align-items:center;color:#666;font-size:0.95rem}
    .badge { padding:6px 10px;border-radius:12px;font-weight:700;color:#fff;background:#9aa; }
    .badge.success{background:#2da84f}
    .badge.pending{background:#ffb86b}
  </style>
</head>
<body>
<?php include __DIR__ . '/layouts/header.php'; ?>
<main class="container" style="padding-top:80px;">
  <h2 style="margin-bottom:18px;font-size:1.5rem;font-weight:800;">Lịch sử 
    đơn hàng</h2>

  <?php if (empty($orders)): ?>
    <div style="background:#fff;padding:28px;border-radius:12px;text-align:center;">Bạn chưa có đơn hàng nào. <a href="<?= BASE_URL ?>" class="btn">Tiếp tục mua sắm</a></div>
  <?php else: ?>
    <div class="orders-list">
      <?php foreach ($orders as $order):
        $items = $orderModel->find($order['id'])['items'] ?? [];
        $status = $order['status'] ?? 'pending';
      ?>
        <article class="order-card">
          <div class="order-shop">
            <div style="flex:1">
              <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                <div style="display:flex;align-items:center;gap:12px;">
                  <div class="shop-icon" style="width:36px;height:36px;border-radius:6px;background:#f3e7d7;display:inline-block"></div>
                  <div>
                    <div class="shop-name">Shop</div>
                    <div style="font-size:0.95rem;color:#888;margin-top:4px;">Đơn hàng #<?= $order['id'] ?> • <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></div>
                  </div>
                </div>
                <div style="text-align:right;">
                  <div class="order-meta"><span class="badge <?= $status === 'completed' ? 'success' : 'pending' ?>"><?= strtoupper($status) ?></span></div>
                </div>
              </div>
            </div>
          </div>

          <ul class="order-items" style="list-style:none;padding:0;margin:8px 0 0 0;">
            <?php foreach ($items as $it):
              $prodName = $it['name'] ?? 'Sản phẩm';
            ?>
              <li>
                <div class="order-item-thumb"><img src="<?= BASE_URL ?>/assets/images/img/default.png" alt=""></div>
                <div style="flex:1;min-width:0">
                  <div style="font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= htmlspecialchars($prodName) ?></div>
                  <div style="color:#888;font-size:0.95rem;margin-top:6px;">Số lượng: <?= $it['quantity'] ?></div>
                </div>
                <div style="font-weight:800;color:var(--color-accent);"><?= number_format($it['price'] * $it['quantity'],0,',','.') ?> ₫</div>
              </li>
            <?php endforeach; ?>
          </ul>

          <div style="display:flex;justify-content:space-between;align-items:center;margin-top:12px;">
            <div style="color:#666">Thành tiền: <strong><?= number_format($order['total_price'],0,',','.') ?> ₫</strong></div>
            <div class="order-actions">
              <a class="btn" href="<?= BASE_URL ?>" style="background:#ff6b3c">Mua lại</a>
              <a class="btn" href="#" style="background:#fff;color:var(--color-text);border:1px solid #e7e7e7">Liên hệ Người Bán</a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>
</body>
</html>
