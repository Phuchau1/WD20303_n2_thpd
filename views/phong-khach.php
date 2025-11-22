<?php
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/database_new.php';
require_once __DIR__ . '/../core/functions.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';

$db = new Database();
$productModel = new Product($db);
$categoryModel = new Category($db);

// Resolve the category by slug 'phong-bep'
$category = $categoryModel->getBySlug('phong-khach');
if (!$category) {
  http_response_code(404);
  echo "<h1>Danh mục 'Phòng Bếp' không tìm thấy</h1>";
  exit;
}
$category_id = (int)$category['id'];

$perPage = 10;
$page = isset($_GET['p']) ? max(1,(int)$_GET['p']) : 1;
$offset = ($page - 1) * $perPage;
$total = $productModel->countByCategory($category_id);
$maxTotal = 20;
$displayTotal = min((int)$total, $maxTotal);
$pages = (int)ceil($displayTotal / $perPage);
if ($pages < 1) $pages = 1;
if ($offset >= $displayTotal) {
  $page = $pages;
  $offset = ($page - 1) * $perPage;
}
$limitFetch = ($displayTotal - $offset) > 0 ? min($perPage, $displayTotal - $offset) : 0;
$productsStmt = $limitFetch > 0 ? $productModel->getByCategory($category_id, $limitFetch, $offset) : [];
$products = [];
if ($productsStmt) {
  if (is_object($productsStmt) && method_exists($productsStmt, 'fetchAll')) {
    $products = $productsStmt->fetchAll();
  } elseif (is_array($productsStmt)) {
    $products = $productsStmt;
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Phòng Ăn - Sản phẩm</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
  <!DOCTYPE html>
  <html lang="vi">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phòng Khách - Nội Thất Cao Cấp</title>
    <meta name="description" content="Nội thất phòng khách cao cấp, hiện đại, sang trọng">
    <meta property="og:title" content="Phòng Khách - Nội Thất Cao Cấp">
    <meta property="og:description" content="Nội thất phòng khách cao cấp, hiện đại, sang trọng">
    <meta property="og:type" content="website">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
  </head>
  <body>
    <a class="skip-link" href="#main">Bỏ qua tới nội dung chính</a>
    <?php include __DIR__ . '/layouts/header.php'; ?>
  <main class="container" style="padding:28px 16px;">
    <div class="section-title">
      <h2>Phòng Ăn</h2>
      
      
    </div>

    <div class="products-grid">
      <?php if (empty($products)): ?>
        <p>Chưa có sản phẩm trong danh mục này.</p>
      <?php else: foreach ($products as $product): ?>
        <div class="product-card">
          <div class="product-image">
            <img src="<?php echo productImageUrl($product['image'], $product['slug'] ?? null); ?>" alt="<?php echo e($product['name']); ?>">
            <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']):
              $discount = round((($product['price'] - $product['sale_price'])/$product['price'])*100);
            ?>
              <span class="product-badge">-<?php echo $discount; ?>%</span>
            <?php endif; ?>
            <div class="product-icons">
              <form method="post" action="<?= BASE_URL ?>/views/cart.php?action=add" class="inline-form">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="product-icon-btn" title="Thêm vào giỏ"><span>🛒</span></button>
              </form>
              <a href="<?= BASE_URL ?>/product-detail.php?id=<?php echo $product['id']; ?>" class="product-icon-btn" title="Xem chi tiết"><span>🔍</span></a>
            </div>
          </div>
          <div class="product-info">
            <?php if (!empty($product['category_name'])): ?>
              <div class="product-category"><?php echo e($product['category_name']); ?></div>
            <?php endif; ?>
            <h3 class="product-name"><?php echo e($product['name']); ?></h3>
            <div class="product-price">
              <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                <span class="price-current"><?php echo formatPrice($product['sale_price']); ?></span>
                <span class="price-old"><?php echo formatPrice($product['price']); ?></span>
              <?php else: ?>
                <span class="price-current"><?php echo formatPrice($product['price']); ?></span>
              <?php endif; ?>
            </div>
            <div class="product-actions">
              <form method="post" action="<?= BASE_URL ?>/views/cart.php?action=add" class="inline-form">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn-add-cart">Thêm Giỏ Hàng</button>
              </form>
              <a href="<?= BASE_URL ?>/product-detail.php?id=<?php echo $product['id']; ?>" class="btn-view">Xem Chi Tiết</a>
            </div>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>


    <?php if ($total > $perPage): ?>
      <nav class="pagination-wrap">
        <ul class="pagination">
          <?php $pages = (int)ceil($total / $perPage); ?>
          <?php if ($page > 1): ?>
            <li><a href="?p=<?php echo $page-1 ?>" class="page-link">« Trước</a></li>
          <?php endif; ?>
          <?php for ($i=1;$i<=$pages;$i++): ?>
            <li><a href="?p=<?php echo $i ?>" class="page-link<?php if ($i==$page) echo ' active'; ?>"><?php echo $i ?></a></li>
          <?php endfor; ?>
          <?php if ($page < $pages): ?>
            <li><a href="?p=<?php echo $page+1 ?>" class="page-link">Tiếp »</a></li>
          <?php endif; ?>
        </ul>
      </nav>
    <?php endif; ?>

    <!-- PHẦN GIỚI THIỆU HIỆN ĐẠI CUỐI TRANG -->
    <section class="section about-room" style="background: linear-gradient(90deg, #f5f5f5 60%, #fbeee6 100%); margin-top: 60px;">
      <div class="container" style="display: flex; flex-wrap: wrap; align-items: center; gap: 40px;">
        <div style="flex:1 1 320px; min-width:260px;">
          <img src="https://images.unsplash.com/photo-1519710164239-da123dc03ef4?w=600&h=400&fit=crop" alt="Phòng khách hiện đại" style="width:100%; border-radius:18px; box-shadow:0 8px 32px rgba(198,164,126,0.13);">
        </div>
        <div style="flex:2 1 400px; min-width:260px;">
          <h2 style="font-size:2.2rem; color:#c6a47e; font-family:var(--font-heading); margin-bottom:18px;">Không Gian Phòng Khách Hiện Đại</h2>
          <p style="font-size:1.15rem; color:#333; margin-bottom:18px;">Phòng khách là bộ mặt của ngôi nhà. Chúng tôi mang đến các sản phẩm nội thất phòng khách sang trọng, hiện đại, tạo điểm nhấn cho không gian sống của bạn.</p>
          <ul style="font-size:1rem; color:#555; margin-bottom:18px; line-height:1.8;">
            <li>Sofa, bàn trà, kệ tivi đa dạng</li>
            <li>Chất liệu cao cấp, bền đẹp</li>
            <li>Thiết kế tối ưu thẩm mỹ</li>
            <li>Bảo hành chính hãng, giao hàng tận nơi</li>
          </ul>
          <a href="/index.php#about" class="btn" style="margin-top:10px;">Tìm Hiểu Thêm</a>
        </div>
      </div>
    </section>

  </main>
  <?php include __DIR__ . '/layouts/footer.php'; ?>
</body>
</html>
