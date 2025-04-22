<?php
session_start();
require_once 'config.php';

if ($_SESSION['role_id'] != 2) {
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['sale_started']) || !$_SESSION['sale_started']) {
    header("Location: start_sale.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    // Fetch product info
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if ($product) {
        $_SESSION['cart'][] = [
            'product_id' => $product['product_id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity
        ];
    }
}

$total = 0;
?>

<!-- Basic HTML table to show cart -->
<h2>Current Sale</h2>
<form method="POST">
    Product ID: <input type="number" name="product_id" required>
    Quantity: <input type="number" name="quantity" required min="1">
    <button type="submit">Add to Cart</button>
</form>

<table border="1">
    <tr><th>Product</th><th>Quantity</th><th>Price</th><th>Subtotal</th></tr>
    <?php foreach ($_SESSION['cart'] as $item): 
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
    ?>
        <tr>
            <td><?= $item['name'] ?></td>
            <td><?= $item['quantity'] ?></td>
            <td><?= $item['price'] ?></td>
            <td><?= $subtotal ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<h3>Total: <?= $total ?></h3>
<a href="sale_details.php">Complete Sale</a>
