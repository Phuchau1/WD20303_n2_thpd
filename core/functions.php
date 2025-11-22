<?php
// Common helper functions
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/config.php';

function e($v){ return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
function formatPrice($n){ return number_format($n,0,',','.') . ' ₫'; }

function getCartCount(){
    if (empty($_SESSION['cart'])) return 0;
    $c = 0; foreach($_SESSION['cart'] as $it) $c += $it['quantity'];
    return $c;
}

// Simple upload helper
function uploadImage($file){
    if (empty($_FILES[$file]) || $_FILES[$file]['error'] !== UPLOAD_ERR_OK) return false;
    $f = $_FILES[$file];
    $allowed = ['image/jpeg','image/png','image/webp'];
    if (!in_array($f['type'], $allowed)) return false;
    $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
    $name = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR,0755,true);
    $dest = UPLOAD_DIR . $name;
    if (move_uploaded_file($f['tmp_name'], $dest)) return $name;
    return false;
}

/**
 * Resolve product image URL.
 * Tries multiple locations (UPLOAD_DIR, UPLOAD_DIR/img) and falls back to a placeholder.
 */
function productImageUrl($filename, $key = null){
    // external placeholder if nothing found
    $placeholder = 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=400&h=400&fit=crop';
    if (empty($filename)) return $placeholder;

    // If filename is already a remote URL, return it directly
    if (preg_match('#^https?://#i', $filename) || strpos($filename, '//') === 0) {
        return $filename;
    }

    // direct path
    $path1 = UPLOAD_DIR . $filename;
    if (file_exists($path1)) return UPLOAD_URL . $filename;

    // try inside img/ subfolder
    $path2 = UPLOAD_DIR . 'img/' . $filename;
    if (file_exists($path2)) return UPLOAD_URL . 'img/' . $filename;

    // try glob matching by filename base inside img/
    $base = pathinfo($filename, PATHINFO_FILENAME);
    $matches = glob(UPLOAD_DIR . 'img/*' . $base . '*');
    if (!empty($matches)){
        // return first match relative URL
        $match = $matches[0];
        $rel = str_replace(UPLOAD_DIR, '', $match);
        return UPLOAD_URL . str_replace('\\\\', '/', $rel);
    }

    // try matching by key (slug or name fragment)
    if ($key) {
        $matches = glob(UPLOAD_DIR . 'img/*' . $key . '*');
        if (!empty($matches)){
            $match = $matches[0];
            $rel = str_replace(UPLOAD_DIR, '', $match);
            return UPLOAD_URL . str_replace('\\\\', '/', $rel);
        }
    }

    return $placeholder;
}

?>
