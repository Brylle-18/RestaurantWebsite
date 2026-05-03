# Miko's Place — Setup Instructions

## ✅ What Changed
The system has been simplified to remove unnecessary customer authentication:
- ❌ Deleted: `customer/register.php` (unused)
- ❌ Deleted: `customer/login.php` (unused)
- ❌ Updated `database.sql`: Removed `customers` table (no longer needed)
- ❌ Cleaned up `customer/api.php`: Removed login/register endpoints
- ❌ Cleaned up `includes/auth.php`: Removed customer auth functions

## ✅ Why?
The system is designed for **friction-free bookings**:
- Customers fill the reservation form on the landing page (no login needed)
- They get a ticket number instantly
- They can track their booking using just the ticket number
- This is perfect for a restaurant/catering business

---

## 🚀 Setup Steps (Do This First!)

### 1. Open phpMyAdmin
Navigate to: **http://localhost/phpmyadmin**

### 2. Import the Database
- Click the **"Import"** tab at the top
- Click **"Browse"** and select: `mikosplace/database.sql`
- Click **"Import"** button
- Wait for: ✅ **"Import has been successfully finished"**

### 3. Test the System
| Page | URL |
|------|-----|
| **Customer Website** | `http://localhost/mikosplace/customer/` |
| **Admin Dashboard** | `http://localhost/mikosplace/admin/login.php` |

**Admin Login:**
- Username: `admin`
- Password: `admin123`

---

## 📋 Database Tables Created

| Table | Purpose |
|-------|---------|
| `admin_users` | Admin login accounts |
| `menu_items` | All dishes & drinks |
| `venues` | Event venue packages |
| `bookings` | Customer reservations |
| `booking_items` | Menu items in a booking |
| `staff` | Team members & shifts |

**Note:** `customers` table removed (not needed for public bookings)

---

## 🎯 Customer Booking Flow

1. **Customer opens**: `/mikosplace/customer/`
2. **Scrolls to**: "Reserve your experience" section
3. **Fills in**:
   - Name ✓
   - Phone ✓
   - Email (optional)
   - Service Type (Restaurant/Catering/Cafe/Venue)
   - Guest count
   - Date
   - Special requests
4. **Gets**: Ticket number (e.g., `#MP-301`)
5. **Tracks**: Enter ticket number in "Track Your Booking" section

---

## 🔐 Admin Features

After logging in to `/admin/`, admins can:
- ✅ View all bookings
- ✅ Update booking status (Pending → Confirmed → In Progress → Completed)
- ✅ Manage menu items
- ✅ Manage venues
- ✅ Manage staff
- ✅ View reports & analytics

---

## 📝 Important Notes

- **No customer registration/login** — Everything is public and ticket-based
- **Database credentials** — Check `includes/db.php` if needed (default works with Laragon)
- **Logo** — Drop your `mikosplace.jpg` in the `assets/` folder
- **Change admin password** — Use phpMyAdmin or PHP's `password_hash()`

---

## ✨ You're All Set!

The system is now fully functional and streamlined. Customers can book directly without creating accounts. Admins manage everything from the dashboard.

