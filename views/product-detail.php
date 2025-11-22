<?php
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/database_new.php';
require_once __DIR__ . '/../core/functions.php';
require_once __DIR__ . '/../models/Product.php';

$db = new Database();
$productModel = new Product($db);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $productModel->getById($id);
if (!$product) {
    echo "<p>Sản phẩm không tồn tại</p>"; exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($product['name']) ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="page-product-detail">
  <?php include __DIR__ . '/layouts/header.php'; ?>
  <main class="container" style="padding:60px 0 60px 0;max-width:1100px;">
    <div class="product-detail" style="background:#fff;border-radius:22px;box-shadow:0 8px 32px rgba(30,41,59,0.13);padding:36px 24px 36px 24px;max-width:1000px;margin:0 auto;">
      <div class="detail-grid" style="display:flex;gap:48px;flex-wrap:wrap;align-items:flex-start;">
        <?php
          $img = productImageUrl($product['image'], $product['slug'] ?? $product['name']);
          $thumbs = [$img];
          if (!empty($product['image'])){
              $info = pathinfo($product['image']);
              $candidates = [
                  $info['filename'] . '-2.' . $info['extension'],
                  $info['filename'] . '-3.' . $info['extension'],
                  $info['filename'] . '_2.' . $info['extension'],
                  $info['filename'] . '_3.' . $info['extension'],
                  $info['filename'] . '-detail.' . $info['extension'],
                  $info['filename'] . '-hover.' . $info['extension']
              ];
              foreach ($candidates as $c){
                  if (file_exists(UPLOAD_DIR . $c)) $thumbs[] = UPLOAD_URL . $c;
              }
          }
        ?>
        <div class="detail-images luxury-gallery" style="flex:1 1 420px;max-width:480px;">
          <div class="main-image-box" style="width:100%;height:400px;overflow:hidden;background:#f7f7f7;border-radius:18px;box-shadow:0 2px 12px rgba(30,41,59,0.07);margin-bottom:18px;">
            <img id="main-image" class="main-image" src="<?= $img ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width:100%;height:100%;object-fit:cover;object-position:center;display:block;" onerror="this.src='https://via.placeholder.com/900x600?text=No+Image'">
          </div>
          <?php if (count($thumbs) > 1): ?>
            <div class="detail-thumbs thumbs-row" style="display:flex;gap:10px;justify-content:center;">
              <?php foreach ($thumbs as $i => $t): ?>
                <img data-src="<?= $t ?>" class="thumb-img <?= $i===0? 'active':'' ?>" src="<?= $t ?>" alt="thumb" style="width:60px;height:60px;object-fit:cover;border-radius:10px;border:2px solid #eee;box-shadow:0 1px 4px rgba(30,41,59,0.07);">
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
        <div class="detail-info" style="flex:1 1 380px;max-width:520px;">
          <h1 class="luxury-title" style="font-size:2.2rem;font-weight:800;margin-bottom:18px;line-height:1.2;color:#1a1a1a;letter-spacing:0.5px;"> <?= htmlspecialchars($product['name']) ?> </h1>
          <?php if (!empty($product['category_name'])): ?>
            <div class="product-breadcrumb" style="margin-bottom:12px;">Danh mục: <a href="<?= BASE_URL ?>/products.php?category_id=<?= $product['category_id'] ?>" style="color:#c6a47e;font-weight:600;"> <?= e($product['category_name']) ?> </a></div>
          <?php endif; ?>
          <div class="luxury-box" style="margin-bottom:22px;">
            <div class="price-block" style="display:flex;align-items:center;gap:18px;margin-bottom:10px;">
              <?php if (!empty($product['sale_price'])): ?>
                <div class="price-current" style="font-size:2rem;font-weight:700;color:#c6a47e;"> <?= number_format($product['sale_price'],0,',','.') ?> ₫</div>
                <div class="price-old" style="font-size:1.2rem;color:#aaa;text-decoration:line-through;"> <?= number_format($product['price'],0,',','.') ?> ₫</div>
                <div class="price-badge" style="background:linear-gradient(90deg,#e74c3c 60%,#ffb199 100%);color:#fff;padding:7px 16px;border-radius:16px;font-size:14px;font-weight:600;box-shadow:0 2px 8px rgba(231,76,60,0.13);margin-left:8px;">Giảm</div>
              <?php else: ?>
                <div class="price-current" style="font-size:2rem;font-weight:700;color:#c6a47e;"> <?= number_format($product['price'],0,',','.') ?> ₫</div>
              <?php endif; ?>
              <div class="stock-status" style="font-size:1.1rem;color:#444;">Số lượng: <strong><?= (int)$product['stock'] ?></strong></div>
            </div>
            <div class="short-desc" style="font-size:1.13rem;line-height:1.7;color:#444;margin-bottom:18px;"> <?= nl2br(htmlspecialchars($product['description'])) ?> </div>
            <div class="add-to-cart" style="margin-bottom:10px;">
              <form method="post" action="<?= BASE_URL ?>/cart-action.php" style="display:flex;align-items:center;gap:12px;">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>">
                <label style="font-size:1.1rem;">Số lượng <input id="product-quantity" type="number" name="quantity" value="1" min="1" style="width:60px;padding:6px 8px;margin-left:6px;border-radius:8px;border:1.5px solid #e7e7e7;"></label>
                <button class="btn ripple" type="submit" style="padding:16px 44px;border-radius:16px;font-size:1.13rem;background:linear-gradient(90deg,#c6a47e 70%,#e7cba0 100%);color:#fff;font-weight:700;box-shadow:0 2px 12px rgba(198,164,126,0.13);">🛒 Thêm vào giỏ</button>
              </form>
            </div>
          </div>
          <div class="luxury-info-boxes" style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:18px;">
            <div class="info-box" style="background:#f7f7f7;border-radius:14px;padding:18px 16px;box-shadow:0 2px 8px rgba(30,41,59,0.06);">
              <h4 style="font-size:1.1rem;font-weight:700;color:#c6a47e;margin-bottom:10px;">Ưu điểm</h4>
              <ul style="font-size:1rem;color:#444;line-height:1.7;">
                <li>Thiết kế sang trọng, phù hợp phong cách Luxury</li>
                <li>Chất liệu cao cấp, bền đẹp</li>
                <li>Bảo hành 2 năm, hỗ trợ lắp đặt</li>
              </ul>
            </div>
            <div class="info-box" style="background:#f7f7f7;border-radius:14px;padding:18px 16px;box-shadow:0 2px 8px rgba(30,41,59,0.06);">
              <h4 style="font-size:1.1rem;font-weight:700;color:#c6a47e;margin-bottom:10px;">Nhược điểm</h4>
              <ul style="font-size:1rem;color:#444;line-height:1.7;">
                <li>Giá thành cao hơn so với sản phẩm phổ thông</li>
                <li>Kích thước lớn, cần không gian rộng</li>
                <li>Thời gian giao hàng có thể lâu hơn</li>
              </ul>
            </div>
            <div class="info-box" style="background:#f7f7f7;border-radius:14px;padding:18px 16px;box-shadow:0 2px 8px rgba(30,41,59,0.06);">
              <h4 style="font-size:1.1rem;font-weight:700;color:#c6a47e;margin-bottom:10px;">Thông số kỹ thuật</h4>
              <ul style="font-size:1rem;color:#444;line-height:1.7;">
                <li>Kích thước: 220x180x90cm</li>
                <li>Chất liệu: Gỗ óc chó, da thật</li>
                <li>Màu sắc: Tùy chọn</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php
    // Sản phẩm gợi ý cùng danh mục (trừ sản phẩm hiện tại)
    $suggested = [];
    if (!empty($product['category_id'])) {
        $suggestedStmt = $productModel->getByCategory($product['category_id'], 4);
        if ($suggestedStmt && method_exists($suggestedStmt, 'fetchAll')) {
            $suggested = $suggestedStmt->fetchAll();
        } elseif (is_array($suggestedStmt)) {
            $suggested = $suggestedStmt;
        }
        // Loại bỏ sản phẩm hiện tại
        $suggested = array_filter($suggested, function($p) use ($product) { return $p['id'] != $product['id']; });
        // Nếu thiếu thì lấy thêm sản phẩm mới nhất khác danh mục
        if (count($suggested) < 4) {
            $extraStmt = $productModel->getAll(8);
            $extra = is_object($extraStmt) && method_exists($extraStmt, 'fetchAll') ? $extraStmt->fetchAll() : (is_array($extraStmt) ? $extraStmt : []);
            foreach ($extra as $p) {
                if ($p['id'] != $product['id'] && (!isset($p['category_id']) || $p['category_id'] != $product['category_id'])) {
                    $suggested[] = $p;
                    if (count($suggested) >= 4) break;
                }
            }
        }
    }
    ?>
    <?php if (!empty($suggested)): ?>
    <div class="section-title" style="margin:60px 0 32px 0;text-align:center;">
        <h2 style="font-size:2rem;color:#c6a47e;text-transform:uppercase;letter-spacing:1.2px;">Sản phẩm gợi ý</h2>
    </div>
    <div class="products-grid" style="margin-bottom:40px;gap:36px;">
      <?php foreach ($suggested as $sp):
        $spImg = productImageUrl($sp['image'], $sp['slug'] ?? $sp['name']); ?>
        <div class="product-card" style="min-height:370px;padding-bottom:18px;">
          <a href="<?= BASE_URL ?>/product-detail.php?id=<?= $sp['id'] ?>" style="display:block;">
            <div class="product-image" style="height:180px;">
              <img src="<?= $spImg ?>" alt="<?= htmlspecialchars($sp['name']) ?>">
            </div>
          </a>
          <div class="product-info" style="gap:10px;padding:16px 14px 0 14px;line-height:1.6;">
            <div class="product-category" style="margin-bottom:2px;"> <?= htmlspecialchars($sp['category_name'] ?? '') ?> </div>
            <h3 class="product-name" style="min-height:48px;font-size:1.13rem;line-height:1.45;font-weight:700;margin-bottom:2px;">
              <a href="<?= BASE_URL ?>/product-detail.php?id=<?= $sp['id'] ?>" style="color:inherit;"> <?= htmlspecialchars($sp['name']) ?> </a>
            </h3>
            <div class="product-price" style="margin-bottom:4px;">
              <?php if (!empty($sp['sale_price'])): ?>
                <span class="price-current" style="color:#c6a47e;font-weight:700;"> <?= number_format($sp['sale_price'],0,',','.') ?> ₫</span>
                <span class="price-old" style="color:#aaa;text-decoration:line-through;margin-left:6px;"> <?= number_format($sp['price'],0,',','.') ?> ₫</span>
              <?php else: ?>
                <span class="price-current" style="color:#c6a47e;font-weight:700;"> <?= number_format($sp['price'],0,',','.') ?> ₫</span>
              <?php endif; ?>
            </div>
            <a href="<?= BASE_URL ?>/product-detail.php?id=<?= $sp['id'] ?>" class="btn-view" style="margin-top:10px;">Xem chi tiết</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </main>
  <?php include __DIR__ . '/layouts/footer.php'; ?>
  <!-- JS removed per request; site works without JavaScript -->
</body>
</html>
