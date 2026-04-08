-- Migration: add unit + profit_rate for products
-- Safe to run multiple times on MySQL 8+ (uses IF NOT EXISTS)

ALTER TABLE products
    ADD COLUMN IF NOT EXISTS unit VARCHAR(50) NOT NULL DEFAULT 'Kg' AFTER name,
    ADD COLUMN IF NOT EXISTS profit_rate DECIMAL(5,2) NOT NULL DEFAULT 0.00 AFTER cost_price;

ALTER TABLE products
    MODIFY COLUMN cost_price DECIMAL(12,3) NOT NULL DEFAULT 0.000;

-- Optional backfill for old rows (in case any null/empty values exist)
UPDATE products
SET unit = 'Kg'
WHERE unit IS NULL OR TRIM(unit) = '';
