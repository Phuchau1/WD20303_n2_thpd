<?php
// Export all products to CSV using existing DB config
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

$sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        ORDER BY p.id ASC";

$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll();

$outDir = __DIR__ . '/../exports';
if (!is_dir($outDir)) {
    mkdir($outDir, 0755, true);
}
$outPath = $outDir . '/products_all.csv';
$fp = fopen($outPath, 'w');
if (!$fp) {
    echo "Failed to open output file: $outPath" . PHP_EOL;
    exit(1);
}

// Header
$headers = [
    'category_id', 'category_slug', 'category_name',
    'id', 'name', 'slug', 'price', 'sale_price', 'description', 'image', 'stock', 'featured', 'created_at'
];
fputcsv($fp, $headers);

foreach ($rows as $r) {
    $line = [
        $r['category_id'],
        $r['category_slug'],
        $r['category_name'],
        $r['id'],
        $r['name'],
        $r['slug'],
        $r['price'],
        $r['sale_price'],
        $r['description'],
        $r['image'],
        $r['stock'],
        $r['featured'],
        $r['created_at']
    ];
    fputcsv($fp, $line);
}

fclose($fp);

echo "Exported " . count($rows) . " products to: $outPath" . PHP_EOL;
echo "You can open the CSV or download it from your server." . PHP_EOL;

// Also print a short SQL query that lists all products
echo PHP_EOL . "SQL used: SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p LEFT JOIN categories c ON p.category_id = c.id;" . PHP_EOL;

?>