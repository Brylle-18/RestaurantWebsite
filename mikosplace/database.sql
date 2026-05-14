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
    venue_id        INT,
    details         TEXT,
    event_date      DATE,
    pax             INT          DEFAULT 1,
    total_amount    DECIMAL(10,2) DEFAULT 0.00,
    status          ENUM('pending','confirmed','in_progress','completed','cancelled') DEFAULT 'pending',
    notes           TEXT,
    pricing_notes   TEXT,
    created_at      TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bookings_venue FOREIGN KEY (venue_id) REFERENCES venues(id)
);

-- -----------------------------------------------------------
-- BOOKING ITEMS (menu items linked to a booking)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS booking_items (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    booking_id  INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity    INT NOT NULL DEFAULT 1,
    unit_price  DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_booking_items_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    CONSTRAINT fk_booking_items_menu FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)
);

-- -----------------------------------------------------------
-- BOOKING ADD-ONS (event extras linked to a booking)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS booking_addons (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    booking_id  INT NOT NULL,
    addon_code  VARCHAR(80),
    addon_name  VARCHAR(160) NOT NULL,
    addon_type  VARCHAR(80) NOT NULL DEFAULT 'addon',
    quantity    INT NOT NULL DEFAULT 1,
    unit_price  DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    notes       TEXT,
    CONSTRAINT fk_booking_addons_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
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

-- Default admin (username: Dread, password: mikosplace2011)
INSERT INTO admin_users (username, password, full_name) VALUES
('Dread', '$2y$12$J7h2B0MWUGWjmQBTw9QhR.h.kmPU7uNrqplJCtljJoZq6kDDFtMKy', 'Dread Admin');

-- Menu Items
INSERT INTO menu_items (name, category, price, description) VALUES
('Pork Adobo',                'restaurant', 620.00, 'Slow-braised pork in soy sauce, vinegar, garlic, and bay leaves, sized for 4 pax'),
('Sinigang na Baboy',         'restaurant', 760.00, 'Tender pork in tamarind broth with kangkong, radish, okra, and tomatoes, sized for 4 pax'),
('Kare-Kare',                 'restaurant', 880.00, 'Peanut-braised beef and vegetables served with bagoong, sized for 4 pax'),
('Crispy Pata',               'restaurant', 950.00, 'Golden pork knuckle with crackling skin and soy-vinegar dip, sized for 4 pax'),
('Chicken Inasal',            'restaurant', 680.00, 'Char-grilled chicken marinated in local spices and calamansi, sized for 4 pax'),
('Pancit Canton',             'catering',   520.00, 'Wok-tossed egg noodles with vegetables and chicken, sized for 4 pax'),
('Pancit Bihon',              'catering',   480.00, 'Light rice noodles stir-fried with vegetables, pork, and citrus notes, sized for 4 pax'),
('Lumpiang Shanghai',         'catering',   420.00, 'Crisp pork spring rolls with sweet chili sauce, good for 4 pax'),
('Garlic Rice Platter',       'catering',   180.00, 'Fragrant garlic fried rice platter for 4 pax'),
('Steamed Rice Bucket',       'catering',   140.00, 'Steamed rice served family-style for 4 pax'),
('Iced Tea Pitcher',          'cafe',       180.00, 'House-brewed iced tea pitcher for 4 pax'),
('Calamansi Juice Pitcher',   'cafe',       220.00, 'Fresh calamansi cooler pitcher for 4 pax'),
('Sago''t Gulaman Pitcher',   'cafe',       240.00, 'Classic Filipino refreshment with tapioca pearls and gulaman for 4 pax'),
('Leche Flan',                'pastry',     220.00, 'Silky caramel custard platter for sharing'),
('Buko Pandan',               'pastry',     280.00, 'Chilled young coconut and pandan jelly dessert bowl for 4 pax');

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
