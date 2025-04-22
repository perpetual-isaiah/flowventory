<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'inventory_system';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // No echo here — we don't want "Connected successfully" on every page
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
