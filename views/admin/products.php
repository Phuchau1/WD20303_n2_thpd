<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/database_new.php';
require_once __DIR__ . '/../../models/Product.php';

session_start();
if (empty($_SESSION['admin_user'])){
    header('Location: ' . BASE_URL . '/index.php?page=admin/login'); exit;
}
$db = new Database();
$productModel = new Product($db);

// For simplicity, support delete via GET
if (isset($_GET['action']) && $_GET['action'] === 'delete' && !empty($_GET['id'])){
    $db->execute('DELETE FROM products WHERE id = ?', [(int)$_GET['id']]);
    header('Location: ' . BASE_URL . '/index.php?page=admin/products'); exit;
}

// Ensure 'approved' column exists (safe migration)
$col = $db->queryOne("SHOW COLUMNS FROM products LIKE 'approved'");
if (!$col) {
  // default to 1 (approved) for existing items
  $db->execute("ALTER TABLE products ADD COLUMN approved TINYINT(1) DEFAULT 1 AFTER featured");
}

// Handle admin updates (stock / approved) via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_action']) && $_POST['admin_action'] === 'update_product'){
  $id = (int)($_POST['id'] ?? 0);
  $stock = (int)($_POST['stock'] ?? 0);
  $approved = isset($_POST['approved']) && $_POST['approved'] == '1' ? 1 : 0;
  if ($id){
    $db->execute("UPDATE products SET stock = ?, approved = ? WHERE id = ?", [$stock, $approved, $id]);
  }
  header('Location: ' . BASE_URL . '/index.php?page=admin/products'); exit;
}

$products = $productModel->getAll(200);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Quản lý sản phẩm</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
</head>
<body class="admin">
  <header class="admin-header">
    <div class="container" style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:16px 0;">
      <h1 style="margin:0;font-size:1.4rem;">Sản phẩm</h1>
      <div class="admin-actions">
        <form method="get" action="<?= BASE_URL ?>/index.php" class="admin-search" style="display:flex;align-items:center;gap:8px;">
          <input type="hidden" name="page" value="admin/products">
          <input name="q" placeholder="Tìm theo tên sản phẩm..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" style="padding:8px 12px;border-radius:8px;border:1px solid #e7e7e7;min-width:220px;">
          <button class="btn" type="submit">Tìm</button>
        </form>
        <a class="btn" href="<?= BASE_URL ?>/index.php?page=admin/product-form">Tạo sản phẩm mới</a>
      </div>
    </div>
  </header>

  <main class="admin-main container" style="padding:18px 0 40px;">
    <?php
    // simple search filter on name if q provided
    $query = trim($_GET['q'] ?? '');
    if ($query) {
      $products = array_filter($products, function($p) use ($query){
        return stripos($p['name'] ?? '', $query) !== false;
      });
    }
    ?>

    <div class="admin-grid">
      <?php foreach ($products as $p):
        $img = $p['image'] ? UPLOAD_URL . $p['image'] : BASE_URL . '/assets/images/img/default.png';
      ?>
        <article class="product-card">
          <div class="product-card-media">
            <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
          </div>
          <div class="product-card-body">
            <h3 class="product-title"><?= htmlspecialchars($p['name']) ?></h3>
            <div class="product-meta"><?= htmlspecialchars($p['category_name'] ?? '—') ?></div>
            <div class="product-price"><?= number_format($p['price'],0,',','.') ?> ₫</div>
            <form method="post" class="admin-product-form" style="margin-top:8px;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
              <input type="hidden" name="admin_action" value="update_product">
              <input type="hidden" name="id" value="<?= $p['id'] ?>">
              <label style="display:flex;align-items:center;gap:8px;font-size:0.95rem;">
                Số lượng
                <input type="number" name="stock" value="<?= (int)($p['stock'] ?? 0) ?>" min="0" style="width:88px;padding:6px;border-radius:8px;border:1px solid #e7e7e7">
              </label>
              <label style="display:flex;align-items:center;gap:6px;font-size:0.95rem;">
                Duyệt
                <select name="approved" style="padding:6px;border-radius:8px;border:1px solid #e7e7e7">
                  <option value="1" <?= (isset($p['approved']) && $p['approved']) ? 'selected' : '' ?>>Có</option>
                  <option value="0" <?= (!isset($p['approved']) || !$p['approved']) ? 'selected' : '' ?>>Không</option>
                </select>
              </label>
              <div style="margin-left:auto;display:flex;gap:8px">
                <a class="btn btn-sm" href="<?= BASE_URL ?>/index.php?page=admin/product-form&id=<?= $p['id'] ?>">Sửa</a>
                <button class="btn btn-outline btn-sm" type="submit">Lưu</button>
              </div>
            </form>
            <div style="margin-top:8px;display:flex;gap:8px;">
              <a class="btn btn-outline" href="<?= BASE_URL ?>/index.php?page=admin/products&action=delete&id=<?= $p['id'] ?>" onclick="return confirm('Xóa sản phẩm này?')">Xóa</a>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>

    <?php if (empty($products)): ?>
      <div style="background:#fff;padding:22px;border-radius:12px;border:1px solid #f1ece6;margin-top:18px;text-align:center;">Không tìm thấy sản phẩm.</div>
    <?php endif; ?>

  </main>
</body>
</html>
