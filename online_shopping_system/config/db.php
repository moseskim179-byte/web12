<?php
// Database connection (PDO). Change these values if your MySQL is different.
$host     = 'localhost';
$dbname   = 'online_shopping';
$username = 'root';
$password = '';   // XAMPP default is empty

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    exit('Database connection failed. Check config/db.php and make sure MySQL is running.');
}
