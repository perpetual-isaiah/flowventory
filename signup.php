<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $companyName = trim($_POST['company_name']);
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $role_id = 1; // Default role for new company registration (Admin)

    try {
        // Check if company already exists
        $stmt = $pdo->prepare("SELECT company_id FROM companies WHERE company_name = ?");
        $stmt->execute([$companyName]);
        $company = $stmt->fetch();

        if ($company) {
            $company_id = $company['company_id'];
        } else {
            // Insert new company with status = 0 (pending approval)
            $stmt = $pdo->prepare("INSERT INTO companies (company_name, status) VALUES (?, 0)");
            $stmt->execute([$companyName]);
            $company_id = $pdo->lastInsertId();
        }

        // Insert user with company_id and role_id = 1 (Admin)
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password_hash, company_id, role_id)
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $company_id, $role_id]);

        echo "Signup successful! Your company is pending approval.";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
