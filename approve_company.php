<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $companyId = $_POST['company_id'] ?? null;

    if (!$companyId || !is_numeric($companyId)) {
        echo json_encode(['success' => false, 'message' => 'Invalid company ID.']);
        exit();
    }

    if (isset($_POST['approve'])) {
        $stmt = $pdo->prepare("UPDATE companies SET status = 1 WHERE company_id = ?");
        $stmt->execute([$companyId]);
        echo json_encode(['success' => true, 'message' => '✅ Company approved successfully.']);
    } elseif (isset($_POST['reject'])) {
        $stmt = $pdo->prepare("DELETE FROM companies WHERE company_id = ?");
        $stmt->execute([$companyId]);
        echo json_encode(['success' => true, 'message' => '❌ Company rejected and removed.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No action specified.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
exit();
