<?php
// includes/db.php — PDO database connection

/**
 * Basic .env loader
 */
function loadEnv(string $path): void {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

loadEnv(__DIR__ . '/../.env');

define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_NAME', $_ENV['DB_NAME'] ?? 'mikosplace');
define('DB_USER', $_ENV['DB_USER'] ?? 'root');
define('DB_PASS', $_ENV['DB_PASS'] ?? '');
define('DB_CHARSET', $_ENV['DB_CHARSET'] ?? 'utf8mb4');

function ensureBookingSchema(PDO $pdo): void {
    static $ensured = false;
    if ($ensured) {
        return;
    }

    $columnExists = static function (string $table, string $column) use ($pdo): bool {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?'
        );
        $stmt->execute([DB_NAME, $table, $column]);
        return (int)$stmt->fetchColumn() > 0;
    };

    if (!$columnExists('bookings', 'pricing_notes')) {
        $pdo->exec('ALTER TABLE bookings ADD COLUMN pricing_notes TEXT NULL AFTER notes');
    }

    if (!$columnExists('bookings', 'discount_percent')) {
        $pdo->exec('ALTER TABLE bookings ADD COLUMN discount_percent DECIMAL(5,2) DEFAULT 0.00 AFTER total_amount');
    }

    if (!$columnExists('bookings', 'final_amount')) {
        $pdo->exec('ALTER TABLE bookings ADD COLUMN final_amount DECIMAL(10,2) DEFAULT 0.00 AFTER discount_percent');
    }

    if (!$columnExists('bookings', 'event_time')) {
        $pdo->exec('ALTER TABLE bookings ADD COLUMN event_time TIME NULL AFTER event_date');
    }

    if (!$columnExists('menu_items', 'image_path')) {
        $pdo->exec('ALTER TABLE menu_items ADD COLUMN image_path VARCHAR(255) DEFAULT "default-dish.jpg" AFTER description');
    }

    // Run migration for existing items if they still have the default image
    $checkMig = $pdo->query("SELECT COUNT(*) FROM menu_items WHERE image_path = 'default-dish.jpg'")->fetchColumn();
    if ($checkMig > 0) {
        $mapping = [
            'Pancit Guisado' => 'pancit-guisado.jpg',
            'Pancit Bihon' => 'pancit-bihon.jpg',
            'Pork Adobo' => 'pork-adobo.jpg',
            'Crispy Pata' => 'crispy-pata.jpg',
            'Lechon Kawali' => 'lechon-kawali.webp',
            'Pork Menudo' => 'pork-menudo.jpg',
            'Kare-Kare' => 'beef-kare.webp',
            'Beef Caldereta' => 'beef-caldereta.jpg',
            'Lumpiang Shanghai' => 'lumpiang-shanghai.jpg',
            'Lumpiang Sariwa' => 'lumpiang-sariwa.jpg',
            'Bistek Tagalog' => 'bistek-tagalog.jpg',
            'Chicken Adobo' => 'chicken-adobo.jpg',
            'Chicken Inasal' => 'chicken-inasal.jpg',
            'Chicken Afritada' => 'chicken-afritada.jpg',
            'Inihaw na Pusit' => 'inihaw-pusit.jpg',
            'Daing na Bangus' => 'daing-bangus.webp',
            'Sinigang na Hipon' => 'sinigang-hipon.webp',
            'Pinakbet' => 'pinakbet.jpg',
            'Ginataang Sitaw at Kalabasa' => 'ginataang-sitaw-kalabasa.jpg',
            'Halo-Halo' => 'halo-halo.jpg',
            'Leche Flan' => 'leche-flan.jpg',
            'Buko Pandan' => 'buko-pandan.webp',
            'Turon' => 'turon.jpg',
            'Cassava Cake' => 'casava-cake.jpg',
            "Sago't Gulaman" => 'sagot-gulaman.webp',
            'Fresh Buko Juice' => 'fresh-buko.jpg',
            'Calamansi Juice' => 'calamansi-juice.jpg',
            'Mango Shake' => 'mango-shake.jpg',
        ];
        $migStmt = $pdo->prepare("UPDATE menu_items SET image_path = ? WHERE name = ? AND image_path = 'default-dish.jpg'");
        foreach ($mapping as $name => $path) {
            $migStmt->execute([$path, $name]);
        }

    }

    $fixStmt = $pdo->prepare("UPDATE menu_items SET image_path = ? WHERE name = ?");
    $fixStmt->execute(['lumpiang-shanghai.jpg', 'Lumpiang Shanghai']);
    $fixStmt->execute(['lumpiang-sariwa.jpg', 'Lumpiang Sariwa']);

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS booking_addons (
            id INT AUTO_INCREMENT PRIMARY KEY,
            booking_id INT NOT NULL,
            addon_code VARCHAR(80) DEFAULT NULL,
            addon_name VARCHAR(160) NOT NULL,
            addon_type VARCHAR(80) NOT NULL DEFAULT "addon",
            quantity INT NOT NULL DEFAULT 1,
            unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            notes TEXT NULL,
            CONSTRAINT fk_booking_addons_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
    );

    $restaurantMenuSeed = [
        ['Pancit Guisado', 'restaurant', 0.00, 'Stir-fried mixed noodles with vegetables, pork, and shrimp. Final pricing depends on selected portion size.', 'pancit-guisado.jpg'],
        ['Pancit Bihon', 'restaurant', 0.00, 'Thin rice noodles with soy sauce, garlic, vegetables, and chicken. Final pricing depends on selected portion size.', 'pancit-bihon.jpg'],
        ['Lumpiang Shanghai', 'restaurant', 0.00, 'Crispy pork spring rolls with sweet and sour dipping sauce. Final pricing depends on selected portion size.', 'lumpiang-shanghai.jpg'],
        ['Lumpiang Sariwa', 'restaurant', 0.00, 'Fresh spring rolls with ubod and vegetables topped with peanut sauce. Final pricing depends on selected portion size.', 'lumpiang-sariwa.jpg'],
        ['Pork Adobo', 'restaurant', 0.00, 'Pork belly braised in soy sauce, vinegar, garlic, and peppercorns. Final pricing depends on selected portion size.', 'pork-adobo.jpg'],
        ['Crispy Pata', 'restaurant', 0.00, 'Deep-fried pork knuckle with crunchy skin and tender meat. Final pricing depends on selected portion size.', 'crispy-pata.jpg'],
        ['Lechon Kawali', 'restaurant', 0.00, 'Crispy deep-fried pork belly chunks served with liver sauce. Final pricing depends on selected portion size.', 'lechon-kawali.webp'],
        ['Pork Menudo', 'restaurant', 0.00, 'Pork stew with tomato sauce, liver, potatoes, raisins, and carrots. Final pricing depends on selected portion size.', 'pork-menudo.jpg'],
        ['Kare-Kare', 'restaurant', 0.00, 'Oxtail and tripe in rich peanut sauce served with shrimp paste. Final pricing depends on selected portion size.', 'beef-kare.webp'],
        ['Beef Caldereta', 'restaurant', 0.00, 'Beef stew with tomato sauce, liver spread, cheese, and peppers. Final pricing depends on selected portion size.', 'beef-caldereta.jpg'],
        ['Bistek Tagalog', 'restaurant', 0.00, 'Soy and calamansi beef topped with onion rings. Final pricing depends on selected portion size.', 'bistek-tagalog.jpg'],
        ['Chicken Adobo', 'restaurant', 0.00, 'Chicken simmered in garlic, soy sauce, vinegar, and bay leaves. Final pricing depends on selected portion size.', 'chicken-adobo.jpg'],
        ['Chicken Inasal', 'restaurant', 0.00, 'Visayan-style grilled chicken with lemongrass, calamansi, and achuete oil. Final pricing depends on selected portion size.', 'chicken-inasal.jpg'],
        ['Chicken Afritada', 'restaurant', 0.00, 'Chicken stew in tomato sauce with potatoes, carrots, and bell peppers. Final pricing depends on selected portion size.', 'chicken-afritada.jpg'],
        ['Inihaw na Pusit', 'restaurant', 0.00, 'Charcoal-grilled squid stuffed with onions and tomatoes. Final pricing depends on selected portion size.', 'inihaw-pusit.jpg'],
        ['Daing na Bangus', 'restaurant', 0.00, 'Deep-fried milkfish marinated in vinegar, garlic, and peppercorns. Final pricing depends on selected portion size.', 'daing-bangus.webp'],
        ['Sinigang na Hipon', 'restaurant', 0.00, 'Shrimp in a sour tamarind broth with local vegetables. Final pricing depends on selected portion size.', 'sinigang-hipon.webp'],
        ['Pinakbet', 'restaurant', 0.00, 'Mixed vegetables sauteed in shrimp paste and topped with crispy pork bits. Final pricing depends on selected portion size.', 'pinakbet.jpg'],
        ['Ginataang Sitaw at Kalabasa', 'restaurant', 0.00, 'String beans and squash cooked in savory coconut milk. Final pricing depends on selected portion size.', 'ginataang-sitaw-kalabasa.jpg'],
        ['Halo-Halo', 'restaurant', 0.00, 'Shaved ice dessert with sweet beans, fruits, leche flan, and ube ice cream. Final pricing depends on selected portion size.', 'halo-halo.jpg'],
        ['Leche Flan', 'restaurant', 0.00, 'Velvety caramel custard dessert. Final pricing depends on selected portion size.', 'leche-flan.jpg'],
        ['Buko Pandan', 'restaurant', 0.00, 'Pandan jelly and young coconut in sweetened cream. Final pricing depends on selected portion size.', 'buko-pandan.webp'],
        ['Turon', 'restaurant', 0.00, 'Caramelized banana and jackfruit spring roll dessert. Final pricing depends on selected portion size.', 'turon.jpg'],
        ['Cassava Cake', 'restaurant', 0.00, 'Moist cassava cake topped with creamy custard. Final pricing depends on selected portion size.', 'casava-cake.jpg'],
        ["Sago't Gulaman", 'restaurant', 0.00, 'Classic iced Filipino drink with syrup, tapioca pearls, and gelatin. Final pricing depends on selected portion size.', 'sagot-gulaman.webp'],
        ['Fresh Buko Juice', 'restaurant', 0.00, 'Naturally sweet coconut water served chilled. Final pricing depends on selected portion size.', 'fresh-buko.jpg'],
        ['Calamansi Juice', 'restaurant', 0.00, 'Freshly squeezed native lime drink served iced or hot. Final pricing depends on selected portion size.', 'calamansi-juice.jpg'],
        ['Mango Shake', 'restaurant', 0.00, 'Creamy ripe mango shake blended with milk and ice. Final pricing depends on selected portion size.', 'mango-shake.jpg'],
    ];

    $seedStmt = $pdo->prepare('SELECT id FROM menu_items WHERE name = ? LIMIT 1');
    $insertStmt = $pdo->prepare('INSERT INTO menu_items (name, category, price, description, image_path, is_available) VALUES (?,?,?,?,?,1)');
    foreach ($restaurantMenuSeed as [$name, $category, $price, $description, $img]) {
        $seedStmt->execute([$name]);
        if (!$seedStmt->fetchColumn()) {
            $insertStmt->execute([$name, $category, $price, $description, $img]);
        }
    }

    $ensured = true;
}

