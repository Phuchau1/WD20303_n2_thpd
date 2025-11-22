<?php
// Shared header with categories dropdown
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/database_new.php';
require_once __DIR__ . '/../../core/functions.php';
require_once __DIR__ . '/../../models/Category.php';

$db = new Database();
$catModel = new Category($db);
$categories = $catModel->getAll();
?>
<!doctype html>
<!-- Shared header -->
<header class="header">
  <div class="container header-content">
    <a class="logo" href="<?= BASE_URL ?>">Furniture <span>Shop</span></a>

    <nav>
      <ul class="nav-menu">
        <li><a href="<?= BASE_URL ?>">Trang chủ</a></li>
        <li class="nav-item has-dropdown">
          <a href="<?= BASE_URL ?>/products.php">Sản phẩm ▾</a>
          <div class="dropdown">
              <?php
              // Use the slug stored in DB to build links. Map known room slugs to dedicated pages,
              // otherwise link to the product listing filtered by category slug.
              $roomPages = [
                  'phong-an' => 'phong-an.php',
                  'phong-ngu' => 'phong-ngu.php',
                  'phong-bep' => 'phong-bep.php',
                  'phong-khach' => 'phong-khach.php',
                  'phong-lam-viec' => 'phong-lam-viec.php',
              ];
              foreach ($categories as $c):
                  $slug = isset($c['slug']) && $c['slug'] !== '' ? $c['slug'] : 'category-' . (int)$c['id'];
                    if (isset($roomPages[$slug])) {
                      // Point to the file under /views/ so header links open the exact view file
                      $url = BASE_URL . '/views/' . $roomPages[$slug];
                    } else {
                      $url = BASE_URL . '/products.php?category=' . urlencode($slug);
                    }
              ?>
                <a class="dropdown-item" href="<?php echo $url; ?>" aria-label="Xem sản phẩm danh mục <?php echo e($c['name']); ?>"><?php echo e($c['name']); ?></a>
              <?php endforeach; ?>
          </div>
        </li>
        <li><a href="<?= BASE_URL ?>/posts.php">Bài viết</a></li>
        <li><a href="<?= BASE_URL ?>/contact.php">Liên hệ</a></li>
      </ul>
    </nav>

    <div class="header-actions">
      <form class="search-box" action="<?= BASE_URL ?>/index.php" method="get">
        <input type="hidden" name="page" value="products">
        <input name="q" placeholder="Tìm kiếm..." value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>">
        <button type="submit">Tìm</button>
      </form>
      <a class="icon-btn cart-btn" href="<?= BASE_URL ?>/views/cart.php" title="Giỏ hàng">
        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h7.72a2 2 0 0 0 2-1.61l1.38-7.39H6"/></svg>
        <span class="cart-count"><?php echo isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'],'quantity')) : 0; ?></span>
      </a>
      <a class="icon-btn fav-btn" href="<?= BASE_URL ?>/favorites.php" title="Yêu thích">
        <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
      </a>
      <?php if (!empty($_SESSION['user'])): ?>
        <div class="user-menu">
          <a class="user-name" href="<?= BASE_URL ?>/views/account.php" title="Tài khoản">Hi, <?= htmlspecialchars($_SESSION['user']['name'] ?? ($_SESSION['user']['email'] ?? 'Bạn')) ?></a>
          <a class="user-logout" href="<?= BASE_URL ?>/login.php?logout=1" title="Đăng xuất">Đăng xuất</a>
        </div>
      <?php else: ?>
        <a class="icon-btn login-btn" href="<?= BASE_URL ?>/login.php" title="Đăng nhập">
          <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M6 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/></svg>
        </a>
      <?php endif; ?>
      <a class="admin-link" href="<?= BASE_URL ?>/index.php?page=admin/login">Admin</a>
    </div>
  </div>
</header>
<?php
// Show flash message (one-time) if set in session
if (session_status() === PHP_SESSION_NONE) session_start();
if (!empty($_SESSION['flash_message'])):
  $msg = $_SESSION['flash_message'];
  unset($_SESSION['flash_message']);
?>
  <div class="flash-message" role="status" aria-live="polite"><?= e($msg) ?></div>
<?php
endif;

