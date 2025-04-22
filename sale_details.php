<?php
session_start();
require_once 'config.php';

if ($_SESSION['role_id'] != 2 || !isset($_SESSION['cart'])) {
    header("Location: index.php");
    exit();
}

$company_id = $_SESSION['company_id'];
$seller_id = $_SESSION['user_id'];

foreach ($_SESSION['cart'] as $item) {
    $stmt = $pdo->prepare("INSERT INTO sales (product_id, quantity, price, seller_id, company_id, sale_date)
                           VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([
        $item['product_id'],
        $item['quantity'],
        $item['price'],
        $seller_id,
        $company_id
    ]);

    // Decrease stock
    $update = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");
    $update->execute([$item['quantity'], $item['product_id']]);
}

unset($_SESSION['cart']);
unset($_SESSION['sale_started']);

echo "<h2>Sale Completed!</h2><a href='start_sale.php'>Start New Sale</a>";
