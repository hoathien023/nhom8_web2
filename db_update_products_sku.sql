-- Thêm mã sản phẩm (SKU) cho bảng products
-- Chạy 1 lần trên từng máy/dev DB:
-- mysql -u root -p duan1_traicay < db_update_products_sku.sql

ALTER TABLE products
ADD COLUMN sku VARCHAR(64) NULL AFTER name;

-- Tạo SKU tạm cho dữ liệu cũ nếu đang NULL/rỗng
UPDATE products
SET sku = CONCAT('SP-', LPAD(product_id, 6, '0'))
WHERE sku IS NULL OR sku = '';

ALTER TABLE products
MODIFY COLUMN sku VARCHAR(64) NOT NULL;

ALTER TABLE products
ADD UNIQUE KEY uq_products_sku (sku);
