<?php
session_start();
require_once 'config.php';

// Only Admin (role_id = 1) can access
if ($_SESSION['role_id'] != 1 || !isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$company_id = $_SESSION['company_id'];
$product_id = $_GET['id'];

// Fetch current product data
$stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ? AND company_id = ?");
$stmt->execute([$product_id, $company_id]);
$product = $stmt->fetch();

if (!$product) {
    echo "Product not found or access denied.";
    exit();
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = floatval($_POST['price']);
    $new_stock = intval($_POST['add_stock']);

    // Update stock and product info
    $stmt = $pdo->prepare("UPDATE products SET name = ?, price = ?, stock = stock + ? WHERE product_id = ? AND company_id = ?");
    $stmt->execute([$name, $price, $new_stock, $product_id, $company_id]);

    header("Location: product_management.php?updated=1");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
            padding: 30px;
        }
        .container {
            max-width: 600px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Product</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required class="form-control">
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Price ($)</label>
            <input type="number" step="0.01" id="price" name="price" value="<?= $product['price'] ?>" required class="form-control">
        </div>
        <div class="mb-3">
            <label for="add_stock" class="form-label">Add Stock Quantity</label>
            <input type="number" id="add_stock" name="add_stock" value="0" min="0" class="form-control">
            <small class="text-muted">Current stock: <?= $product['stock'] ?></small>
        </div>
        <button type="submit" class="btn btn-success">Update Product</button>
        <a href="product_management.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>
</html>
