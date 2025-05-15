<?php 
session_start();
require_once 'config.php';

// Only allow sellers
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 2) {
    header("Location: index.php");
    exit();
}

// Start new sale session
$_SESSION['cart'] = [];
$_SESSION['sale_started'] = true;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Starting Sale</title>
    <meta http-equiv="refresh" content="2;url=process_sale.php">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .message-box {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        h2 {
            color: #27ae60;
        }
        p {
            color: #555;
            font-size: 1rem;
        }
        .loader {
            margin-top: 20px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            display: inline-block;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="message-box">
        <h2>✅ New Sale Session Started</h2>
        <p>Redirecting to sale processing page...</p>
        <div class="loader"></div>
    </div>
</body>
</html>
