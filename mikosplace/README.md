# Miko's Place — Restaurant Management System
**Stack:** PHP 8+, MySQL (via PDO), HTML/CSS/JS — runs on Laragon

---

## 📁 Folder Structure

```
mikosplace/
├── database.sql          ← Run this FIRST in phpMyAdmin
├── includes/
│   ├── db.php            ← PDO connection (edit credentials if needed)
│   └── auth.php          ← Session-based admin guard
├── admin/
│   ├── login.php         ← Admin login page
│   ├── index.php         ← Full admin dashboard
│   ├── logout.php        ← Destroys session
│   └── api.php           ← All admin AJAX endpoints (protected)
├── customer/
│   ├── index.php         ← Public-facing website
│   └── api.php           ← Public AJAX endpoints (no auth)
└── assets/
    └── mikosplace.jpg    ← Drop your logo/photo here
```

---

## ⚡ Setup Steps

### 1. Copy project to Laragon `www`
```
C:\laragon\www\mikosplace\
```

### 2. Create the database
- Open **phpMyAdmin** → http://localhost/phpmyadmin
- Click **Import** → select `database.sql` → Go
- Or from terminal:
  ```bash
  mysql -u root < database.sql
  ```

### 3. Check DB credentials (if needed)
Edit `includes/db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'mikosplace');
define('DB_USER', 'root');
define('DB_PASS', '');   // Laragon default is empty
```

### 4. Add your logo
Drop `mikosplace.jpg` into the `assets/` folder.

### 5. Open in browser
| Page | URL |
|------|-----|
| Customer site | http://localhost/mikosplace/customer/ |
| Admin login   | http://localhost/mikosplace/admin/login.php |

**Default admin credentials:**
- Username: `admin`
- Password: `admin123`

> ⚠️ Change the password in phpMyAdmin after first login:  
> `UPDATE admin_users SET password = PASSWORD_HASH('your-new-password', PASSWORD_BCRYPT) WHERE username = 'admin';`  
> Or use PHP: `echo password_hash('your-new-password', PASSWORD_BCRYPT);`

---

## 🔑 Features

### Admin Dashboard (`/admin/`)
- **Overview** — Live stats: weekly bookings, pending count, monthly revenue, active dishes
- **Bookings** — Search/filter, update status (pending → confirmed → in_progress → completed → cancelled), delete, add new bookings
- **Menu** — Search by name/category, toggle availability, add/delete items
- **Venues** — Toggle available/unavailable, view details
- **Team** — Search staff, update shift status, add/remove members
- **Reports** — Breakdown by service type, top dishes, status counts, monthly revenue

### Customer Page (`/customer/`)
- **Hero** — Brand intro with pricing highlights
- **Services strip** — Visual overview of all services
- **Menu Browser** — Live search + filter by category (Restaurant / Catering / Cafe / Pastry)
- **Venue Cards** — Rates, capacity, availability, one-click pre-fill booking
- **Reservation Form** — Submits inquiry to DB, returns ticket number
- **Booking Tracker** — Enter ticket number (#MP-XXX) to see live status

---

## 🗄 Database Tables
| Table | Purpose |
|-------|---------|
| `admin_users` | Admin login accounts |
| `menu_items` | All menu dishes and drinks |
| `venues` | Event venue packages |
| `bookings` | All customer reservations & orders |
| `booking_items` | Menu items linked to a booking |
| `staff` | Team members and shift info |

---

## 🔧 Customization Tips
- Change brand colors in the `<style>` `:root` variables in each file
- Add more venue types by expanding the `ENUM` in `database.sql`
- Add payment status column to `bookings` by running:  
  `ALTER TABLE bookings ADD COLUMN payment_status ENUM('unpaid','partial','paid') DEFAULT 'unpaid';`
- Replace `password_verify` with your own auth system if integrating an existing user table
