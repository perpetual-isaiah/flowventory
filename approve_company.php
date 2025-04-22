<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['company_id'])) {
    $companyId = $_POST['company_id'];

    try {
        if (isset($_POST['approve'])) {
            // Approve company
            $stmt = $pdo->prepare("UPDATE companies SET status = 1 WHERE company_id = ?");
            $stmt->execute([$companyId]);
            echo "Company approved successfully!";
        } elseif (isset($_POST['reject'])) {
            // Reject company (could delete or mark as rejected)
            $stmt = $pdo->prepare("UPDATE companies SET status = -1 WHERE company_id = ?");
            $stmt->execute([$companyId]);
            echo "Company rejected!";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
