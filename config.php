<?php
// config.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'dbl2pb1zhxan9g');
define('DB_USER', 'u9sezs09xvil4');
define('DB_PASS', 'esfemh4y63a9');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Start session for cart functionality
session_start();
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = uniqid();
}
?>
