<?php 
session_start();
require_once 'config.php';

header('Content-Type: application/json'); // Let JS know we’re sending back JSON
$response = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use LEFT JOIN to allow users without a company (like Super Admin)
    $stmt = $pdo->prepare("
        SELECT u.*, c.status AS company_status 
        FROM users u 
        LEFT JOIN companies c ON u.company_id = c.company_id 
        WHERE u.email = ?
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // Check if user exists and password matches
    if ($user && password_verify($password, $user['password_hash'])) {
        // If not Super Admin, check if company is approved
        if ($user['role_id'] != 5 && $user['company_status'] != 1) {
            $response['success'] = false;
            $response['message'] = "Your company is still pending approval.";
        } else {
            // Login success
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['email'] = $user['email'];  
            $_SESSION['company_id'] = $user['company_id'];

            $response['success'] = true;
            $response['role'] = $user['role_id'];

            // Redirect based on role
            if ($user['role_id'] == 5) {
                $response['redirect'] = 'super_admin_dashboard.php';
            } else {
                $response['redirect'] = 'dashboard.php';
            }
        }
    } else {
        $response['success'] = false;
        $response['message'] = "Invalid login credentials!";
    }
} else {
    $response['success'] = false;
    $response['message'] = "Invalid request method.";
}

echo json_encode($response);
exit();
