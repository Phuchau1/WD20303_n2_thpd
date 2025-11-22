

<?php
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/database_new.php';
require_once __DIR__ . '/../models/Product.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$db = new Database();
$productModel = new Product($db);
$cart = $_SESSION['cart'] ?? [];

// Quick debug endpoint: append ?dumpcart=1 to URL to see session cart content
if (isset($_GET['dumpcart']) && php_sapi_name() !== 'cli') {
	header('Content-Type: text/plain; charset=utf-8');
	echo "SESSION CART:\n";
	var_export($_SESSION['cart'] ?? []);
	exit;
}
$total = 0;

// Xử lý cập nhật/xóa/sửa giỏ hàng, selection, coupon, checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// store selected checkboxes (if present)
	if (isset($_POST['selected'])) {
		$_SESSION['cart_selected'] = array_fill_keys(array_keys($_POST['selected']), true);
	}

	// apply coupon
	if (isset($_POST['apply_coupon'])) {
		$code = trim($_POST['coupon'] ?? '');
		if ($code !== '') {
			if (strtoupper($code) === 'MGM50') {
				$_SESSION['coupon'] = ['code' => $code, 'type' => 'fixed', 'value' => 50000];
				$_SESSION['flash_message'] = 'Áp dụng mã Mã giảm 50.000₫ thành công';
			} elseif (strtoupper($code) === 'SALE10') {
				$_SESSION['coupon'] = ['code' => $code, 'type' => 'percent', 'value' => 10];
				$_SESSION['flash_message'] = 'Áp dụng mã SALE10 - giảm 10% thành công';
			} else {
				$_SESSION['flash_message'] = 'Mã giảm giá không hợp lệ';
				unset($_SESSION['coupon']);
			}
		}
	}

	// increment / decrement quick controls (submitted via buttons)
	if (isset($_POST['inc'])) {
		foreach ($_POST['inc'] as $pid => $v) {
			if (isset($cart[$pid])) $cart[$pid]['quantity'] = max(1, $cart[$pid]['quantity'] + 1);
		}
	}
	if (isset($_POST['dec'])) {
		foreach ($_POST['dec'] as $pid => $v) {
			if (isset($cart[$pid])) {
				$cart[$pid]['quantity'] = max(0, $cart[$pid]['quantity'] - 1);
				if ($cart[$pid]['quantity'] === 0) unset($cart[$pid]);
			}
		}
	}

	if (isset($_POST['qty'])) {
		foreach ($_POST['qty'] as $pid => $qty) {
			$qty = max(0, (int)$qty);
			if ($qty > 0 && isset($cart[$pid])) {
				$cart[$pid]['quantity'] = $qty;
			} elseif ($qty == 0) {
				unset($cart[$pid]);
			}
		}
	}
	if (isset($_POST['remove'])) {
		foreach ($_POST['remove'] as $pid => $v) {
			unset($cart[$pid]);
		}
	}

	// handle checkout action: collect selected items and redirect to checkout page
	if (isset($_POST['checkout'])) {
		$sel = $_SESSION['cart_selected'] ?? (isset($_POST['selected']) ? array_fill_keys(array_keys($_POST['selected']), true) : null);
		if ($sel === null) {
			$sel = [];
			foreach ($cart as $pid => $c) $sel[$pid] = true;
		}
		$checkout_items = [];
		foreach ($sel as $pid => $_v) {
			if (isset($cart[$pid])) $checkout_items[$pid] = $cart[$pid];
		}
		$_SESSION['checkout_items'] = $checkout_items;
		$_SESSION['cart'] = $cart;
		header('Location: ' . BASE_URL . '/views/checkout.php');
		exit;
	}

	$_SESSION['cart'] = $cart;
	header('Location: ' . BASE_URL . '/views/cart.php');
	exit;
}

// Lấy lại thông tin sản phẩm từ DB để đảm bảo luôn đúng và tính toán tổng tiền
$selected_session = $_SESSION['cart_selected'] ?? null; // associative [pid => true]
$coupon = $_SESSION['coupon'] ?? null;
$subtotal_all = 0;
$subtotal_selected = 0;
foreach ($cart as $pid => $c) {
	$p = $c['product'];
	$price = $p['sale_price'] ?: $p['price'];
	$row = $price * $c['quantity'];
	$subtotal_all += $row;
	if ($selected_session === null || isset($selected_session[$pid])) {
		$subtotal_selected += $row;
	}
}

// compute selected quantity
$selected_qty = 0;
foreach ($cart as $pid => $c) {
	if ($selected_session === null || isset($selected_session[$pid])) $selected_qty += $c['quantity'];
}

// compute discount from coupon
$discount = 0;
if ($coupon) {
	if ($coupon['type'] === 'percent') {
		$discount = round($subtotal_selected * ($coupon['value'] / 100));
	} else {
		$discount = min($subtotal_selected, $coupon['value']);
	}
}

// shipping rule: free over 500k, otherwise 30k (example)
$shipping = ($subtotal_selected - $discount <= 0) ? 0 : (($subtotal_selected - $discount) >= 500000 ? 0 : 30000);

$final_total = max(0, $subtotal_selected - $discount + $shipping);

?>
<!doctype html>
<html lang="vi">
<head>
	<meta charset="utf-8">
	<title>Giỏ hàng</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/layouts/header.php'; ?>
