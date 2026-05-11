<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db_name = 'pcconfigurationcompanyphp';
$charset = 'utf8mb4';

$conn = new mysqli($host, $user, $pass, $db_name);

$dsn = "mysql:host=$host;dbname=$db_name;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

if ($conn->connect_error) die("Connection error: " . $conn->connect_error);
?>