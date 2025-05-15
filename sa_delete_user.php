<?php
session_start();
require_once 'config.php';

// Ensure user is logged in and is Super Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 5) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    try {
        // Check if the user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'User not found']);
            exit();
        }

        // Delete related records in the supplier_products table
        $deleteSupplierProductsStmt = $pdo->prepare("DELETE FROM supplier_products WHERE supplier_id = ?");
        $deleteSupplierProductsStmt->execute([$user_id]);

        // Delete the user from the database
        $deleteStmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
        $deleteStmt->execute([$user_id]);

        // Check if the user was deleted
        if ($deleteStmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete the user']);
        }

    } catch (PDOException $e) {
        // Log and display error message
        error_log($e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No user ID provided']);
}
?>
