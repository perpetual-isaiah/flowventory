<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: index.php");
    exit();
}

$supplier_id = $_SESSION['user_id']; // assuming user_id = supplier_id

// Fetch products assigned to this supplier
$stmt = $pdo->prepare("
    SELECT p.product_id, p.name 
    FROM products p
    JOIN product_suppliers ps ON ps.product_id = p.product_id
    WHERE ps.supplier_id = ?
");
$stmt->execute([$supplier_id]);
$products = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $supply_price = $_POST['supply_price'];

    // Add pending supply entry
    $insert = $pdo->prepare("
        INSERT INTO pending_supplies (product_id, supplier_id, quantity, supply_price, status)
        VALUES (?, ?, ?, ?, 'pending')
    ");
    $insert->execute([$product_id, $supplier_id, $quantity, $supply_price]);

    echo "<script>alert('Supply submitted and pending admin approval.'); window.location.href='supplier_dashboard.php';</script>";
    exit();
}
?>

<!-- HTML form for supplier to submit quantity/price -->
<form method="POST">
    <label>Product:</label>
    <select name="product_id" required>
        <?php foreach ($products as $product): ?>
            <option value="<?= $product['product_id'] ?>"><?= $product['name'] ?></option>
        <?php endforeach; ?>
    </select>

    <label>Quantity Supplied:</label>
    <input type="number" name="quantity" min="1" required>

    <label>Supply Price per Unit:</label>
    <input type="number" name="supply_price" min="0.01" step="0.01" required>

    <button type="submit">Submit Supply</button>
</form>
