-- ============================================================
--  Miko's Place — Database Setup
--  Run this in phpMyAdmin or via: mysql -u root < database.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS mikosplace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mikosplace;

-- -----------------------------------------------------------
-- MENU ITEMS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS menu_items (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120)   NOT NULL,
    category    ENUM('restaurant','catering','cafe','pastry') NOT NULL,
    price       DECIMAL(10,2)  NOT NULL,
    description TEXT,
    is_available TINYINT(1)    DEFAULT 1,
    created_at  TIMESTAMP      DEFAULT CURRENT_TIMESTAMP
);

-- -----------------------------------------------------------
-- VENUES
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS venues (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120)   NOT NULL,
    type        ENUM('indoor','outdoor','premium') NOT NULL,
    capacity    INT            NOT NULL,
    rate        DECIMAL(10,2)  NOT NULL,
    description TEXT,
    is_available TINYINT(1)    DEFAULT 1
);

-- -----------------------------------------------------------
-- BOOKINGS (reservations / catering orders)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS bookings (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    ticket_no       VARCHAR(20)  NOT NULL UNIQUE,
    customer_name   VARCHAR(120) NOT NULL,
    customer_email  VARCHAR(160),
    customer_phone  VARCHAR(30),
    service_type    ENUM('restaurant','catering','cafe','venue') NOT NULL,
    venue_id        INT          REFERENCES venues(id),
    details         TEXT,
    event_date      DATE,
    pax             INT          DEFAULT 1,
    total_amount    DECIMAL(10,2) DEFAULT 0.00,
    status          ENUM('pending','confirmed','in_progress','completed','cancelled') DEFAULT 'pending',
    notes           TEXT,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

-- -----------------------------------------------------------
-- BOOKING ITEMS (menu items linked to a booking)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS booking_items (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    booking_id  INT NOT NULL REFERENCES bookings(id) ON DELETE CASCADE,
    menu_item_id INT NOT NULL REFERENCES menu_items(id),
    quantity    INT NOT NULL DEFAULT 1,
    unit_price  DECIMAL(10,2) NOT NULL
);

-- -----------------------------------------------------------
-- STAFF
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS staff (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(120) NOT NULL,
    role        VARCHAR(80)  NOT NULL,
    assignment  VARCHAR(160),
    shift_start TIME,
    shift_end   TIME,
    status      ENUM('on_duty','off_duty','prepping','on_leave') DEFAULT 'on_duty'
);

-- -----------------------------------------------------------
-- CUSTOMERS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS customers (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    email       VARCHAR(160)  NOT NULL UNIQUE,
    password    VARCHAR(255)  NOT NULL,
    full_name   VARCHAR(120)  NOT NULL,
    phone       VARCHAR(30),
    address     TEXT,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

-- -----------------------------------------------------------
-- ADMIN USERS
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS admin_users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(80)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    full_name   VARCHAR(120),
    created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- SEED DATA
-- ============================================================

-- Default admin (password: admin123)
INSERT INTO admin_users (username, password, full_name) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Operations Admin');

-- Menu Items
INSERT INTO menu_items (name, category, price, description) VALUES
('Buttered Chicken',      'restaurant', 299.00, 'Tender chicken in rich butter sauce, good for 2-3 pax'),
('Garlic Shrimp',         'restaurant', 349.00, 'Sautéed shrimp in garlic butter, good for 2-3 pax'),
('Calamares',             'restaurant', 249.00, 'Crispy fried squid rings with dipping sauce'),
('Tuna Kinilaw',          'restaurant', 279.00, 'Fresh tuna cured in vinegar and spices'),
('Chicken Cordon Bleu',   'restaurant', 399.00, 'Classic chicken stuffed with ham and cheese'),
('Seafood Platter',       'restaurant', 599.00, 'Mixed seafood for 3-4 pax'),
('Canton Guisado',        'catering',   0.00,  'Stir-fried noodles for events (price per tray)'),
('Pancit Palabok',        'catering',   0.00,  'Filipino noodles in shrimp sauce (price per tray)'),
('Brewed Coffee',         'cafe',       89.00, 'Freshly brewed barako coffee'),
('Pastry of the Day',     'pastry',     75.00, 'Ask our staff for today\'s selection');

-- Venues
INSERT INTO venues (name, type, capacity, rate, description) VALUES
('Pool w/ Pavilion',  'outdoor', 50, 10000.00, 'Open-air poolside setting with covered pavilion'),
('Banquet Hall',      'indoor',  40, 20000.00, 'Elegant air-conditioned hall for formal events'),
('Function Hall',     'premium', 50, 25000.00, 'Premium fully-equipped hall for all occasions');

-- Staff
INSERT INTO staff (name, role, assignment, shift_start, shift_end, status) VALUES
('Juan dela Cruz',   'Floor Supervisor',    'Restaurant Operations',       '10:00:00', '19:00:00', 'on_duty'),
('Maria Santos',     'Head Chef',           'Main Kitchen and Catering',   '09:00:00', '18:00:00', 'on_duty'),
('Ana Reyes',        'Pastry Lead',         'Cafe and Pastries',           '08:00:00', '17:00:00', 'prepping'),
('Carlo Mendoza',    'Events Coordinator',  'Pool, Banquet, Function Hall','11:00:00', '20:00:00', 'on_duty');

-- Sample bookings  
INSERT INTO bookings (ticket_no, customer_name, customer_email, customer_phone, service_type, pax, total_amount, status, event_date) VALUES
('#MP-201', 'Jose Rizal',       'jose@example.com', '09171234567', 'restaurant', 3,  548.00, 'confirmed',   CURDATE()),
('#MP-202', 'Apolinario Mabini', 'apo@example.com', '09189876543', 'catering',   50, 0.00,   'in_progress', CURDATE()),
('#MP-203', 'Gabriela Silang',  'gab@example.com', '09201112222', 'cafe',        2,  164.00, 'completed',   CURDATE());
