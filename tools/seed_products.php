<?php
/**
 * Simple seeder to add sample categories and products (with remote images)
 * Run: php tools/seed_products.php
 */
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/database_new.php';

$db = new Database();

// Categories we want to ensure exist
$categories = [
    'Phòng ăn',
    'Phòng ngủ',
    'Phòng bếp',
    'Phòng khách',
    'Phòng làm việc'
];

// Insert categories if not present
foreach ($categories as $name) {
    $exists = $db->queryOne('SELECT * FROM categories WHERE name = ?', [$name]);
    if (!$exists) {
        $db->execute('INSERT INTO categories (name) VALUES (?)', [$name]);
        echo "Inserted category: $name\n";
    } else {
        echo "Category exists: $name\n";
    }
}

// Fetch category ids
$cats = $db->query('SELECT * FROM categories')->fetchAll();
$catMap = [];
foreach ($cats as $c) $catMap[$c['name']] = $c['id'];

$sampleProducts = [
    [
        'name' => 'Bộ bàn ăn gỗ sồi 4 ghế',
        'slug' => 'bo-ban-an-go-soi-4-ghe',
        'price' => 4200000,
        'sale_price' => 3500000,
        'category' => 'Phòng ăn',
        'description' => 'Bàn ăn bằng gỗ sồi tự nhiên, thiết kế hiện đại.',
        'image' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=800&h=800&fit=crop',
        'stock' => 10,
        'featured' => 1
    ],
    [
        'name' => 'Giường ngủ bọc nệm King',
        'slug' => 'giuong-king-boc-nem',
        'price' => 8200000,
        'sale_price' => 7200000,
        'category' => 'Phòng ngủ',
        'description' => 'Giường ngủ bọc nệm êm ái, khung chắc chắn.',
        'image' => 'https://images.unsplash.com/photo-1505691723518-36a3a4f5c9b2?w=800&h=800&fit=crop',
        'stock' => 5,
        'featured' => 1
    ],
    [
        'name' => 'Kệ bếp đa năng',
        'slug' => 'ke-bep-da-nang',
        'price' => 2200000,
        'sale_price' => 0,
        'category' => 'Phòng bếp',
        'description' => 'Kệ bếp tiết kiệm không gian, phù hợp gia đình nhỏ.',
        'image' => 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?w=800&h=800&fit=crop',
        'stock' => 12,
        'featured' => 0
    ],
    [
        'name' => 'Bộ ghế sofa phòng khách',
        'slug' => 'sofa-phong-khach-3-cho',
        'price' => 15200000,
        'sale_price' => 13200000,
        'category' => 'Phòng khách',
        'description' => 'Sofa 3 chỗ cao cấp, vải chống bẩn.',
        'image' => 'https://images.unsplash.com/photo-1505691723518-36a3a4f5c9b2?w=800&h=800&fit=crop',
        'stock' => 3,
        'featured' => 1
    ],
    [
        'name' => 'Bàn làm việc chữ L',
        'slug' => 'ban-lam-viec-chu-l',
        'price' => 3200000,
        'sale_price' => 2900000,
        'category' => 'Phòng làm việc',
        'description' => 'Bàn chữ L rộng rãi cho góc làm việc hiệu quả.',
        'image' => 'https://images.unsplash.com/photo-1499951360447-b19be8fe80f5?w=800&h=800&fit=crop',
        'stock' => 8,
        'featured' => 0
    ],
];

foreach ($sampleProducts as $p) {
    // avoid duplicate by slug
    $exists = $db->queryOne('SELECT * FROM products WHERE slug = ?', [$p['slug']]);
    if ($exists) {
        echo "Product exists: {$p['slug']}\n";
        continue;
    }
    $categoryId = $catMap[$p['category']] ?? null;
    if (!$categoryId) continue;
    $db->execute('INSERT INTO products (name, slug, price, sale_price, category_id, description, image, stock, featured, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())', [
        $p['name'], $p['slug'], $p['price'], $p['sale_price'], $categoryId, $p['description'], $p['image'], $p['stock'], $p['featured']
    ]);
    echo "Inserted product: {$p['name']}\n";
}

echo "Seeding completed.\n";

?>