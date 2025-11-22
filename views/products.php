<?php
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/database_new.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

$db = new Database();
$productModel = new Product($db);
$categoryModel = new Category($db);

// Accept either numeric category_id or a category slug via `category`
$category_id = null;
if (isset($_GET['category_id']) && $_GET['category_id'] !== '') {
  $category_id = (int)$_GET['category_id'];
} elseif (isset($_GET['category']) && $_GET['category'] !== '') {
  // try to resolve slug to id
  $cat = $categoryModel->getBySlug($_GET['category']);
  if ($cat) $category_id = (int)$cat['id'];
}

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($q) {
    $products = $productModel->search($q, $category_id);
} elseif ($category_id) {
  $products = $productModel->getByCategory($category_id);
} else {
    $products = $productModel->getAll(0);
}
$categories = $categoryModel->getAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Sản phẩm</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
  <?php include __DIR__ . '/../layouts/header.php'; ?>
  <main class="container">
    <h2 style="padding-top:100px">Sản phẩm</h2>
    <div style="display:flex;gap:20px;margin-top:20px">
      <form method="get" style="flex:1">
        <input type="hidden" name="page" value="products">
        <select name="category_id">
          <option value="">-- Danh mục --</option>
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($category_id && $category_id==$c['id'])?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
        <input type="text" name="q" placeholder="Tìm..." value="<?= htmlspecialchars($q) ?>">
        <button class="btn" type="submit">Áp dụng</button>
      </form>
    </div>

    <div class="products-grid two-cols">
      <?php $count = 0; ?>
      <?php foreach ($products as $p): ?>
        <?php
          if ($category_id && $count >= 10) break;
          // resolve image URL (supports remote URLs)
          $img = productImageUrl($p['image'], $p['slug'] ?? $p['name']);
          $hoverUrl = '';
          if (!empty($p['image'])) {
            $orig = $p['image'];
            $info = pathinfo($orig);
            $candidates = [
              $info['filename'] . '-2.' . $info['extension'],
              $info['filename'] . '_2.' . $info['extension'],
              $info['filename'] . '-hover.' . $info['extension']
            ];
            foreach ($candidates as $cand) {
              if (file_exists(UPLOAD_DIR . $cand)) {
                $hoverUrl = UPLOAD_URL . $cand;
                break;
              }
            }
          }
          $count++;
        ?>
        <div class="product-card">
          <div class="product-image">
            <img src="<?= $img ?>" alt="<?= htmlspecialchars($p['name']) ?>" <?= $hoverUrl ? 'data-hover="'.$hoverUrl.'"' : '' ?> onerror="this.src='https://via.placeholder.com/600x400?text=No+Image'">
            <?php if ($hoverUrl): ?>
              <img class="hover-img" src="<?= $hoverUrl ?>" alt="">
            <?php endif; ?>
            <div class="product-icons">
              <a href="<?= BASE_URL ?>/product-detail.php?id=<?= $p['id'] ?>" class="icon-btn" title="Xem chi tiết">🔍</a>
              <form method="post" action="<?= BASE_URL ?>/cart-action.php" style="display:inline">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="icon-btn" title="Thêm vào giỏ" style="background:none;border:none;padding:0;cursor:pointer;">🛒</button>
              </form>
            </div>
            <?php if (!empty($p['badge'])): ?>
              <span class="product-badge badge-<?= $p['badge'] ?>"><?= htmlspecialchars($p['badge']) ?></span>
            <?php endif; ?>
          </div>
          <div class="product-info">
            <h3 class="product-name" title="<?= htmlspecialchars($p['name']) ?>">
              <?= htmlspecialchars($p['name']) ?>
            </h3>
            <div class="product-price">
              <?php if (!empty($p['sale_price']) && $p['sale_price'] < $p['price']): ?>
                <span class="price-discounted"><?= number_format($p['sale_price']) ?>₫</span>
                <span class="price-original"><?= number_format($p['price']) ?>₫</span>
              <?php else: ?>
                <span class="price-normal"><?= number_format($p['price']) ?>₫</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </main>
  <!-- JS removed per request; site works without JavaScript -->
</body>
</html>
