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

    $ensured = true;
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
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            http_response_code(500);
            die(json_encode(['error' => 'A system error occurred. Please try again later.']));
        }
        ensureBookingSchema($pdo);
    }
    return $pdo;
}
