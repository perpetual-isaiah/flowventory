<?php
session_start();
require_once 'config.php';

// Only allow sellers
if ($_SESSION['role_id'] != 2) {
    header("Location: index.php");
    exit();
}

// Start new sale session
$_SESSION['cart'] = [];
$_SESSION['sale_started'] = true;

header("Location: process_sale.php");
exit();
?>
