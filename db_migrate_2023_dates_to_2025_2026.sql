-- Cập nhật mọi datetime năm 2023 sang khoảng 2025-12-20 .. 2026-04-10 (giữ tỷ lệ thời gian).
-- Biên dữ liệu gốc: 2023-11-18 .. 2023-12-13 (theo bản dump mẫu).
-- Chạy sau khi USE duan1_traicay;

SET @old_lo := '2023-11-18 00:00:00';
SET @old_hi := '2023-12-13 23:59:59';
SET @new_lo := '2025-12-20 00:00:00';
SET @new_hi := '2026-04-10 23:59:59';
SET @span_old := TIMESTAMPDIFF(SECOND, @old_lo, @old_hi);
SET @span_new := TIMESTAMPDIFF(SECOND, @new_lo, @new_hi);

UPDATE `comments`
SET `date` = DATE_ADD(
  @new_lo,
  INTERVAL FLOOR(
    TIMESTAMPDIFF(SECOND, @old_lo, `date`) * @span_new / NULLIF(@span_old, 0)
  ) SECOND
)
WHERE YEAR(`date`) = 2023;

UPDATE `orders`
SET `date` = DATE_ADD(
  @new_lo,
  INTERVAL FLOOR(
    TIMESTAMPDIFF(SECOND, @old_lo, `date`) * @span_new / NULLIF(@span_old, 0)
  ) SECOND
)
WHERE YEAR(`date`) = 2023;

UPDATE `posts`
SET `created_at` = DATE_ADD(
  @new_lo,
  INTERVAL FLOOR(
    TIMESTAMPDIFF(SECOND, @old_lo, `created_at`) * @span_new / NULLIF(@span_old, 0)
  ) SECOND
)
WHERE YEAR(`created_at`) = 2023;

UPDATE `posts`
SET `updated_at` = DATE_ADD(
  @new_lo,
  INTERVAL FLOOR(
    TIMESTAMPDIFF(SECOND, @old_lo, `updated_at`) * @span_new / NULLIF(@span_old, 0)
  ) SECOND
)
WHERE YEAR(`updated_at`) = 2023;

UPDATE `products`
SET `create_date` = DATE_ADD(
  @new_lo,
  INTERVAL FLOOR(
    TIMESTAMPDIFF(SECOND, @old_lo, `create_date`) * @span_new / NULLIF(@span_old, 0)
  ) SECOND
)
WHERE YEAR(`create_date`) = 2023;
