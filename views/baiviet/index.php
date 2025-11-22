
<?php
require_once __DIR__ . '/../../core/config.php';
require_once __DIR__ . '/../../core/database_new.php';
$posts = [];
$dbError = null;
try {
    $db = new Database();
    $posts = $db->query('SELECT * FROM posts ORDER BY created_at DESC')->fetchAll();
} catch (Exception $e) {
    $dbError = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài Viết Nội Thất</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <?php include __DIR__ . '/../layouts/header.php'; ?>
    <main class="container" style="padding:40px 0 60px 0;">
        <div class="section-title">
            <h2>Bài Viết Nội Thất</h2>
            <p>Chia sẻ kiến thức, xu hướng và mẹo hay về nội thất</p>
        </div>
        <?php if ($dbError): ?>
            <div style="color:#c00;text-align:center;font-size:1.3rem;margin:60px 0 80px 0;">
                <b>Lỗi kết nối database:</b><br><?php echo htmlspecialchars($dbError); ?><br>
                <span style="color:#888;font-size:1rem;">Vui lòng kiểm tra cấu hình hoặc import lại dữ liệu.</span>
            </div>
        <?php elseif (empty($posts)): ?>
            <div style="color:#c00;text-align:center;font-size:1.3rem;margin:60px 0 80px 0;">
                Không có bài viết nào trong database.<br>
                <span style="color:#888;font-size:1rem;">Hãy kiểm tra lại dữ liệu bảng <b>posts</b>.</span>
            </div>
        <?php else: ?>
        <div class="products-grid">
            <?php foreach ($posts as $post): ?>
            <div class="product-card" style="min-height:420px;display:flex;flex-direction:column;justify-content:space-between;">
                <a href="post-template.php?slug=<?php echo $post['slug']; ?>" style="display:block;">
                    <div class="product-image" style="height:200px;">
                        <img src="<?php echo $post['image']; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                    </div>
                </a>
                <div class="product-info" style="gap:8px;">
                    <div class="product-category">Bài viết</div>
                    <h3 class="product-name" style="min-height:48px;font-size:1.18rem;line-height:1.3;font-weight:700;">
                        <a href="post-template.php?slug=<?php echo $post['slug']; ?>" style="color:inherit;"> <?php echo htmlspecialchars($post['title']); ?> </a>
                    </h3>
                    <p style="color:#555;font-size:15px;min-height:48px;"> 
                        <?php echo isset($post['excerpt']) ? htmlspecialchars($post['excerpt']) : mb_substr(strip_tags($post['content']),0,80).'...'; ?> 
                    </p>
                    <a href="post-template.php?slug=<?php echo $post['slug']; ?>" class="btn-view" style="margin-top:10px;">Xem chi tiết</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </main>
    <?php include __DIR__ . '/../layouts/footer.php'; ?>
</body>
</html>
