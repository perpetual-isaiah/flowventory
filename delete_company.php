<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

// Super Admin only
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 5) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['company_id'])) {
    $companyId = $_POST['company_id'];

    try {
        // Delete users first (if any) to avoid FK constraint issues
        $stmt = $pdo->prepare("DELETE FROM users WHERE company_id = ?");
        $stmt->execute([$companyId]);

        // Delete company
        $stmt = $pdo->prepare("DELETE FROM companies WHERE company_id = ?");
        $stmt->execute([$companyId]);

        echo json_encode(['success' => true, 'message' => 'Company successfully deleted.']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error deleting company.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
