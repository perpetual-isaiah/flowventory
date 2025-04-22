<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

// Fetch user role
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT role_id FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$role_id = $user['role_id'];

// Display different content based on role
if ($role_id == 1) {
    // Admin Dashboard
    echo "<h1>Admin Dashboard</h1>";
    echo "<p>Welcome Admin! Manage users, products, and reports here.</p>";
    // Admin functionality like user management, reports, etc.
} else if ($role_id == 2) {
    // Seller Dashboard
    echo "<h1>Seller Dashboard</h1>";
    echo "<p>Welcome Seller! Manage your products and view sales here.</p>";
    // Seller functionality like product management, sales, etc.
} else {
    echo "Access Denied!";
}
?>
