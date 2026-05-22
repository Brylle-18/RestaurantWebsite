-- ============================================================
-- MIGRATION: Add Discount Management & Sales Reporting
-- Run this in phpMyAdmin or via: mysql -u root mikosplace < MIGRATION_DISCOUNT_FEATURE.sql
-- ============================================================

USE mikosplace;

-- Add discount fields to existing bookings table
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS discount_percent DECIMAL(5,2) DEFAULT 0.00 AFTER total_amount;
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS final_amount DECIMAL(10,2) DEFAULT 0.00 AFTER discount_percent;
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS event_time TIME NULL AFTER event_date;

-- Update existing bookings to have final_amount equal to total_amount if not already set
UPDATE bookings SET final_amount = total_amount WHERE final_amount = 0.00 OR final_amount IS NULL;

-- ============================================================
-- MIGRATION COMPLETE
-- 
-- New Features:
-- 1. Discount Management: Apply 10%, 20%, or custom percentage discounts to bookings
-- 2. Auto Calculation: Final price automatically calculated as: 
--    final_amount = total_amount - (total_amount * discount_percent / 100)
-- 3. Sales Report: New admin tab shows all completed bookings with:
--    - Original price vs final price
--    - Total discounts given
--    - Revenue before and after discounts
--    - Average discount percentage
-- ============================================================