<main class="container" style="padding-top:60px;">
	<h2 style="margin-bottom:20px;font-size:1.6rem;font-weight:700;">Giỏ h</h2>
	<?php if (empty($cart)): ?>
		<div class="empty-cart" style="padding:30px 0;text-align:center;font-size:1.1rem;">Giỏ hàng trống.<br><a href="<?= BASE_URL ?>" class="btn" style="margin-top:14px;">Tiếp tục mua sắm</a></div>
	<?php else: ?>
	<form method="post" class="cart-container" style="gap:24px;">
		<div class="cart-card">
			<div style="margin-bottom:12px;display:flex;align-items:center;justify-content:space-between;">
				<div style="font-weight:700;color:#666;">Bạn đang mua <span style="color:var(--color-accent);"><?= array_sum(array_column($cart,'quantity')) ?></span> sản phẩm</div>
			
			</div>

			<div class="cart-table-wrap">
			<table class="cart-table">
				<thead>
					<tr>
						<th style="width:40px;"></th>
						<th style="text-align:left;">Sản phẩm</th>
						<th style="width:160px">Đơn giá</th>
						<th style="width:160px">Số lượng</th>
						<th style="width:160px">Thành tiền</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<?php foreach ($cart as $pid => $c):
					$p = $c['product'];
					$price = $p['sale_price'] ?: $p['price'];
					$subtotal = $price * $c['quantity'];
					$img = productImageUrl($p['image'] ?? '', $p['slug'] ?? $p['name']);
				?>
					<tr>
						<td>
							<input type="checkbox" name="selected[<?= $pid ?>]" value="1" <?= ($selected_session === null || isset($selected_session[$pid])) ? 'checked' : '' ?> >
						</td>
						<td style="text-align:left;">
							<div class="cart-product">
								<div class="cart-thumb"><img src="<?= $img ?>" alt="<?= e($p['name']) ?>" style="display:block;width:48px;height:48px;object-fit:cover;object-position:center;"></div>
								<div>
									<div class="cart-name"><?= e($p['name']) ?></div>
									<div style="font-size:0.95rem;color:#888;margin-top:6px;">Nhà bán: <strong><?= e($p['vendor'] ?? 'Shop Chính') ?></strong></div>
								</div>
							</div>
						</td>
						<td class="unit-price" style="text-align:center;">
							<?php if (!empty($p['sale_price']) && $p['sale_price'] < $p['price']): ?>
								<div class="price-old" style="color:#999;font-size:0.95rem;text-decoration:line-through;"><?= number_format($p['price'],0,',','.') ?> ₫</div>
								<div class="price-current" style="color:#ff5c3c;font-weight:700;margin-top:4px;"><?= number_format($p['sale_price'],0,',','.') ?> ₫</div>
							<?php else: ?>
								<div class="price-current" style="color:var(--color-accent);font-weight:700;"><?= number_format($price,0,',','.') ?> ₫</div>
							<?php endif; ?>
						</td>
						<td>
							<div class="cart-qty-controls">
								<button type="submit" name="dec[<?= $pid ?>]" value="1" class="qty-btn">−</button>
								<input type="number" name="qty[<?= $pid ?>]" value="<?= $c['quantity'] ?>" min="1" style="width:64px;padding:8px;border-radius:8px;border:1px solid #e7e7e7;text-align:center">
								<button type="submit" name="inc[<?= $pid ?>]" value="1" class="qty-btn">+</button>
							</div>
						</td>
						<td class="subtotal" style="text-align:right;color:#ff5c3c;font-weight:800;"><?= number_format($subtotal,0,',','.') ?> ₫</td>
						<td style="text-align:right;">
							<button type="submit" name="remove[<?= $pid ?>]" value="1" class="btn-remove">Xóa</button>
							
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			</div>
		</div>

		<aside class="cart-summary">
			<h3 style="margin-top:0;margin-bottom:10px;font-size:1.15rem;font-weight:700;">Tóm tắt đơn hàng</h3>
			<div style="display:flex;justify-content:space-between;margin-bottom:8px;"><span>Số sản phẩm (đã chọn)</span><strong><?= $selected_qty ?></strong></div>
			<div style="display:flex;justify-content:space-between;margin-bottom:8px;"><span>Tạm tính</span><strong><?= number_format($subtotal_selected,0,',','.') ?> ₫</strong></div>
			<?php if ($coupon): ?>
				<div style="display:flex;justify-content:space-between;margin-bottom:8px;"><span>Giảm (<?= htmlspecialchars($coupon['code']) ?>)</span><strong>-<?= number_format($discount,0,',','.') ?> ₫</strong></div>
			<?php endif; ?>
			<div style="display:flex;justify-content:space-between;margin-bottom:18px;"><span>Phí vận chuyển</span><strong><?= number_format($shipping,0,',','.') ?> ₫</strong></div>
			<hr style="border:none;height:1px;background:#f1f1f1;margin:14px 0;">
			<div style="display:flex;justify-content:space-between;font-size:1.15rem;font-weight:800;margin-bottom:18px;"><span>Tổng</span><span style="color:var(--color-accent);"><?= number_format($final_total,0,',','.') ?> ₫</span></div>

			<div style="margin-bottom:12px;display:flex;gap:8px;">
				<input type="text" name="coupon" placeholder="Mã giảm giá" value="<?= htmlspecialchars($coupon['code'] ?? '') ?>" style="flex:1;padding:8px;border-radius:8px;border:1px solid #e7e7e7;">
				<button type="submit" name="apply_coupon" class="btn" style="padding:10px 14px;">Áp dụng</button>
			</div>

			<button type="submit" name="checkout" value="1" class="btn" style="width:100%;display:inline-block;text-align:center;padding:12px 18px;">Mua hàng</button>
			<a class="btn" href="<?= BASE_URL ?>" style="width:100%;display:inline-block;text-align:center;padding:10px 18px;margin-top:10px;background:#f7f7f7;color:var(--color-text);">Tiếp tục mua sắm</a>
		</aside>
	</form>
	<?php endif; ?>
</main>
</body>
</html>

