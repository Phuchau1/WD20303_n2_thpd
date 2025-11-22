
-- Xóa các bảng cũ nếu đã tồn tại
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS admin_users;

-- Tạo database (chỉ cần nếu chưa có)
CREATE DATABASE IF NOT EXISTS webdogiadung CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE webdogiadung;

-- Bảng danh mục sản phẩm

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng sản phẩm

CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    price DECIMAL(10,2) NOT NULL,
    sale_price DECIMAL(10,2) NULL,
    category_id INT NOT NULL,
    description TEXT,
    image VARCHAR(255),
    stock INT DEFAULT 0,
    featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng đơn hàng

CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255),
    address TEXT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng chi tiết đơn hàng

CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng giỏ hàng
CREATE TABLE carts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NULL,
    session_id VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cart_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cart_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bảng admin

CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mẫu danh mục
INSERT INTO categories (name, slug) VALUES
('Phòng Khách', 'phong-khach'),
('Phòng Ngủ', 'phong-ngu'),
('Phòng Bếp', 'phong-bep'),
('Phòng Ăn', 'phong-an'),
('Phòng Làm Việc', 'phong-lam-viec');

-- Dữ liệu mẫu sản phẩm
INSERT INTO products (name, slug, price, sale_price, category_id, description, image, stock, featured) VALUES
('Sofa Da Cao Cấp Milano', 'sofa-da-cao-cap-milano', 45000000, 39900000, 1, 'Sofa da thật nhập khẩu Italia, thiết kế hiện đại sang trọng. Khung gỗ sồi chắc chắn, đệm mút cao cấp êm ái.', 'sofa-1.jpg', 10, 1),
('Bàn Trà Gỗ Óc Chó', 'ban-tra-go-oc-cho', 8500000, 7200000, 1, 'Bàn trà gỗ óc chó tự nhiên, vân gỗ đẹp, bề mặt sơn PU cao cấp chống nước.', 'table-1.jpg', 15, 1),
('Giường Ngủ Gỗ Sồi Tự Nhiên', 'giuong-ngu-go-soi-tu-nhien', 25000000, 22000000, 2, 'Giường ngủ gỗ sồi Mỹ 100%, thiết kế tối giản hiện đại, đầu giường bọc nệm cao cấp.', 'bed-1.jpg', 8, 1),
('Tủ Quần Áo 4 Cánh', 'tu-quan-ao-4-canh', 18000000, NULL, 2, 'Tủ quần áo gỗ MDF phủ Melamine chống trầy, ray trượt giảm chấn êm ái.', 'wardrobe-1.jpg', 12, 0),
('Bộ Bàn Ăn 6 Ghế', 'bo-ban-an-6-ghe', 32000000, 28500000, 5, 'Bộ bàn ăn gỗ sồi tự nhiên cao cấp, mặt đá Marble trắng vân vàng tự nhiên.', 'dining-1.jpg', 6, 1),
('Tủ Bếp Acrylic Cao Cấp', 'tu-bep-acrylic-cao-cap', 85000000, 79000000, 3, 'Tủ bếp Acrylic bóng gương, phụ kiện Blum nhập khẩu Đức, thiết kế hiện đại.', 'kitchen-1.jpg', 5, 0),
('Bàn Làm Việc Gỗ Công Nghiệp', 'ban-lam-viec-go-cong-nghiep', 5500000, 4800000, 4, 'Bàn làm việc thiết kế tối giản, chân sắt sơn tĩnh điện chắc chắn.', 'desk-1.jpg', 20, 0),
('Ghế Văn Phòng Ergonomic', 'ghe-van-phong-ergonomic', 6500000, 5900000, 4, 'Ghế văn phòng thiết kế công thái học, lưng lưới thoáng mát, tay vịn điều chỉnh.', 'chair-1.jpg', 25, 1);

