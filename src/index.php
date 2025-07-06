<?php

echo "PHP is working<br>";

if (extension_loaded('xdebug')) {
    echo "Xdebug is enabled<br>";
} else {
    echo "Xdebug is not enabled<br>";
}

$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$db   = getenv('DB_NAME');
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "MySQL connection successful<br>";
} catch (PDOException $e) {
    echo "MySQL connection failed: " . $e->getMessage();
}