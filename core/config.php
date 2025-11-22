<?php
/*
 * Quanan - core configuration
 * Chỉnh sửa các hằng số bên dưới theo môi trường XAMPP của bạn.
 */

// Database (mặc định tương thích file `database.sql` tạo database `webdogiadung`)
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'webdogiadung');
define('DB_USER', 'root');
define('DB_PASS', '');

// Đường dẫn base - chỉnh theo tên thư mục trong htdocs của bạn (ví dụ: '/webdogiadung')
define('BASE_URL', '/webdogiadung');

// Thư mục upload ảnh (đường dẫn tuyệt đối) và URL tương ứng
define('UPLOAD_DIR', __DIR__ . '/../assets/images/');
define('UPLOAD_URL', BASE_URL . '/assets/images/');

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Development: hiển thị lỗi (tắt khi đưa vào production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Không gọi session_start() ở đây; gọi trong core/functions.php khi cần.
?>