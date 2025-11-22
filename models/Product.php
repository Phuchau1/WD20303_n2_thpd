<?php
/**
 * Model Product - Quản lý sản phẩm
 */

class Product {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Lấy tất cả sản phẩm
     */
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                ORDER BY p.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT $limit OFFSET $offset";
        }
        
        return $this->db->query($sql);
    }
    
    /**
     * Lấy sản phẩm theo ID
     */
    public function getById($id) {
        return $this->db->queryOne(
            "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.id = ?",
            [$id]
        );
    }
    
    /**
     * Lấy sản phẩm theo slug
     */
    public function getBySlug($slug) {
        return $this->db->queryOne(
            "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.slug = ?",
            [$slug]
        );
    }
    
    /**
     * Lấy sản phẩm theo danh mục
     */
    public function getByCategory($categoryId, $limit = null, $offset = 0) {
        $params = [$categoryId];
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.category_id = ? 
                ORDER BY p.created_at DESC";

        if ($limit !== null) {
            // Use integer casting to avoid injection in LIMIT/OFFSET
            $limit = (int)$limit;
            $offset = (int)$offset;
            $sql .= " LIMIT $limit OFFSET $offset";
        }

        return $this->db->query($sql, $params);
    }

    /**
     * Count products in a category
     */
    public function countByCategory($categoryId) {
        $row = $this->db->queryOne("SELECT COUNT(*) as total FROM products WHERE category_id = ?", [$categoryId]);
        return $row ? (int)$row['total'] : 0;
    }
    
    /**
     * Lấy sản phẩm nổi bật
     */
    public function getFeatured($limit = 8) {
        // Some MySQL drivers don't allow binding LIMIT as a parameter when emulation is disabled.
        $limit = (int)$limit;
        $sql = "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.featured = 1 
             ORDER BY p.created_at DESC 
             LIMIT $limit";
        $stmt = $this->db->query($sql);
        $rows = [];
        if ($stmt && method_exists($stmt, 'fetchAll')) {
            $rows = $stmt->fetchAll();
        }

        // If no featured items found, fall back to latest products so homepage isn't empty
        if (empty($rows)) {
            $fallback = $this->getAll($limit);
            if (is_object($fallback) && method_exists($fallback, 'fetchAll')) {
                $rows = $fallback->fetchAll();
            } elseif (is_array($fallback)) {
                $rows = $fallback;
            } else {
                $rows = [];
            }
        }

        return $rows;
    }
    
    /**
     * Tìm kiếm sản phẩm
     */
    public function search($keyword, $categoryId = null, $minPrice = null, $maxPrice = null) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.name LIKE ?";
        
        $params = ["%$keyword%"];
        
        if ($categoryId) {
            $sql .= " AND p.category_id = ?";
            $params[] = $categoryId;
        }
        
        if ($minPrice) {
            $sql .= " AND p.price >= ?";
            $params[] = $minPrice;
        }
        
        if ($maxPrice) {
            $sql .= " AND p.price <= ?";
            $params[] = $maxPrice;
        }
        
        $sql .= " ORDER BY p.created_at DESC";
        
        return $this->db->query($sql, $params);
    }
    
    /**
     * Thêm sản phẩm
     */
    public function create($data) {
        return $this->db->execute(
            "INSERT INTO products (name, slug, price, sale_price, category_id, description, image, stock, featured) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $data['name'],
                $data['slug'],
                $data['price'],
                $data['sale_price'],
                $data['category_id'],
                $data['description'],
                $data['image'],
                $data['stock'],
                $data['featured'] ?? 0
            ]
        );
    }
    
    /**
     * Cập nhật sản phẩm
     */
    public function update($id, $data) {
        $sql = "UPDATE products SET 
                name = ?, 
                slug = ?, 
                price = ?, 
                sale_price = ?, 
                category_id = ?, 
                description = ?, 
                stock = ?, 
                featured = ?";
        
        $params = [
            $data['name'],
            $data['slug'],
            $data['price'],
            $data['sale_price'],
            $data['category_id'],
            $data['description'],
            $data['stock'],
            $data['featured'] ?? 0
        ];
        
        if (isset($data['image'])) {
            $sql .= ", image = ?";
            $params[] = $data['image'];
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        return $this->db->execute($sql, $params);
    }
    
    /**
     * Xóa sản phẩm
     */
    public function delete($id) {
        return $this->db->execute("DELETE FROM products WHERE id = ?", [$id]);
    }
    
    /**
     * Đếm tổng sản phẩm
     */
    public function count() {
        $result = $this->db->queryOne("SELECT COUNT(*) as total FROM products");
        return $result['total'];
    }
}