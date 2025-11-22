-- Full seed: 5 categories × 20 products (100 products)
-- Includes 10 featured products with sale prices (discounts)

DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS carts;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS admin_users;

CREATE DATABASE IF NOT EXISTS webdogiadung CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE webdogiadung;

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255),
    address TEXT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('pending','processing','completed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories
INSERT INTO categories (name, slug) VALUES
('Phòng Khách', 'phong-khach'),
('Phòng Ngủ', 'phong-ngu'),
('Phòng Bếp', 'phong-bep'),
('Phòng Ăn', 'phong-an'),
('Phòng Làm Việc', 'phong-lam-viec');

-- Helper: we'll insert 20 products per category. Slugs are unique.
-- Category IDs: 1..5

-- Products for Phòng Khách (category_id = 1)
INSERT INTO products (name, slug, price, sale_price, category_id, description, image, stock, featured) VALUES
('Sofa Da Milano', 'sofa-da-milano', 45000000, 39900000, 1, 'Sofa da cao cấp, khung gỗ chắc chắn.', 'sofa-1.jpg', 10, 1),
('Sofa Vải Luna', 'sofa-vai-luna', 12000000, 10900000, 1, 'Sofa vải phong cách Bắc Âu, chân gỗ.', 'sofa-2.jpg', 12, 1),
('Sofa Góc Orion', 'sofa-goc-orion', 18000000, 16000000, 1, 'Sofa góc tiện nghi cho phòng khách lớn.', 'sofa-3.jpg', 8, 1),
('Sofa Đơn Cozy', 'sofa-don-cozy', 3200000, 2890000, 1, 'Sofa đơn nhỏ gọn, góc đọc sách.', 'sofa-4.jpg', 40, 0),
('Bàn Trà Gỗ Óc Chó', 'ban-tra-go-oc-cho', 8500000, 7200000, 1, 'Bàn trà gỗ óc chó tự nhiên.', 'table-1.jpg', 15, 1),
('Bàn Trà Mặt Kính Halo', 'ban-tra-mat-kinh-halo', 3200000, 2900000, 1, 'Bàn trà chân kim loại, mặt kính cường lực.', 'table-2.jpg', 18, 0),
('Kệ Tivi Gỗ', 'ke-tivi-go', 4500000, NULL, 1, 'Kệ tivi gỗ sồi nhỏ gọn.', 'tv-1.jpg', 20, 0),
('Đèn Cây Nordic', 'den-cay-nordic', 1500000, 1290000, 1, 'Đèn cây phong cách Nordic.', 'lamp-1.jpg', 30, 0),
('Gối Sofa Họa Tiết', 'goi-sofa-hoa-tiet', 220000, 199000, 1, 'Gối sofa họa tiết, tạo điểm nhấn.', 'pillow-1.jpg', 200, 0),
('Kệ Trang Trí Da Vinci', 'ke-trang-tri-da-vinci', 1250000, 1190000, 1, 'Kệ trang trí đa tầng hiện đại.', 'shelf-1.jpg', 25, 0),
('Bàn Console Trang Trí', 'ban-console-trang-tri', 2700000, NULL, 1, 'Bàn console trang trí hành lang.', 'console-1.jpg', 12, 0),
('Thảm Trải Sàn Orb', 'tham-trai-san-orb', 2200000, 1890000, 1, 'Thảm trang trí - mềm mại và ấm.', 'rug-1.jpg', 40, 0),
('Đèn Treo Trang Trí Luna', 'den-treo-luna', 1200000, 1090000, 1, 'Đèn thả trang trí hiện đại.', 'lamp-2.jpg', 20, 0),
('Kệ Trang Trí Tường', 'ke-trang-tri-tuong', 900000, NULL, 1, 'Kệ sách treo tường đa năng.', 'shelf-2.jpg', 35, 0),
('Bàn Viết Nhỏ Gọn', 'ban-viet-nho-gon', 980000, NULL, 1, 'Bàn viết cho góc làm việc nhỏ.', 'desk-1.jpg', 60, 0),
('Ghế Bành Thư Giãn', 'ghe-banh-thu-gian', 5200000, 4890000, 1, 'Ghế thư giãn cho phòng khách.', 'armchair-1.jpg', 14, 0),
('Đèn Trang Trí Corner', 'den-trang-tri-corner', 850000, 790000, 1, 'Đèn góc phòng tiện dụng.', 'lamp-3.jpg', 50, 0),
('Bàn Đa Năng Modul', 'ban-da-nang-modul', 2200000, 1990000, 1, 'Bàn modul đa năng cho phòng khách.', 'table-3.jpg', 22, 0),
('Tủ Rượu Mini', 'tu-ruou-mini', 5200000, NULL, 1, 'Tủ rượu mini cho phòng khách.', 'wine-1.jpg', 8, 0),
('Bộ Trang Trí Phòng Khách', 'bo-trang-tri-phong-khach', 6800000, 6300000, 1, 'Bộ sản phẩm trang trí phòng khách.', 'decor-1.jpg', 11, 0);

-- Products for Phòng Ngủ (category_id = 2)
INSERT INTO products (name, slug, price, sale_price, category_id, description, image, stock, featured) VALUES
('Giường Gỗ Sồi Milano', 'giuong-go-soi-milano', 25000000, 22000000, 2, 'Giường ngủ gỗ sồi, thiết kế hiện đại.', 'bed-1.jpg', 8, 1),
('Giường Vega', 'giuong-vega', 16000000, 14500000, 2, 'Giường gỗ với đầu giường bọc vải.', 'bed-2.jpg', 10, 1),
('Giường Trẻ Em Mini', 'giuong-tre-em-mini', 7800000, 7200000, 2, 'Giường an toàn cho trẻ em.', 'bed-3.jpg', 6, 0),
('Tủ Quần Áo 4 Cánh', 'tu-quan-ao-4-canh', 18000000, NULL, 2, 'Tủ quần áo MDF phủ Melamine.', 'wardrobe-1.jpg', 12, 0),
('Tủ Đầu Giường Siena', 'tu-dau-giuong-siena', 2200000, NULL, 2, 'Tủ đầu giường ngăn kéo êm.', 'wardrobe-2.jpg', 25, 0),
('Bộ Chăn Ga Cao Cấp', 'bo-chan-ga-cao-cap', 1200000, 990000, 2, 'Chăn ga cotton cao cấp.', 'bedding-1.jpg', 50, 0),
('Gối Memory', 'goi-memory', 450000, NULL, 2, 'Gối memory êm ái.', 'pillow-1.jpg', 100, 0),
('Tủ Đựng Đa Năng', 'tu-dung-da-nang', 3400000, NULL, 2, 'Tủ nhỏ đa năng cho phòng ngủ.', 'wardrobe-3.jpg', 20, 0),
('Thảm Phòng Ngủ Soft', 'tham-phong-ngu-soft', 1800000, 1590000, 2, 'Thảm êm cho phòng ngủ.', 'rug-2.jpg', 30, 0),
('Bàn Trang Điểm Aurora', 'ban-trang-diem-aurora', 4200000, 3890000, 2, 'Bàn trang điểm tiện nghi.', 'dressing-1.jpg', 7, 0),
('Kệ Giày Đa Năng', 'ke-giay-da-nang', 900000, NULL, 2, 'Kệ giày nhỏ gọn.', 'shelf-3.jpg', 40, 0),
('Đèn Đọc Sách', 'den-doc-sach', 350000, 299000, 2, 'Đèn đọc sách chống mỏi mắt.', 'lamp-4.jpg', 60, 0),
('Bộ Nệm Cao Cấp', 'bo-nem-cao-cap', 5600000, 4990000, 2, 'Nệm êm, hỗ trợ cột sống.', 'mattress-1.jpg', 15, 1),
('Gương Toàn Thân', 'guong-toan-than', 1200000, NULL, 2, 'Gương trang trí lớn.', 'mirror-1.jpg', 20, 0),
('Bộ Đệm Gối Deluxe', 'bo-dem-goi-deluxe', 2200000, 1990000, 2, 'Bộ đệm gối thoải mái.', 'bedding-2.jpg', 50, 0),
('Tủ Lưu Trữ Nhiều Ngăn', 'tu-luu-tru-nhieu-ngan', 4200000, 3890000, 2, 'Tủ lưu trữ nhỏ gọn.', 'wardrobe-4.jpg', 18, 0),
('Bộ Đệm Giường Trẻ Em', 'bo-dem-giuong-tre-em', 1800000, NULL, 2, 'Đệm cho giường trẻ em.', 'mattress-2.jpg', 26, 0),
('Bộ Ga Gối Thoáng Mát', 'bo-ga-goi-thoang-mat', 980000, 850000, 2, 'Chăn ga thoáng mát.', 'bedding-3.jpg', 80, 0),
('Tủ Đầu Giường Kepler', 'tu-dau-giuong-kepler', 1500000, NULL, 2, 'Tủ đầu giường kiểu đơn giản.', 'nightstand-1.jpg', 22, 0),
('Bộ Trang Trí Phòng Ngủ', 'bo-trang-tri-phong-ngu', 780000, 699000, 2, 'Trang trí phòng ngủ nhỏ gọn.', 'decor-2.jpg', 30, 0);

-- Products for Phòng Bếp (category_id = 3)
INSERT INTO products (name, slug, price, sale_price, category_id, description, image, stock, featured) VALUES
('Tủ Bếp Acrylic', 'tu-bep-acrylic', 85000000, 79000000, 3, 'Tủ bếp bóng gương, phụ kiện cao cấp.', 'kitchen-1.jpg', 5, 0),
('Tủ Bếp Amber', 'tu-bep-amber', 42000000, 38900000, 3, 'Tủ bếp modular tiện nghi.', 'kitchen-2.jpg', 6, 1),
('Bàn Bếp Di Động', 'ban-bep-di-dong', 3800000, 3490000, 3, 'Bàn bếp có bánh xe tiết kiệm không gian.', 'kitchen-3.jpg', 14, 0),
('Bộ Đồ Ăn Sứ Marina', 'bo-do-an-marina', 1800000, NULL, 3, 'Bộ đồ ăn sứ 12 món.', 'dinnerware-1.jpg', 60, 0),
('Giá Treo Chén Inox', 'gia-treo-chen-inox', 450000, NULL, 3, 'Giá treo chén inox tiện lợi.', 'rack-1.jpg', 80, 0),
('Máy Hút Mùi SlimPro', 'may-hut-mui-slimpro', 7200000, 6800000, 3, 'Máy hút mùi công suất cao.', 'hood-1.jpg', 5, 0),
('Máy Rửa Chén Mini', 'may-rua-chen-mini', 8200000, 7990000, 3, 'Máy rửa chén cho gia đình nhỏ.', 'dishwasher-1.jpg', 4, 0),
('Bồn Rửa Chén Inox', 'bon-rua-chen-inox', 4200000, 3890000, 3, 'Bồn rửa chén chất liệu inox 304.', 'sink-1.jpg', 10, 0),
('Bộ Dao Thớt Cao Cấp', 'bo-dao-thot-cao-cap', 650000, 590000, 3, 'Bộ dao thớt nhà bếp chất lượng.', 'knife-1.jpg', 45, 0),
('Tủ Bếp Nhỏ Gọn', 'tu-bep-nho-gon', 22000000, 19900000, 3, 'Tủ bếp cho không gian nhỏ.', 'kitchen-4.jpg', 9, 0),
('Bàn Ăn Di Động', 'ban-an-di-dong', 3200000, 2890000, 3, 'Bàn ăn nhỏ linh hoạt.', 'dining-3.jpg', 20, 0),
('Kệ Gia Vị Treo Tường', 'ke-gia-vi-treo-tuong', 450000, NULL, 3, 'Kệ gia vị tiện dụng.', 'shelf-kitchen-1.jpg', 60, 0),
('Bộ Đồ Nấu Ăn Smart', 'bo-do-nau-an-smart', 2200000, 1990000, 3, 'Bộ nồi chảo thông dụng.', 'cookware-1.jpg', 50, 0),
('Máy Xay Sinh Tố Mini', 'may-xay-mini', 850000, 749000, 3, 'Máy xay nhỏ gọn.', 'blender-1.jpg', 30, 0),
('Đèn Bếp LED', 'den-bep-led', 350000, 299000, 3, 'Đèn bếp chống mỏi mắt.', 'lamp-kitchen-1.jpg', 60, 0),
('Bàn Bar Cao', 'ban-bar-cao', 5200000, NULL, 3, 'Bàn bar cho khu bếp mở.', 'bar-table-1.jpg', 8, 0),
('Tủ Đồ Nhà Bếp Multi', 'tu-do-nha-bep-multi', 7800000, 6990000, 3, 'Tủ lưu trữ đa năng cho bếp.', 'kitchen-5.jpg', 11, 0),
('Phụ Kiện Nhà Bếp Set', 'phu-kien-nha-bep-set', 450000, NULL, 3, 'Phụ kiện tiện ích nhỏ gọn.', 'accessory-kitchen-1.jpg', 100, 0),
('Thớt Gỗ Chopping', 'thot-go-chopping', 220000, 199000, 3, 'Thớt gỗ chất lượng cao.', 'cuttingboard-1.jpg', 200, 0),
('Bàn Soạn Inox', 'ban-soan-inox', 4200000, 3890000, 3, 'Bàn soạn inox bền bỉ.', 'stainless-table-1.jpg', 12, 0);

-- Products for Phòng Ăn (category_id = 4)
INSERT INTO products (name, slug, price, sale_price, category_id, description, image, stock, featured) VALUES
('Bộ Bàn Ăn Scala 4 Ghế', 'bo-ban-an-scala-4', 16000000, 14900000, 4, 'Bộ bàn ăn 4 ghế, mặt gỗ công nghiệp.', 'dining-2.jpg', 10, 1),
('Bộ Bàn Ăn 6 Ghế', 'bo-ban-an-6', 32000000, 28500000, 4, 'Bộ bàn ăn gỗ sồi cao cấp.', 'dining-1.jpg', 6, 1),
('Ghế Ăn Bọc Nệm', 'ghe-an-boc-nem', 1800000, 1590000, 4, 'Ghế ăn bọc nệm dễ lau chùi.', 'dining-3.jpg', 30, 0),
('Đèn Thả Bàn Ăn', 'den-tha-ban-an', 1200000, 1090000, 4, 'Đèn thả trang trí cho bàn ăn.', 'lamp-3.jpg', 20, 0),
('Tủ Rượu Mini', 'tu-ruou-mini-2', 5200000, NULL, 4, 'Tủ rượu cho không gian ăn uống.', 'wine-2.jpg', 8, 0),
('Bộ Phụ Kiện Bàn Ăn', 'bo-phu-kien-ban-an', 450000, NULL, 4, 'Phụ kiện cho bàn ăn.', 'accessory-1.jpg', 100, 0),
('Khăn Trải Bàn Linen', 'khan-trai-ban-linen', 350000, 299000, 4, 'Khăn trải bàn chất liệu linen.', 'linen-1.jpg', 80, 0),
('Bộ Ly Thủy Tinh', 'bo-ly-thuy-tinh', 250000, NULL, 4, 'Bộ ly thủy tinh tinh tế.', 'glass-1.jpg', 120, 0),
('Bộ Đĩa Sứ Decor', 'bo-dia-su-decor', 450000, NULL, 4, 'Đĩa sứ họa tiết trang nhã.', 'plate-1.jpg', 90, 0),
('Bộ Bàn Ăn Màu Tối', 'bo-ban-an-mau-toi', 14000000, 12900000, 4, 'Bộ bàn ăn phong cách cổ điển.', 'dining-4.jpg', 6, 0),
('Bàn Gấp Di Động', 'ban-gap-di-dong', 2900000, 2590000, 4, 'Bàn gấp cho không gian nhỏ.', 'table-fold-1.jpg', 22, 0),
('Ghế Ăn MDF', 'ghe-an-mdf', 950000, 850000, 4, 'Ghế ăn giá rẻ chất lượng ổn.', 'chair-3.jpg', 70, 0),
('Tấm Lót Chống Trượt', 'tam-lot-chong-truot', 150000, NULL, 4, 'Tấm lót bàn chống trượt.', 'mat-1.jpg', 200, 0),
('Thảm Dưới Bàn Ăn', 'tham-duoi-ban-an', 1200000, 1090000, 4, 'Thảm trang trí dưới bàn ăn.', 'rug-dining-1.jpg', 30, 0),
('Đèn Trang Trí Phòng Ăn', 'den-trang-tri-phong-an', 220000, 199000, 4, 'Đèn trang trí nhỏ gọn.', 'lamp-dining-1.jpg', 60, 0),
('Kệ Gia Vị Bếp', 'ke-gia-vi-bep', 450000, NULL, 4, 'Kệ gia vị tiện lợi.', 'shelf-kitchen-2.jpg', 45, 0),
('Bộ Dao Nhà Bếp', 'bo-dao-nha-bep', 650000, 590000, 4, 'Bộ dao chất lượng.', 'knife-2.jpg', 55, 0),
('Tủ Đồ Phòng Ăn', 'tu-do-phong-an', 4200000, 3890000, 4, 'Tủ nhỏ lưu trữ cho phòng ăn.', 'cabinet-1.jpg', 18, 0),
('Bộ Nến Trang Trí', 'bo-nen-trang-tri', 250000, NULL, 4, 'Bộ nến cho bữa tối ấm cúng.', 'candle-1.jpg', 150, 0),
('Bộ Dụng Cụ Tiệc', 'bo-dung-cu-tiec', 980000, 850000, 4, 'Dụng cụ tiệc tiện lợi.', 'party-set-1.jpg', 30, 0);

-- Products for Phòng Làm Việc (category_id = 5)
INSERT INTO products (name, slug, price, sale_price, category_id, description, image, stock, featured) VALUES
('Bàn Làm Việc Nova', 'ban-lam-viec-nova', 2900000, 2590000, 5, 'Bàn xếp gọn phù hợp không gian nhỏ.', 'desk-2.jpg', 22, 0),
('Ghế Ergonomic Apollo', 'ghe-ergonomic-apollo', 7200000, 6900000, 5, 'Ghế công thái học chống mỏi.', 'chair-2.jpg', 18, 1),
('Kệ Sách Treo Tường', 'ke-sach-treo-tuong-2', 900000, NULL, 5, 'Kệ sách treo tường đa năng.', 'shelf-4.jpg', 35, 0),
('Đèn Bàn LED Pro', 'den-ban-led-pro', 350000, 299000, 5, 'Đèn bàn chống mỏi mắt.', 'lamp-5.jpg', 60, 0),
('Bảng Ghim Văn Phòng', 'bang-ghim-van-phong', 250000, NULL, 5, 'Bảng ghim cho văn phòng tại nhà.', 'board-1.jpg', 80, 0),
('Tủ Lưu Trữ Office', 'tu-luu-tru-office', 4200000, 3890000, 5, 'Tủ lưu trữ hồ sơ nhỏ gọn.', 'cabinet-office-1.jpg', 12, 0),
('Bàn Viết Nhỏ Gọn 2', 'ban-viet-nho-gon-2', 980000, NULL, 5, 'Bàn viết tiện dụng.', 'desk-3.jpg', 60, 0),
('Bộ Phụ Kiện Office', 'bo-phu-kien-office', 450000, NULL, 5, 'Phụ kiện văn phòng tiện lợi.', 'accessory-office-1.jpg', 100, 0),
('Kệ Tài Liệu', 'ke-tai-lieu', 1250000, 1190000, 5, 'Kệ tài liệu nhiều ngăn.', 'shelf-office-1.jpg', 25, 0),
('Bàn Làm Việc Gỗ', 'ban-lam-viec-go', 5500000, 4800000, 5, 'Bàn làm việc gỗ công nghiệp.', 'desk-4.jpg', 20, 0),
('Ghế Văn Phòng Basic', 'ghe-van-phong-basic', 2200000, 1990000, 5, 'Ghế văn phòng cơ bản.', 'chair-4.jpg', 70, 0),
('Đèn Downlight Office', 'den-downlight-office', 450000, NULL, 5, 'Đèn downlight cho bàn làm việc.', 'lamp-office-2.jpg', 80, 0),
('Kệ Tài Liệu Di Động', 'ke-tai-lieu-di-dong', 2900000, 2590000, 5, 'Kệ di động tiết kiệm không gian.', 'shelf-office-2.jpg', 15, 0),
('Ghế Xoay Công Thái', 'ghe-xoay-cong-thai', 6500000, 5900000, 5, 'Ghế xoay thoải mái cho công việc.', 'chair-5.jpg', 25, 1),
('Bàn Công Thái Học', 'ban-cong-thai-hoc', 4200000, 3890000, 5, 'Bàn điều chỉnh chiều cao.', 'desk-5.jpg', 10, 0),
('Giá Treo Dụng Cụ', 'gia-treo-dung-cu', 450000, NULL, 5, 'Giá treo tiện lợi cho văn phòng.', 'rack-office-1.jpg', 60, 0),
('Thảm Chân Bàn', 'tham-chan-ban', 250000, NULL, 5, 'Thảm lót chân bàn chống mòn.', 'mat-office-1.jpg', 100, 0),
('Bộ Đèn LED Điều Chỉnh', 'bo-den-led-dieu-chinh', 980000, 850000, 5, 'Bộ đèn LED điều chỉnh ánh sáng.', 'led-set-1.jpg', 40, 0),
('Hộc Tủ Di Động', 'hoc-tu-di-dong', 650000, NULL, 5, 'Hộc tủ di động nhỏ.', 'mobile-drawer-1.jpg', 55, 0),
('Bộ Thiết Bị Họp Trực Tuyến', 'bo-thiet-bi-hop-truc-tuyen', 3200000, 2890000, 5, 'Bộ webcam + microphone cho họp online.', 'webcam-set-1.jpg', 18, 0);

-- Mark 10 additional distinct products as featured + discount (ensure they are unique across IDs)
-- We'll update 10 earlier-inserted products by slug (safe approach)
UPDATE products SET featured = 1, sale_price = price * 0.85 WHERE slug IN (
  'sofa-da-milano', 'sofa-vai-luna', 'giuong-go-soi-milano', 'bo-ban-an-6', 'tu-bep-amber',
  'ban-lam-viec-nova', 'ghe-ergonomic-apollo', 'ban-lam-viec-go', 'bo-nem-cao-cap', 'ban-tra-go-oc-cho'
);

-- Admin user (default password hash uses bcrypt placeholder; replace in production)
INSERT INTO admin_users (username, password_hash) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Optional example order and order item
INSERT INTO orders (customer_name, phone, email, address, total_price, status) VALUES
('Nguyen Van A', '0901234567', 'a@example.com', '123 Le Loi, HCMC', 39900000, 'completed');

INSERT INTO order_items (order_id, product_id, quantity, price) VALUES
(1, 1, 1, 39900000);

-- End of seed

-- ===== BLOG POSTS =====
DROP TABLE IF EXISTS posts;
CREATE TABLE posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    image VARCHAR(255),
    content TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO posts (title, slug, image, content) VALUES
('5 Xu hướng nội thất 2025', '5-xu-huong-noi-that-2025', 'images/blog1.jpg', 'Khám phá 5 xu hướng nội thất nổi bật năm 2025 giúp không gian sống hiện đại, tiện nghi và cá tính hơn.'),
('Bí quyết chọn sofa phòng khách', 'bi-quyet-chon-sofa-phong-khach', 'images/blog2.jpg', 'Chia sẻ kinh nghiệm chọn sofa phù hợp với diện tích, phong cách và ngân sách gia đình.'),
('Tối ưu hóa không gian bếp nhỏ', 'toi-uu-hoa-khong-gian-bep-nho', 'images/blog3.jpg', 'Gợi ý thiết kế và lựa chọn nội thất giúp căn bếp nhỏ trở nên rộng rãi, tiện nghi.'),
('Phong thủy phòng ngủ hiện đại', 'phong-thuy-phong-ngu-hien-dai', 'images/blog4.jpg', 'Những lưu ý phong thủy khi bố trí phòng ngủ để mang lại giấc ngủ ngon và năng lượng tích cực.'),
('Trang trí phòng làm việc sáng tạo', 'trang-tri-phong-lam-viec-sang-tao', 'images/blog5.jpg', 'Ý tưởng trang trí phòng làm việc giúp tăng cảm hứng và hiệu suất công việc.');
