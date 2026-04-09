ALTER TABLE `orders`
    ADD COLUMN IF NOT EXISTS `payment_method` VARCHAR(20) NOT NULL DEFAULT 'cod' AFTER `note`,
    ADD COLUMN IF NOT EXISTS `payment_status` VARCHAR(20) NOT NULL DEFAULT 'none' AFTER `payment_method`,
    ADD COLUMN IF NOT EXISTS `payment_deadline` DATETIME NULL DEFAULT NULL AFTER `payment_status`;

UPDATE `orders`
SET `payment_method` = CASE
    WHEN `payment_method` IS NULL OR TRIM(`payment_method`) = '' THEN 'cod'
    ELSE `payment_method`
END,
`payment_status` = CASE
    WHEN `payment_method` = 'bank' AND (`payment_status` IS NULL OR TRIM(`payment_status`) = '' OR `payment_status` = 'none') THEN 'pending'
    WHEN `payment_status` IS NULL OR TRIM(`payment_status`) = '' THEN 'none'
    ELSE `payment_status`
END
WHERE 1 = 1;

CREATE INDEX `idx_orders_payment_timeout` ON `orders` (`payment_method`, `payment_status`, `payment_deadline`, `status`);
