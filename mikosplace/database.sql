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
    image_path  VARCHAR(255)   DEFAULT 'default-dish.jpg',
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
    event_time      TIME,
    pax             INT          DEFAULT 1,
    total_amount    DECIMAL(10,2) DEFAULT 0.00,
    discount_percent DECIMAL(5,2) DEFAULT 0.00,
    final_amount    DECIMAL(10,2) DEFAULT 0.00,
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
('Pancit Guisado',            'restaurant', 200.00, 'Stir-fried mixed noodles with vegetables, pork, and shrimp.'),
('Pancit Bihon',              'restaurant', 185.00, 'Thin rice noodles with soy sauce, garlic, vegetables, and chicken.'),
('Lumpiang Shanghai',         'restaurant', 120.00, 'Crispy pork spring rolls served with sweet and sour dipping sauce.'),
('Lumpiang Sariwa',           'restaurant', 95.00, 'Fresh spring rolls with ubod and vegetables topped with peanut sauce.'),
('Pork Adobo',                'restaurant', 250.00, 'Pork belly braised in soy sauce, vinegar, garlic, and peppercorns.'),
('Crispy Pata',               'restaurant', 565.00, 'Deep-fried pork knuckle with crunchy skin and tender meat.'),
('Lechon Kawali',             'restaurant', 275.00, 'Crispy pork belly chunks served with liver sauce.'),
('Pork Menudo',               'restaurant', 215.00, 'Pork stew with tomato sauce, liver, potatoes, raisins, and carrots.'),
('Kare-Kare',                 'restaurant', 415.00, 'Oxtail and tripe in peanut sauce served with shrimp paste.'),
('Beef Caldereta',            'restaurant', 330.00, 'Hearty beef stew with tomato sauce, liver spread, cheese, and peppers.'),
('Bistek Tagalog',            'restaurant', 295.00, 'Soy and calamansi beef topped with onion rings.'),
('Chicken Adobo',             'restaurant', 225.00, 'Chicken simmered in garlic, soy sauce, vinegar, and bay leaves.'),
('Chicken Inasal',            'restaurant', 180.00, 'Visayan-style grilled chicken marinated in lemongrass, calamansi, and achuete oil.'),
('Chicken Afritada',          'restaurant', 210.00, 'Chicken stewed in tomato sauce with potatoes, carrots, and bell peppers.'),
('Inihaw na Pusit',           'restaurant', 300.00, 'Charcoal-grilled squid stuffed with onions and tomatoes.'),
('Daing na Bangus',           'restaurant', 220.00, 'Deep-fried milkfish marinated in vinegar, garlic, and peppercorns.'),
('Sinigang na Hipon',         'restaurant', 305.00, 'Shrimp in a sour tamarind broth with local vegetables.'),
('Pinakbet',                  'restaurant', 175.00, 'Mixed vegetables sauteed in shrimp paste with crispy pork bits.'),
('Ginataang Sitaw at Kalabasa','restaurant',160.00, 'String beans and squash cooked in rich coconut milk.'),
('Halo-Halo',                 'restaurant', 125.00, 'Shaved ice dessert with sweet beans, fruits, leche flan, and ube ice cream.'),
('Leche Flan',                'restaurant', 95.00, 'Velvety caramel custard dessert.'),
('Buko Pandan',               'restaurant', 105.00, 'Pandan jelly and young coconut in sweetened cream.'),
('Turon',                     'restaurant', 70.00, 'Caramelized banana and jackfruit spring roll dessert.'),
('Cassava Cake',              'restaurant', 85.00, 'Moist cassava cake finished with creamy custard.'),
('Sago''t Gulaman',           'restaurant', 70.00, 'Classic iced Filipino drink with syrup, tapioca pearls, and gelatin.'),
('Fresh Buko Juice',          'restaurant', 90.00, 'Naturally sweet coconut water served chilled.'),
('Calamansi Juice',           'restaurant', 80.00, 'Freshly squeezed native lime drink served iced or hot.'),
('Mango Shake',               'restaurant', 115.00, 'Creamy ripe mango shake blended with milk and ice.');

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
('#MP-201', 'Jose Rizal',        'jose@example.com', '09171234567', 'restaurant', 4,  0.00, 'confirmed',   CURDATE()),
('#MP-202', 'Apolinario Mabini', 'apo@example.com',  '09189876543', 'venue',     50, 10000.00, 'in_progress', CURDATE()),
('#MP-203', 'Gabriela Silang',   'gab@example.com',  '09201112222', 'restaurant', 6, 0.00, 'pending',     CURDATE());
