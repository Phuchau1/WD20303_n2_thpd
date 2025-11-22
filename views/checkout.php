<?php
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/database_new.php';
require_once __DIR__ . '/../models/Order.php';

if (session_status() === PHP_SESSION_NONE) session_start();
$db = new Database();
$orderModel = new Order($db);

$cart = $_SESSION['checkout_items'] ?? $_SESSION['cart'] ?? [];
if (empty($cart)){
  echo "<p>Giỏ hàng trống</p>"; exit;
}
$total = 0; $items = [];
foreach ($cart as $c){
  $price = $c['product']['sale_price']?:$c['product']['price'];
  $items[] = ['product_id'=>$c['product']['id'],'quantity'=>$c['quantity'],'price'=>$price];
  $total += $price * $c['quantity'];
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  $name = trim($_POST['name'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $province = trim($_POST['province'] ?? '');
  $district = trim($_POST['district'] ?? '');
  $ward = trim($_POST['ward'] ?? '');
  $address_specific = trim($_POST['address_specific'] ?? '');
  $address_type = $_POST['address_type'] ?? 'home';
  $add_location = isset($_POST['add_location']) ? true : false;
  $lat = trim($_POST['lat'] ?? '');
  $lng = trim($_POST['lng'] ?? '');
  $payment_method = $_POST['payment_method'] ?? 'cod';

  // compose a single address string from parts
  $address_parts = [];
  if ($address_specific) $address_parts[] = $address_specific;
  if ($ward) $address_parts[] = $ward;
  if ($district) $address_parts[] = $district;
  if ($province) $address_parts[] = $province;
  $address = implode(', ', $address_parts);

  if ($name && $address){
    if ($payment_method === 'cod'){
      // save email to order and session
      $orderId = $orderModel->create($name,$phone,$address,$total,$items, $email);
      if ($email){
        $_SESSION['user'] = array_merge($_SESSION['user'] ?? [], ['name'=>$name,'email'=>$email,'phone'=>$phone]);
      }
            if ($orderId){
        // optionally store address metadata
        $_SESSION['last_order_address'] = ['type'=>$address_type,'lat'=>$lat,'lng'=>$lng];
        // clear checkout items and cart
        unset($_SESSION['checkout_items']);
        $_SESSION['cart'] = [];
        header('Location: ' . BASE_URL . '/order-success.php?id=' . $orderId); exit;
            } else {
                $message = "Lỗi lưu đơn hàng";
            }
        } else {
            // Online payment: save pending order in session and redirect to payment gateway mock
      $_SESSION['pending_order'] = ['customer_name'=>$name,'phone'=>$phone,'email'=>$email,'address'=>$address,'address_type'=>$address_type,'lat'=>$lat,'lng'=>$lng,'total_price'=>$total,'items'=>$items];
      if ($email){
        $_SESSION['user'] = array_merge($_SESSION['user'] ?? [], ['name'=>$name,'email'=>$email,'phone'=>$phone]);
      }
            header('Location: ' . BASE_URL . '/payment-mock.php'); exit;
        }
    } else {
        $message = "Vui lòng điền họ tên và địa chỉ";
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>Thanh toán</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
  <?php include __DIR__ . '/layouts/header.php'; ?>
  <main class="container" style="padding-top:80px;max-width:980px;">
    <h2 style="margin-bottom:20px;font-size:1.75rem;font-weight:800;">Thanh toán</h2>
    <?php if ($message): ?><p class="flash-message" style="background:transparent;color:#b00020;box-shadow:none;padding:0;margin-bottom:12px;"><?= htmlspecialchars($message) ?></p><?php endif; ?>

    <form method="post" class="checkout-form" style="display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start;">
      <section aria-labelledby="contact-heading" style="background:#fff;border-radius:12px;padding:20px;box-shadow:0 6px 20px rgba(15,23,36,0.06);">
        <h3 id="contact-heading" style="font-size:1.05rem;margin-bottom:12px;font-weight:700;">Thông tin người nhận</h3>
        <fieldset style="border:none;padding:0;margin:0;">
          <label for="name" style="display:block;margin-bottom:12px;font-weight:600;">Họ và tên
            <input id="name" name="name" required aria-required="true" placeholder="Nguyễn Văn A" value="<?= htmlspecialchars(
              $_POST['name'] ?? $_SESSION['user']['name'] ?? ''
            ) ?>" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e7e7e7;margin-top:6px;">
          </label>

          <label for="email" style="display:block;margin-bottom:12px;font-weight:600;">Email
            <input id="email" name="email" type="email" placeholder="you@example.com" value="<?= htmlspecialchars(
              $_POST['email'] ?? $_SESSION['user']['email'] ?? ''
            ) ?>" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e7e7e7;margin-top:6px;">
          </label>

          <label for="phone" style="display:block;margin-bottom:12px;font-weight:600;">Số điện thoại
            <input id="phone" name="phone" type="tel" placeholder="0901234567" value="<?= htmlspecialchars(
              $_POST['phone'] ?? $_SESSION['user']['phone'] ?? ''
            ) ?>" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e7e7e7;margin-top:6px;">
          </label>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;">
            <label style="display:block;font-weight:600;">Tỉnh / Thành phố
              <input name="province" placeholder="Hà Nội" value="<?= htmlspecialchars($_POST['province'] ?? '') ?>" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e7e7e7;margin-top:6px;">
            </label>
            <label style="display:block;font-weight:600;">Quận / Huyện
              <input name="district" placeholder="Quận Hoàn Kiếm" value="<?= htmlspecialchars($_POST['district'] ?? '') ?>" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e7e7e7;margin-top:6px;">
            </label>
          </div>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:12px;">
            <label style="display:block;font-weight:600;">Phường / Xã
              <input name="ward" placeholder="Phường Hàng Bạc" value="<?= htmlspecialchars($_POST['ward'] ?? '') ?>" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e7e7e7;margin-top:6px;">
            </label>
            <label style="display:block;font-weight:600;">Địa chỉ cụ thể
              <input name="address_specific" placeholder="Số nhà, tên đường" required value="<?= htmlspecialchars($_POST['address_specific'] ?? '') ?>" style="width:100%;padding:10px;border-radius:8px;border:1px solid #e7e7e7;margin-top:6px;">
            </label>
          </div>

          <div style="margin-bottom:12px;display:flex;gap:12px;align-items:center;">
            <label style="display:inline-flex;align-items:center;gap:8px;font-weight:600;"><input type="checkbox" name="add_location" <?= isset($_POST['add_location']) ? 'checked' : '' ?>> Thêm vị trí</label>
            <div style="flex:1;display:flex;gap:8px;">
              <input name="lat" placeholder="Lat" value="<?= htmlspecialchars($_POST['lat'] ?? '') ?>" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e7e7e7;">
              <input name="lng" placeholder="Lng" value="<?= htmlspecialchars($_POST['lng'] ?? '') ?>" style="width:100%;padding:8px;border-radius:8px;border:1px solid #e7e7e7;">
            </div>
          </div>
        </fieldset>

        <fieldset style="border:none;padding:0;margin-top:6px;">
          <legend style="font-weight:700;margin-bottom:8px;">Loại địa chỉ</legend>
          <label style="display:inline-flex;align-items:center;gap:8px;margin-right:16px;"><input type="radio" name="address_type" value="home" checked> Nhà riêng</label>
          <label style="display:inline-flex;align-items:center;gap:8px;"><input type="radio" name="address_type" value="office"> Văn phòng</label>
        </fieldset>

        <fieldset style="border:none;padding:0;margin-top:12px;">
          <legend style="font-weight:700;margin-bottom:8px;">Phương thức thanh toán</legend>
          <label style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
            <input type="radio" name="payment_method" value="cod" checked aria-checked="true"> <span>Thanh toán khi nhận hàng (COD)</span>
          </label>
          <label style="display:flex;align-items:center;gap:8px;margin-bottom:8px;">
            <input type="radio" name="payment_method" value="online"> <span>Thanh toán trực tuyến (Mock)</span>
          </label>
        </fieldset>
      </section>

      <aside class="checkout-summary" aria-labelledby="order-heading" style="background:#fff;border-radius:12px;padding:18px;box-shadow:0 6px 20px rgba(15,23,36,0.06);">
        <h3 id="order-heading" style="font-size:1.05rem;margin-bottom:12px;font-weight:700;">Đơn hàng</h3>
        <ul class="checkout-items" style="list-style:none;padding:0;margin:0 0 12px 0;max-height:320px;overflow:auto;">
          <?php foreach ($cart as $c):
            $p = $c['product'];
            $price = $p['sale_price']?:$p['price'];
            $line = $price * $c['quantity'];
          ?>
            <li style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #f3efe9;">
              <div style="width:48px;height:48px;overflow:hidden;border-radius:8px;background:#f7f7f7;"><img src="<?= productImageUrl($p['image'] ?? '', $p['slug'] ?? $p['name']) ?>" alt="<?= e($p['name']) ?>" style="width:100%;height:100%;object-fit:cover;"></div>
              <div style="flex:1;min-width:0;">
                <div style="font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($p['name']) ?></div>
                <div style="font-size:0.92rem;color:#666;margin-top:6px;">x<?= $c['quantity'] ?> · <?= number_format($price,0,',','.') ?> ₫</div>
              </div>
              <div style="font-weight:700;color:var(--color-accent);"><?= number_format($line,0,',','.') ?> ₫</div>
            </li>
          <?php endforeach; ?>
        </ul>

        <div style="display:flex;justify-content:space-between;margin-top:12px;font-weight:600;"><span>Tạm tính</span><span><?= number_format($total,0,',','.') ?> ₫</span></div>
        <div style="display:flex;justify-content:space-between;margin-top:6px;color:#666;"><span>Phí vận chuyển</span><span>Tính khi chọn địa chỉ</span></div>
        <hr style="border:none;height:1px;background:#f1f1f1;margin:12px 0;">
        <div style="display:flex;justify-content:space-between;font-size:1.1rem;font-weight:800;margin-bottom:12px;"><span>Tổng</span><span style="color:var(--color-accent);"><?= number_format($total,0,',','.') ?> ₫</span></div>

        <button class="btn" type="submit" style="width:100%;padding:12px 16px;border-radius:10px;font-weight:700;">Xác nhận và Thanh toán</button>
        <a href="<?= BASE_URL ?>" class="btn" style="display:block;text-align:center;margin-top:10px;background:#f7f7f7;color:var(--color-text);">Tiếp tục mua sắm</a>
      </aside>
    </form>
  </main>
</body>
</html>
