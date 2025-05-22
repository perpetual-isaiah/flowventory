<?php
session_start();
require_once 'config.php';

// Only Admins (role_id = 1) can delete products
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

// Check if product ID is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $product_id = $_GET['id'];
    $company_id = $_SESSION['company_id']; // To ensure deletion is scoped to their company

    // Optional: Verify product belongs to this company
    $check = $pdo->prepare("SELECT * FROM products WHERE product_id = ? AND company_id = ?");
    $check->execute([$product_id, $company_id]);

    if ($check->rowCount() > 0) {
        $delete = $pdo->prepare("DELETE FROM products WHERE product_id = ?");
        if ($delete->execute([$product_id])) {
            $_SESSION['success'] = "Product deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete the product.";
        }
    } else {
        $_SESSION['error'] = "Product not found or does not belong to your company.";
    }
} else {
    $_SESSION['error'] = "Invalid product ID.";
}

// Redirect back to product management
header("Location: product_management.php");
exit();
?>