-- Thêm ~40 sản phẩm mẫu phân bổ theo 5 danh mục
INSERT INTO products (name, slug, price, sale_price, category_id, description, image, stock, featured) VALUES
('Sofa Vải Bắc Âu Luna', 'sofa-vai-bac-au-luna', 12000000, 10900000, 1, 'Sofa vải phong cách Bắc Âu, chân gỗ, đệm êm.', 'sofa-2.jpg', 12, 1),
('Sofa Góc Hiện Đại Orion', 'sofa-goc-hien-dai-orion', 18000000, 16000000, 1, 'Sofa góc bọc vải, tiện nghi cho phòng khách lớn.', 'sofa-3.jpg', 8, 1),
('Kệ Tivi Gỗ Tự Nhiên', 'ke-tivi-go-tu-nhien', 4500000, NULL, 1, 'Kệ tivi gỗ sồi nhỏ gọn, thiết kế tối giản.', 'tv-1.jpg', 20, 0),
('Bàn Trà Mặt Kính Halo', 'ban-tra-mat-kinh-halo', 3200000, 2900000, 1, 'Bàn trà chân kim loại, mặt kính cường lực.', 'table-2.jpg', 18, 0),
('Đèn Cây Trang Trí Nordic', 'den-cay-trang-tri-nordic', 1500000, 1290000, 1, 'Đèn cây phong cách Nordic cho góc phòng sáng tạo.', 'lamp-1.jpg', 30, 0),

('Giường Gỗ Hiện Đại Vega', 'giuong-go-hien-dai-vega', 16000000, 14500000, 2, 'Giường gỗ kết hợp đầu giường bọc vải cao cấp.', 'bed-2.jpg', 10, 1),
('Tủ Đầu Giường Siena', 'tu-dau-giuong-siena', 2200000, NULL, 2, 'Tủ nhỏ đầu giường, ngăn kéo êm.', 'wardrobe-2.jpg', 25, 0),
('Bộ Chăn Ga Cao Cấp', 'bo-chan-ga-cao-cap', 1200000, 990000, 2, 'Bộ chăn ga gối chất liệu cotton cao cấp.', 'bedding-1.jpg', 50, 0),
('Gối Tựa Lưng Memory', 'goi-tua-lung-memory', 450000, NULL, 2, 'Gối tựa lưng chất liệu memory foam.', 'pillow-1.jpg', 100, 0),
('Thảm Trải Sàn Orb', 'tham-trai-san-orb', 2200000, 1890000, 2, 'Thảm trang trí phòng ngủ - mềm mại và ấm.', 'rug-1.jpg', 40, 0),

('Tủ Bếp Thông Minh Amber', 'tu-bep-thong-minh-amber', 42000000, 38900000, 3, 'Tủ bếp modular, phụ kiện Blum.', 'kitchen-2.jpg', 6, 1),
('Bàn Bếp Di Động', 'ban-bep-di-dong', 3800000, 3490000, 3, 'Bàn bếp di động có bánh xe, tiết kiệm không gian.', 'kitchen-3.jpg', 14, 0),
('Bộ Đồ Ăn Sứ Marina', 'bo-do-an-su-marina', 1800000, NULL, 3, 'Bộ đồ ăn sứ 12 món, họa tiết tinh tế.', 'dinnerware-1.jpg', 60, 0),
('Giá Treo Chén Đĩa Inox', 'gia-treo-chen-dia-inox', 450000, NULL, 3, 'Giá treo chén inox cho không gian bếp gọn gàng.', 'rack-1.jpg', 80, 0),
('Máy Hút Mùi SlimPro', 'may-hut-mui-slimpro', 7200000, 6800000, 3, 'Máy hút mùi công suất cao, tiết kiệm điện.', 'hood-1.jpg', 5, 0),

('Bàn Làm Việc Xếp Gọn Nova', 'ban-lam-viec-xep-gon-nova', 2900000, 2590000, 4, 'Bàn làm việc xếp gọn, phù hợp không gian nhỏ.', 'desk-2.jpg', 22, 0),
('Ghế Công Thái Học Apollo', 'ghe-cong-thai-hoc-apollo', 7200000, 6900000, 4, 'Ghế lưng lưới chống mỏi, tay vịn điều chỉnh.', 'chair-2.jpg', 18, 1),
('Kệ Sách Treo Tường', 'ke-sach-treo-tuong', 900000, NULL, 4, 'Kệ sách treo tường đa năng.', 'shelf-1.jpg', 35, 0),
('Đèn Bàn Làm Việc LED', 'den-ban-lam-viec-led', 350000, 299000, 4, 'Đèn bàn LED chống mỏi mắt.', 'lamp-2.jpg', 60, 0),
('Bảng Ghim Văn Phòng', 'bang-ghim-van-phong', 250000, NULL, 4, 'Bảng ghim tiện lợi cho văn phòng tại nhà.', 'board-1.jpg', 80, 0),

