<?php
require_once 'core/config.php';
require_once 'core/database_new.php';
require_once 'core/functions.php';
require_once 'models/Product.php';
require_once 'models/Category.php';

$db = new Database();
$productModel = new Product($db);
$categoryModel = new Category($db);

$featuredProducts = $productModel->getFeatured(8);
$categories = $categoryModel->getAll();

// Diagnostic/fallback: if no featured products, try fetching any products and show debug hint
$debugMessages = [];
if (empty($featuredProducts)) {
    $debugMessages[] = 'No featured products found.';
    // Try alternative fetch: getAll may return PDOStatement or array
    $res = $productModel->getAll(8);
    if (is_object($res) && method_exists($res, 'fetchAll')) {
        $rows = $res->fetchAll();
    } elseif (is_array($res)) {
        $rows = $res;
    } else {
        $rows = [];
    }
    if (!empty($rows)) {
        $debugMessages[] = 'Found products via fallback getAll(). Showing them.';
        $featuredProducts = $rows;
    } else {
        $debugMessages[] = 'No products in database. If you have not imported sample data, run import_db.php or import database.sql via phpMyAdmin.';
    }
}
$cartCount = getCartCount();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nội Thất Cao Cấp - Furniture Luxury</title>
    <meta name="description" content="Chuyên cung cấp nội thất cao cấp, hiện đại cho mọi không gian sống">
    <meta property="og:title" content="Nội Thất Cao Cấp - Furniture Luxury">
    <meta property="og:description" content="Chuyên cung cấp nội thất cao cấp, hiện đại">
    <meta property="og:type" content="website">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <a class="skip-link" href="#main">Bỏ qua tới nội dung chính</a>
    <!-- HEADER -->
    <?php include __DIR__ . '/views/layouts/header.php'; ?>

    <main id="main">

    <!-- HERO SECTION -->
    <section class="hero" style="background: url('assets/images/banner2.jpg') center/cover no-repeat;">
        <div class="container hero-content">
            <h1>Nội Thất Cao Cấp Cho Không Gian Thượng Lưu</h1>
            <p>Chọn lọc sản phẩm thiết kế, chất lượng và bền bỉ — biến ngôi nhà của bạn thành tổ ấm đáng mơ ước.</p>
            <div style="margin-top:18px; display:flex; gap:12px; flex-wrap:wrap;">
                <a href="products.php" class="btn" style="background:#ff6b35;">Xem Bộ Sưu Tập</a>
                <a href="views/cart.php" class="btn btn-ghost" style="color:#fff; border-color: rgba(255,255,255,0.85);">Mua Ngay</a>
            </div>
        </div>
    </section>

    <!-- CATEGORIES STRIP -->
    <section class="section" style="padding: 28px 0 40px;">
        <div class="container">
            <div class="section-title reveal-on-scroll">
                <h2>Danh Mục Sản Phẩm</h2>
                <p>Chọn theo không gian hoặc phong cách</p>
            </div>
            <div class="categories-grid reveal-on-scroll" style="margin-top:18px;">
                <?php if (!empty($categories)): foreach ($categories as $cat): ?>
                <?php
                    $catImg = productImageUrl($cat['image'] ?? '', $cat['slug'] ?? null);
                    $slug = isset($cat['slug']) && $cat['slug'] !== '' ? $cat['slug'] : 'category-' . (int)$cat['id'];
                    $roomPages = [
                        'phong-an' => 'phong-an.php',
                        'phong-ngu' => 'phong-ngu.php',
                        'phong-bep' => 'phong-bep.php',
                        'phong-khach' => 'phong-khach.php',
                        'phong-lam-viec' => 'phong-lam-viec.php',
                    ];
                    if (isset($roomPages[$slug])) {
                        $url = BASE_URL . '/' . $roomPages[$slug];
                    } else {
                        $url = BASE_URL . '/products.php?category=' . urlencode($slug);
                    }
                ?>
                <a href="<?php echo $url; ?>" class="category-card" aria-label="Xem sản phẩm danh mục <?php echo e($cat['name']); ?>">
                    <img src="<?php echo $catImg; ?>" alt="<?php echo e($cat['name']); ?>">
                    <div class="category-overlay">
                        <div class="category-content">
                            <h3 class="category-title"><?php echo e($cat['name']); ?></h3>
                            <p class="category-sub">Xem sản phẩm</p>
                        </div>
                    </div>
                </a>
                <?php endforeach; else: ?>
                <div>Chưa có danh mục. Hãy thêm danh mục trong admin hoặc import dữ liệu.</div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- GIẢM GIÁ SECTION -->
    <section class="section" style="background: #F5F5F5;">
        <div class="container">
            <div class="section-title reveal-on-scroll">
                <h2>Sản Phẩm Giảm Giá</h2>
                <p>Ưu đãi đặc biệt cho các sản phẩm hot nhất</p>
            </div>
            <div class="products-grid two-cols">
                <?php
                $saleProducts = array_filter($featuredProducts, function($p){ return !empty($p['sale_price']) && $p['sale_price'] < $p['price']; });
                $saleProducts = array_slice($saleProducts,0,4);
                foreach ($saleProducts as $index => $product):
                    $discount = round((($product['price'] - $product['sale_price']) / $product['price']) * 100);
                ?>
                <div class="product-card" style="transition-delay: <?php echo $index * 0.1; ?>s">
                    <div class="product-image">
                        <?php $mainImg = productImageUrl($product['image'], $product['slug'] ?? null); ?>
                        <img src="<?php echo $mainImg; ?>" alt="<?php echo e($product['name']); ?>" onerror="this.src='https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400&h=400&fit=crop'">
                        <span class="product-badge">-<?php echo $discount; ?>%</span>
                        <div class="product-icons">
                            <form method="post" action="<?= BASE_URL ?>/views/cart.php?action=add" class="inline-form">
                                <form method="post" action="<?= BASE_URL ?>/cart-action.php" class="inline-form">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="product-icon-btn" title="Thêm vào giỏ"><span>🛒</span></button>
                                </form>
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="product-icon-btn" title="Xem chi tiết"><span>🔍</span></a>
                        </div>
                    </div>
                    <div class="product-info">
                        <div class="product-category">Giảm giá</div>
                        <h3 class="product-name"><?php echo e($product['name']); ?></h3>
                        <div class="product-price">
                            <span class="price-current"><?php echo formatPrice($product['sale_price']); ?></span>
                            <span class="price-old"><?php echo formatPrice($product['price']); ?></span>
                        </div>
                        <div class="product-actions">
                            <form method="post" action="<?= BASE_URL ?>/views/cart.php?action=add" class="inline-form">
                                <form method="post" action="<?= BASE_URL ?>/cart-action.php" class="inline-form">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn-add-cart">Thêm Giỏ Hàng</button>
                                </form>
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn-view">Xem Chi Tiết</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- NỔI BẬT/ƯU ĐÃI SECTION -->
    <section class="section">
        <div class="container">
            <div class="section-title reveal-on-scroll">
                <h2>Sản Phẩm Ưu Đãi</h2>
                <p>Những sản phẩm được khách hàng yêu thích nhất</p>
            </div>
            <div class="products-grid two-cols">
                <?php
                $hotProducts = array_filter($featuredProducts, function($p){ return !empty($p['featured']); });
                $hotProducts = array_slice($hotProducts,0,4);
                foreach ($hotProducts as $index => $product):
                ?>
                <div class="product-card" style="transition-delay: <?php echo $index * 0.1; ?>s">
                    <div class="product-image">
                        <?php $mainImg = productImageUrl($product['image'], $product['slug'] ?? null); ?>
                        <img src="<?php echo $mainImg; ?>" alt="<?php echo e($product['name']); ?>" onerror="this.src='https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400&h=400&fit=crop'">
                        <span class="product-badge">Nổi bật</span>
                        <div class="product-icons">
                            <form method="post" action="<?= BASE_URL ?>/views/cart.php?action=add" class="inline-form">
                                <form method="post" action="<?= BASE_URL ?>/cart-action.php" class="inline-form">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="product-icon-btn" title="Thêm vào giỏ"><span>🛒</span></button>
                                </form>
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="product-icon-btn" title="Xem chi tiết"><span>🔍</span></a>
                        </div>
                    </div>
                    <div class="product-info">
                        <div class="product-category">Nổi bật</div>
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
                                <form method="post" action="<?= BASE_URL ?>/cart-action.php" class="inline-form">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn-add-cart">Thêm Giỏ Hàng</button>
                                </form>
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn-view">Xem Chi Tiết</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ĐỀ XUẤT SECTION -->
    <section class="section" style="background: #F5F5F5;">
        <div class="container">
            <div class="section-title reveal-on-scroll">
                <h2>Sản Phẩm Đề Xuất</h2>
                <p>Gợi ý dành riêng cho bạn</p>
            </div>
            <div class="products-grid two-cols">
                <?php
                $suggestProducts = array_slice($featuredProducts,0,4);
                foreach ($suggestProducts as $index => $product):
                ?>
                <div class="product-card" style="transition-delay: <?php echo $index * 0.1; ?>s">
                    <div class="product-image">
                        <?php $mainImg = productImageUrl($product['image'], $product['slug'] ?? null); ?>
                        <img src="<?php echo $mainImg; ?>" alt="<?php echo e($product['name']); ?>" onerror="this.src='https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400&h=400&fit=crop'">
                        <span class="product-badge">Đề xuất</span>
                        <div class="product-icons">
                            <form method="post" action="<?= BASE_URL ?>/views/cart.php?action=add" class="inline-form">
                                <form method="post" action="<?= BASE_URL ?>/cart-action.php" class="inline-form">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="product-icon-btn" title="Thêm vào giỏ"><span>🛒</span></button>
                                </form>
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="product-icon-btn" title="Xem chi tiết"><span>🔍</span></a>
                        </div>
                    </div>
                    <div class="product-info">
                        <div class="product-category">Đề xuất</div>
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
                                <form method="post" action="<?= BASE_URL ?>/cart-action.php" class="inline-form">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn-add-cart">Thêm Giỏ Hàng</button>
                                </form>
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn-view">Xem Chi Tiết</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <!-- (Removed duplicate sale section to avoid repeated product blocks) -->

    <!-- NỔI BẬT SECTION -->
    <section class="section">
        <div class="container">
            <div class="section-title reveal-on-scroll">
                <h2>Sản Phẩm Nổi Bật</h2>
                <p>Những sản phẩm được khách hàng yêu thích nhất</p>
            </div>
            <div class="products-grid">
                <?php
                $hotProducts = array_filter($featuredProducts, function($p){ return !empty($p['featured']); });
                $hotProducts = array_slice($hotProducts,0,4);
                foreach ($hotProducts as $index => $product):
                ?>
                <div class="product-card" style="transition-delay: <?php echo $index * 0.1; ?>s">
                    <div class="product-image">
                        <img src="<?php echo productImageUrl($product['image'], $product['slug'] ?? null); ?>" alt="<?php echo e($product['name']); ?>" onerror="this.src='https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400&h=400&fit=crop'">
                        <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                        <span class="product-badge">Nổi bật</span>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <div class="product-category"><?php echo e($product['category_name']); ?></div>
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
                                <form method="post" action="<?= BASE_URL ?>/cart-action.php" class="inline-form">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn-add-cart">Thêm Giỏ Hàng</button>
                                </form>
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn-view">Xem Chi Tiết</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ƯU ĐÃI ĐẶC BIỆT SECTION -->
    <section class="section" style="background: #F5F5F5;">
        <div class="container">
            <div class="section-title reveal-on-scroll">
                <h2>Ưu Đãi Đặc Biệt</h2>
                <p>Chỉ có tại Furniture Luxury, số lượng có hạn!</p>
            </div>
            <div class="products-grid">
                <?php
                $specialProducts = array_slice($featuredProducts, 0, 4);
                foreach ($specialProducts as $index => $product):
                ?>
                <div class="product-card" style="transition-delay: <?php echo $index * 0.1; ?>s">
                    <div class="product-image">
                        <img src="<?php echo productImageUrl($product['image'], $product['slug'] ?? null); ?>" alt="<?php echo e($product['name']); ?>" onerror="this.src='https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400&h=400&fit=crop'">
                        <span class="product-badge">Ưu đãi</span>
                    </div>
                    <div class="product-info">
                        <div class="product-category"><?php echo e($product['category_name']); ?></div>
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
                            <a href="product-detail.php?id=<?php echo $product['id']; ?>" class="btn-view">Xem Chi Tiết</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ABOUT SECTION -->
    <section class="section" id="about">
        <div class="container">
            <div class="section-title">
                <h2>Về Chúng Tôi</h2>
                <p>Furniture Luxury - Đẳng cấp trong từng chi tiết</p>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;">
                <div>
                    <img src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?w=800&h=600&fit=crop" alt="About Us" style="border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
                </div>
                <div>
                    <h3 style="font-size: 32px; margin-bottom: 20px;">15 Năm Kinh Nghiệm</h3>
                    <p style="font-size: 16px; line-height: 1.8; margin-bottom: 15px;">
                        Furniture Luxury tự hào là đơn vị hàng đầu trong lĩnh vực cung cấp nội thất cao cấp tại Việt Nam. 
                        Với hơn 15 năm kinh nghiệm, chúng tôi luôn mang đến những sản phẩm chất lượng nhất từ các thương hiệu uy tín thế giới.
                    </p>
                    <p style="font-size: 16px; line-height: 1.8; margin-bottom: 30px;">
                        Chúng tôi không chỉ bán nội thất, mà còn tư vấn thiết kế và thi công trọn gói, 
                        giúp bạn biến ước mơ về ngôi nhà lý tưởng thành hiện thực.
                    </p>
                    <a href="#contact" class="btn">Liên Hệ Ngay</a>
                </div>
            </div>
        </div>
    </section>

    </main>

    <!-- FOOTER -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Furniture Luxury</h3>
                    <p>Nội thất cao cấp cho mọi không gian sống</p>
                    <p style="margin-top: 20px;">
                        📍 123 Nguyễn Huệ, Q.1, TP.HCM<br>
                        📞 0901 234 567<br>
                        ✉️ info@furnitureluxury.vn
                    </p>
                </div>
                
                <div class="footer-section">
                    <h3>Về Chúng Tôi</h3>
                    <ul>
                        <li><a href="#">Giới thiệu</a></li>
                        <li><a href="#">Tuyển dụng</a></li>
                        <li><a href="#">Tin tức</a></li>
                        <li><a href="#">Liên hệ</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Chính Sách</h3>
                    <ul>
                        <li><a href="#">Chính sách bảo hành</a></li>
                        <li><a href="#">Chính sách đổi trả</a></li>
                        <li><a href="#">Chính sách vận chuyển</a></li>
                        <li><a href="#">Chính sách bảo mật</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Hỗ Trợ Khách Hàng</h3>
                    <ul>
                        <li><a href="#">Hướng dẫn đặt hàng</a></li>
                        <li><a href="#">Hướng dẫn thanh toán</a></li>
                        <li><a href="#">Câu hỏi thường gặp</a></li>
                        <li><a href="#">Tư vấn miễn phí</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2025 Furniture Luxury. All rights reserved. Designed with ❤️</p>
            </div>
        </div>
    </footer>

   
</body>
</html>