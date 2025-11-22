<?php
require_once 'core/config.php';
require_once 'core/database_new.php';
require_once 'core/functions.php';
require_once 'models/Product.php';
require_once 'models/Category.php';

$db = new Database();
$productModel = new Product($db);
$categoryModel = new Category($db);

$res = $productModel->getAll();
$products = [];
if ($res) {
    if (is_object($res) && method_exists($res, 'fetchAll')) {
        $products = $res->fetchAll();
    } elseif (is_array($res)) {
        $products = $res;
    }
}

$cartCount = getCartCount();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Tất cả sản phẩm - Furniture</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include __DIR__ . '/views/layouts/header.php'; ?>

<main class="container" id="main" style="padding:28px 16px;">
    <div class="section-title">
        <h2>Tất cả sản phẩm</h2>
        <p>Hiển thị tất cả sản phẩm có trong cơ sở dữ liệu (<?php echo count($products); ?> sản phẩm)</p>
    </div>

    <div class="products-grid">
        <?php if (empty($products)): ?>
            <div>Chưa có sản phẩm nào. Hãy import dữ liệu bằng `database_full_seed.sql` hoặc thêm sản phẩm trong admin.</div>
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
                            <button type="submit" class="product-icon-btn" title="Thêm vào giỏ">🛒</button>
                        </form>
                        <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="product-icon-btn" title="Xem chi tiết">🔍</a>
                    </div>
                </div>
                <div class="product-info">
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
                        <a class="btn-view" href="product-detail.php?id=<?php echo $product['id']; ?>">Xem Chi Tiết</a>
                    </div>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>

</main>

<?php include __DIR__ . '/views/layouts/footer.php'; ?>
</body>
</html>