('Bộ Bàn Ăn 4 Ghế Scala', 'bo-ban-an-4-ghe-scala', 16000000, 14900000, 5, 'Bộ bàn ăn 4 ghế, mặt gỗ công nghiệp cao cấp.', 'dining-2.jpg', 10, 1),
('Ghế Ăn Bọc Nệm', 'ghe-an-boc-nem', 1800000, 1590000, 5, 'Ghế ăn bọc nệm, dễ lau chùi.', 'dining-3.jpg', 30, 0),
('Đèn Thả Trần Phòng Ăn', 'den-tha-tran-phong-an', 1200000, 1090000, 5, 'Đèn thả trang trí cho bàn ăn.', 'lamp-3.jpg', 20, 0),
('Tủ Rượu Mini', 'tu-ruou-mini', 5200000, NULL, 5, 'Tủ rượu mini cho không gian phòng ăn.', 'wine-1.jpg', 8, 0),
('Bộ Phụ Kiện Bàn Ăn', 'bo-phu-kien-ban-an', 450000, NULL, 5, 'Phụ kiện tiện lợi cho bộ bàn ăn.', 'accessory-1.jpg', 100, 0);


-- Thêm sản phẩm mở rộng (tăng số lượng để đạt ~48 sản phẩm)
INSERT INTO products (name, slug, price, sale_price, category_id, description, image, stock, featured) VALUES
('Sofa Đơn Cozy', 'sofa-don-cozy', 3200000, 2890000, 1, 'Sofa đơn nhỏ gọn, phù hợp góc đọc sách.', 'sofa-4.jpg', 40, 0),
('Bàn Trang Trí Console', 'ban-trang-tri-console', 2700000, NULL, 1, 'Bàn console trang trí hành lang.', 'console-1.jpg', 12, 0),
('Gối Sofa Họa Tiết', 'goi-sofa-hoa-tiet', 220000, 199000, 1, 'Gối sofa họa tiết, tạo điểm nhấn.', 'pillow-2.jpg', 200, 0),
('Giường Gỗ Trẻ Em Mini', 'giuong-go-tre-em-mini', 7800000, 7200000, 2, 'Giường gỗ an toàn cho phòng trẻ em.', 'bed-3.jpg', 6, 0),
('Tủ Đựng Đồ Đa Năng', 'tu-dung-do-da-nang', 3400000, NULL, 2, 'Tủ nhỏ đa năng cho phòng ngủ.', 'wardrobe-3.jpg', 20, 0),
('Bồn Rửa Chén Cao Cấp', 'bon-rua-chen-cao-cap', 4200000, 3890000, 3, 'Bồn rửa chén inox 304 bền bỉ.', 'sink-1.jpg', 10, 0),
('Máy Rửa Chén Mini', 'may-rua-chen-mini', 8200000, 7990000, 3, 'Máy rửa chén cho gia đình nhỏ.', 'dishwasher-1.jpg', 4, 0),
('Ghế Bành Thư Giãn', 'ghe-banh-thu-gian', 5200000, 4890000, 1, 'Ghế bành thoải mái cho phòng khách.', 'armchair-1.jpg', 14, 0),
('Bàn Viết Nhỏ Gọn', 'ban-viet-nho-gon', 980000, NULL, 1, 'Bàn viết vừa đủ cho góc làm việc.', 'desk-3.jpg', 60, 0),
('Kệ Trang Trí Đa Tầng', 'ke-trang-tri-da-tang', 1250000, 1190000, 1, 'Kệ trang trí cho phòng khách.', 'shelf-2.jpg', 25, 0);

INSERT INTO admin_users (username, password_hash) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Dữ liệu mẫu đơn hàng
INSERT INTO orders (customer_name, phone, email, address, total_price, status) VALUES
('Nguyễn Văn A', '0901234567', 'a@example.com', '123 Đường ABC, Quận 1, TP.HCM', 39900000, 'completed');

-- Dữ liệu mẫu chi tiết đơn hàng
INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
(1, 1, 1, 39900000);