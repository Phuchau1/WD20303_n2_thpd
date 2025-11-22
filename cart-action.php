<?php
// Xử lý thêm, cập nhật, xóa sản phẩm trong giỏ hàng
require_once __DIR__ . '/core/config.php';
require_once __DIR__ . '/core/database_new.php';
require_once __DIR__ . '/models/Product.php';
if (session_status() === PHP_SESSION_NONE) session_start();

$db = new Database();
$productModel = new Product($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;
    if ($product_id > 0) {
        $product = $productModel->getById($product_id);
        if ($product) {
            if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
            if ($action === 'add') {
                if (isset($_SESSION['cart'][$product_id])) {
                    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
                } else {
                    $_SESSION['cart'][$product_id] = [
                        'product' => $product,
                        'quantity' => $quantity
                    ];
                }
                // Set a flash message so the UI can show a confirmation
                $_SESSION['flash_message'] = 'Bạn đã thêm "' . ($product['name'] ?? 'sản phẩm') . '" vào giỏ hàng.';
            } elseif ($action === 'update') {
                if ($quantity > 0) {
                    $_SESSION['cart'][$product_id]['quantity'] = $quantity;
                } else {
                    unset($_SESSION['cart'][$product_id]);
                }
            } elseif ($action === 'remove') {
                unset($_SESSION['cart'][$product_id]);
                $_SESSION['flash_message'] = 'Sản phẩm đã được xóa khỏi giỏ hàng.';
            }
        }
    }
    // Quay lại trang trước (nếu có) hoặc redirect được cung cấp, nếu không thì về trang giỏ hàng
    $redirect = $_POST['redirect'] ?? ($_SERVER['HTTP_REFERER'] ?? (BASE_URL . '/views/cart.php'));
    header('Location: ' . $redirect);
    exit;
}
// Nếu truy cập GET thì chuyển về trang chủ
header('Location: ' . BASE_URL);
exit;
