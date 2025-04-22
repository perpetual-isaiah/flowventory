<?php
session_start();
require_once 'config.php';

// Ensure the user is an Admin
if ($_SESSION['role_id'] != 1) {
    header("Location: index.php"); // Redirect non-admin users
    exit();
}

$user_id = $_GET['id'];

// Delete the user from the database
$stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);

header("Location: admin_dashboard.php"); // Redirect back to the dashboard
exit();
?>
