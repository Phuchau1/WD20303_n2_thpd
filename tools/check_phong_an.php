<?php
require_once __DIR__ . '/../core/config.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (Exception $e) {
    echo "DB connection failed: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

echo "Checking category slug 'phong-an'...\n";
$stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
$stmt->execute(['phong-an']);
$cat = $stmt->fetch();
if (!$cat) {
    echo "Category 'phong-an' not found in table categories.\n";
    echo "Existing categories:\n";
    $all = $pdo->query("SELECT id, name, slug FROM categories ORDER BY id")->fetchAll();
    foreach ($all as $c) {
        echo "- ({$c['id']}) {$c['name']} -> slug={$c['slug']}\n";
    }
    exit(0);
}

echo "Found category: id={$cat['id']}, name={$cat['name']}, slug={$cat['slug']}\n";

// Count products
$stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM products WHERE category_id = ?");
$stmt->execute([(int)$cat['id']]);
$row = $stmt->fetch();
$cnt = $row ? (int)$row['cnt'] : 0;
echo "Products assigned to category_id={$cat['id']}: $cnt\n";

if ($cnt > 0) {
    echo "Listing up to 10 sample products for this category:\n";
    $s = $pdo->prepare("SELECT id, name, slug, price, sale_price, image FROM products WHERE category_id = ? ORDER BY id LIMIT 10");
    $s->execute([(int)$cat['id']]);
    $samples = $s->fetchAll();
    foreach ($samples as $p) {
        echo "- ({$p['id']}) {$p['name']} | slug={$p['slug']} | price={$p['price']} | sale={$p['sale_price']} | image={$p['image']}\n";
    }
} else {
    echo "No products found for this category. Possible causes:\n";
    echo "- Seed data not imported. Run the SQL seed file.\n";
    echo "- products table uses different category IDs than expected.\n";
    echo "- Wrong database configured in core/config.php (DB_NAME).\n";
}

// Extra: check DB name and connection info
echo "\nDB connection info: host=".DB_HOST." dbname=".DB_NAME." user=".DB_USER."\n";

echo "Done.\n";
?>