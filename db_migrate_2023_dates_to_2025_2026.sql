-- =============================================================================
-- Đồng bộ ngày tháng demo: mọi bản ghi có năm 2023, 2024 hoặc 2025
-- được đưa về THÁNG 4 NĂM 2026, ngày trong tháng từ 1–10 (theo id dòng).
-- Giữ nguyên phần giờ:phút:giây của cột gốc.
--
-- Cách chạy (phpMyAdmin hoặc mysql CLI):
--   USE duan1_traicay;
--   SOURCE db_migrate_2023_dates_to_2025_2026.sql;
--
-- Nếu lỗi ở khối payment_deadline: bảng chưa có cột — chạy trước
-- db_update_orders_payment_status.sql hoặc xóa/comment 4 dòng UPDATE đó.
-- =============================================================================

SET NAMES utf8mb4;

-- Ngày mới: 2026-04-(1..10) + giờ cũ
-- MOD(id,10) = 0..9  =>  1 + MOD(...) = 1..10

UPDATE `comments`
SET `date` = STR_TO_DATE(
  CONCAT('2026-04-', LPAD(1 + MOD(`comment_id`, 10), 2, '0'), ' ', DATE_FORMAT(`date`, '%H:%i:%s')),
  '%Y-%m-%d %H:%i:%s'
)
WHERE YEAR(`date`) BETWEEN 2023 AND 2025;

UPDATE `orders`
SET `date` = STR_TO_DATE(
  CONCAT('2026-04-', LPAD(1 + MOD(`order_id`, 10), 2, '0'), ' ', DATE_FORMAT(`date`, '%H:%i:%s')),
  '%Y-%m-%d %H:%i:%s'
)
WHERE YEAR(`date`) BETWEEN 2023 AND 2025;

UPDATE `posts`
SET `created_at` = STR_TO_DATE(
  CONCAT('2026-04-', LPAD(1 + MOD(`post_id`, 10), 2, '0'), ' ', DATE_FORMAT(`created_at`, '%H:%i:%s')),
  '%Y-%m-%d %H:%i:%s'
)
WHERE YEAR(`created_at`) BETWEEN 2023 AND 2025;

UPDATE `posts`
SET `updated_at` = STR_TO_DATE(
  CONCAT('2026-04-', LPAD(1 + MOD(`post_id`, 10), 2, '0'), ' ', DATE_FORMAT(`updated_at`, '%H:%i:%s')),
  '%Y-%m-%d %H:%i:%s'
)
WHERE YEAR(`updated_at`) BETWEEN 2023 AND 2025;

UPDATE `products`
SET `create_date` = STR_TO_DATE(
  CONCAT('2026-04-', LPAD(1 + MOD(`product_id`, 10), 2, '0'), ' ', DATE_FORMAT(`create_date`, '%H:%i:%s')),
  '%Y-%m-%d %H:%i:%s'
)
WHERE YEAR(`create_date`) BETWEEN 2023 AND 2025;

UPDATE `warehouse`
SET `created_at` = STR_TO_DATE(
  CONCAT('2026-04-', LPAD(1 + MOD(`id`, 10), 2, '0'), ' ', DATE_FORMAT(`created_at`, '%H:%i:%s')),
  '%Y-%m-%d %H:%i:%s'
)
WHERE YEAR(`created_at`) BETWEEN 2023 AND 2025;

UPDATE `warehouse_receipts`
SET `import_date` = STR_TO_DATE(
  CONCAT('2026-04-', LPAD(1 + MOD(`receipt_id`, 10), 2, '0')),
  '%Y-%m-%d'
)
WHERE YEAR(`import_date`) BETWEEN 2023 AND 2025;

UPDATE `warehouse_receipts`
SET `created_at` = STR_TO_DATE(
  CONCAT('2026-04-', LPAD(1 + MOD(`receipt_id`, 10), 2, '0'), ' ', DATE_FORMAT(`created_at`, '%H:%i:%s')),
  '%Y-%m-%d %H:%i:%s'
)
WHERE YEAR(`created_at`) BETWEEN 2023 AND 2025;

UPDATE `warehouse_receipts`
SET `updated_at` = STR_TO_DATE(
  CONCAT('2026-04-', LPAD(1 + MOD(`receipt_id`, 10), 2, '0'), ' ', DATE_FORMAT(`updated_at`, '%H:%i:%s')),
  '%Y-%m-%d %H:%i:%s'
)
WHERE YEAR(`updated_at`) BETWEEN 2023 AND 2025;

UPDATE `warehouse_receipt_items`
SET `created_at` = STR_TO_DATE(
  CONCAT('2026-04-', LPAD(1 + MOD(`item_id`, 10), 2, '0'), ' ', DATE_FORMAT(`created_at`, '%H:%i:%s')),
  '%Y-%m-%d %H:%i:%s'
)
WHERE YEAR(`created_at`) BETWEEN 2023 AND 2025;

-- Cột có sau khi chạy db_update_orders_payment_status.sql
UPDATE `orders`
SET `payment_deadline` = STR_TO_DATE(
  CONCAT('2026-04-', LPAD(1 + MOD(`order_id`, 10), 2, '0'), ' ', DATE_FORMAT(`payment_deadline`, '%H:%i:%s')),
  '%Y-%m-%d %H:%i:%s'
)
WHERE `payment_deadline` IS NOT NULL
  AND YEAR(`payment_deadline`) BETWEEN 2023 AND 2025;