function normalizeBookingTime(?string $time): ?string {
    $time = trim((string)$time);
    if ($time === '') {
        return null;
    }

    $dateTime = DateTime::createFromFormat('H:i', $time) ?: DateTime::createFromFormat('H:i:s', $time);
    if (!$dateTime) {
        return null;
    }

    return $dateTime->format('H:i:s');
}

function findVenueBookingConflict(PDO $pdo, int $venueId, string $eventDate, ?string $eventTime, ?int $excludeBookingId = null): ?array {
    $params = [$venueId, $eventDate];
    $sql = "SELECT id, ticket_no, event_date, event_time
            FROM bookings
            WHERE venue_id = ?
              AND event_date = ?
              AND status IN ('confirmed', 'in_progress', 'completed')";

    if ($excludeBookingId !== null) {
        $sql .= ' AND id != ?';
        $params[] = $excludeBookingId;
    }

    if ($eventTime !== null) {
        $sql .= ' AND (event_time = ? OR event_time IS NULL)';
        $params[] = $eventTime;
    } else {
        $sql .= ' AND 1 = 1';
    }

    $sql .= ' ORDER BY id ASC LIMIT 1';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $conflict = $stmt->fetch();

    return $conflict ?: null;
}

function buildVenueConflictMessage(?array $conflict, ?string $fallbackDate = null, ?string $fallbackTime = null): string {
    $eventDate = $conflict['event_date'] ?? $fallbackDate;
    $eventTime = $conflict['event_time'] ?? $fallbackTime;

    $parts = [];
    if ($eventDate) {
        $parts[] = date('F j, Y', strtotime((string)$eventDate));
    }
    if ($eventTime) {
        $parts[] = date('g:i A', strtotime((string)$eventTime));
    }

    $schedule = $parts ? implode(' at ', $parts) : 'the selected schedule';

    return "This venue already has a confirmed booking for {$schedule}. Please choose a different time or venue.";
}

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            $pdo->exec("SET time_zone = '+08:00'");
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            http_response_code(500);
            die(json_encode(['error' => 'A system error occurred. Please try again later.']));
        }
        ensureBookingSchema($pdo);
    }
    return $pdo;
}
