## Discount Management & Sales Report Feature - Implementation Guide

### Overview
This feature adds the ability to automatically calculate and track discounts applied to bookings, plus a comprehensive sales report showing revenue impact of discounts.

### What Was Added

#### 1. **Database Changes**
- Added `discount_percent` field to bookings table (0-100%)
- Added `final_amount` field to store calculated price after discount

#### 2. **Admin API Enhancements** (`admin/api.php`)
- **Updated `booking_update` endpoint**: Now accepts `discount_percent` parameter
  - Automatically calculates: `final_amount = total_amount × (1 - discount_percent/100)`
  - Validates discount is between 0-100%
  
- **New `sales_report` endpoint**: Returns completed bookings with:
  - Original price
  - Discount percentage & amount
  - Final price after discount
  - Summary statistics (total revenue, total discounts, final revenue)

#### 3. **Admin Dashboard Updates** (`admin/index.php`)

**Booking Review Form**:
- New discount field accepting 0-100%
- Real-time calculation of final price
- Displays discount amount in currency
- Shows calculated final amount (read-only)

**Reports Section**:
- New "Sales Report with Discounts" tab
- Date range: Current month by default
- Shows summary cards with:
  - Total completed bookings
  - Original revenue (before discounts)
  - Total discounts given
  - Final revenue (after discounts)
  - Average discount percentage
- Detailed booking table showing:
  - Ticket number
  - Customer name
  - Original price
  - Discount (% + amount)
  - Final price

### How to Use

#### 1. **Run Database Migration**
```bash
mysql -u root mikosplace < MIGRATION_DISCOUNT_FEATURE.sql
```

Or run manually in phpMyAdmin:
```sql
USE mikosplace;
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS discount_percent DECIMAL(5,2) DEFAULT 0.00 AFTER total_amount;
ALTER TABLE bookings ADD COLUMN IF NOT EXISTS final_amount DECIMAL(10,2) DEFAULT 0.00 AFTER discount_percent;
UPDATE bookings SET final_amount = total_amount WHERE final_amount = 0.00 OR final_amount IS NULL;
```

#### 2. **Apply Discount to a Booking**
1. Go to Admin Dashboard → Bookings
2. Click on a booking to review
3. In the booking review modal:
   - Set the **Final Price (₱)** - original quoted price
   - Set the **Discount (%)** - e.g., enter `10` for 10% off
   - **Calculated Final Amount** auto-updates
4. Click "Save Changes"

**Example**:
- Original Price: ₱10,000
- Discount: 20%
- Final Amount: ₱8,000 (auto-calculated)

#### 3. **View Sales Report**
1. Go to Admin Dashboard → Reports
2. Click "Sales Report with Discounts" tab
3. See monthly summary and booking-by-booking breakdown

### Features Details

#### Auto-Calculation Logic
```
Discount Amount = Total Amount × (Discount Percent / 100)
Final Amount = Total Amount - Discount Amount
```

**Example**:
- Booking Total: ₱50,000
- Discount: 15%
- Discount Amount: ₱7,500
- Final Amount: ₱42,500

#### Sales Report Metrics
- **Total Revenue**: Sum of all original prices
- **Total Discount**: Sum of all discount amounts given
- **Final Revenue**: Sum of all final prices (what you actually get paid)
- **Average Discount %**: Mean discount percentage across bookings

### API Reference

#### Update Booking with Discount
```
POST /admin/api.php
Parameters:
  - action: 'booking_update'
  - id: booking_id
  - total_amount: original_price
  - discount_percent: 0-100
  - status: booking_status
  - pricing_notes: optional
  - notes: optional
```

**Response**:
```json
{
  "ok": true,
  "message": "Booking updated",
  "final_amount": 8000.00,
  "discount_percent": 20
}
```

#### Get Sales Report
```
GET /admin/api.php?action=sales_report&start_date=2024-01-01&end_date=2024-01-31
```

**Response**:
```json
{
  "ok": true,
  "bookings": [
    {
      "id": 1,
      "ticket_no": "#MP-301",
      "customer_name": "John Doe",
      "total_amount": 10000,
      "discount_percent": 10,
      "final_amount": 9000,
      "status": "completed"
    }
  ],
  "summary": {
    "total_bookings": 15,
    "total_revenue": 150000,
    "total_discount": 15000,
    "final_revenue": 135000,
    "average_discount_percent": 10
  }
}
```

### Common Discount Scenarios

#### Restaurant Order with Early Bird Discount
- Price: ₱2,500
- Discount: 10% (early booking)
- Final: ₱2,250

#### Venue + Catering Package with Volume Discount
- Price: ₱50,000
- Discount: 20% (large group of 100+ guests)
- Final: ₱40,000

#### Senior/Student Discount
- Price: ₱1,500
- Discount: 15%
- Final: ₱1,275

### Notes
- Discounts are stored as percentages for flexibility
- Final amount is calculated and stored for accurate reporting
- All discount information is included in the sales report
- Discounts only apply to completed bookings in the report
- Zero discount is allowed (customers may get full price)

### Troubleshooting

**Q: Discount calculation not showing?**
A: Make sure you've entered the total amount first, then enter discount percentage.

**Q: Sales report shows no data?**
A: Ensure bookings have status set to "completed" to appear in the report.

**Q: Discount allows values over 100%?**
A: The API validates 0-100%. If using API directly, ensure your validation.

### Files Modified
- `database.sql` - Updated bookings table schema
- `admin/api.php` - Added discount handling and sales_report endpoint
- `admin/index.php` - Added UI for discounts and sales report
- Created: `MIGRATION_DISCOUNT_FEATURE.sql` - Database migration script
