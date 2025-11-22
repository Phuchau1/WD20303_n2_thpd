<?php
// List products grouped by category and export to CSV
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

// Fetch categories
$catStmt = $pdo->query("SELECT id, name, slug FROM categories ORDER BY id ASC");
$categories = $catStmt->fetchAll();

$outDir = __DIR__ . '/../exports';
if (!is_dir($outDir)) {
    mkdir($outDir, 0755, true);
}
$outCsv = $outDir . '/products_by_category.csv';
$fp = fopen($outCsv, 'w');
if (!$fp) {
    echo "Failed to create CSV: $outCsv" . PHP_EOL;
    exit(1);
}

// CSV header
$header = [
    'category_id', 'category_slug', 'category_name',
    'product_id','product_name','product_slug','price','sale_price','stock','featured','image','description','created_at'
];
fputcsv($fp, $header);

$totalProducts = 0;

foreach ($categories as $cat) {
    $pStmt = $pdo->prepare("SELECT * FROM products WHERE category_id = ? ORDER BY id ASC");
    $pStmt->execute([$cat['id']]);
    $products = $pStmt->fetchAll();
    $count = count($products);
    $totalProducts += $count;

    // Print header for console
    echo "Category: {$cat['name']} ({$cat['slug']}) - {$count} products" . PHP_EOL;

    foreach ($products as $p) {
        $row = [
            $cat['id'],
            $cat['slug'],
            $cat['name'],
            $p['id'],
            $p['name'],
            $p['slug'],
            $p['price'],
            $p['sale_price'],
            $p['stock'],
            $p['featured'],
            $p['image'],
            $p['description'],
            $p['created_at']
        ];
        fputcsv($fp, $row);
    }
}

fclose($fp);

echo PHP_EOL . "Exported total: $totalProducts products to $outCsv" . PHP_EOL;

// Print short sample (first 10 rows) for quick inspection
$sampleStmt = $pdo->query("SELECT p.id, p.name, p.slug, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id ASC LIMIT 10");
$samples = $sampleStmt->fetchAll();

echo PHP_EOL . "Sample (first 10 products):" . PHP_EOL;
foreach ($samples as $s) {
    echo "[{$s['id']}] {$s['name']} (slug: {$s['slug']}) - Category: {$s['category_name']}" . PHP_EOL;
}

?>