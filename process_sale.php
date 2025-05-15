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

$company_id = $_SESSION['company_id'] ?? 0;
$alert = "";
$alertClass = "";

$selectedProductId = '';
$selectedQuantity = 1;

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    $selectedProductId = $product_id;
    $selectedQuantity = $quantity;

    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ? AND company_id = ?");
    $stmt->execute([$product_id, $company_id]);
    $product = $stmt->fetch();

    if ($product) {
        if ($quantity > $product['stock']) {
            $alert = "Not enough stock for <strong>{$product['name']}</strong>. Available: {$product['stock']}.";
            $alertClass = "alert-danger";
        } else {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['product_id'] == $product_id) {
                    $item['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }
            unset($item); // break reference

            if (!$found) {
                $_SESSION['cart'][] = [
                    'product_id' => $product['product_id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'quantity' => $quantity
                ];
            }

            $alert = "Product <strong>{$product['name']}</strong> added to cart.";
            $alertClass = "alert-success";

            // Reset selection after successful add
            $selectedProductId = '';
            $selectedQuantity = 1;
        }
    } else {
        $alert = "Product not found or not allowed.";
        $alertClass = "alert-danger";
    }
}

// Handle remove from cart
if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    if (!empty($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $index => $item) {
            if ($item['product_id'] == $remove_id) {
                unset($_SESSION['cart'][$index]);
                $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
                $alert = "Product removed from cart.";
                $alertClass = "alert-success";
                break;
            }
        }
    }
}

// Get list of products from seller's company
$stmt = $pdo->prepare("SELECT product_id, name FROM products WHERE company_id = ? ORDER BY name ASC");
$stmt->execute([$company_id]);
$products = $stmt->fetchAll();

$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Process Sale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="mb-4">🛍️ Process Current Sale</h2>

    <?php if ($alert): ?>
        <div class="alert <?= $alertClass ?>">
            <?= $alert ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-6">
            <label for="product_id" class="form-label">Select Product</label>
            <select name="product_id" class="form-select" required>
                <option value="">-- Choose product --</option>
                <?php foreach ($products as $p): ?>
                    <option value="<?= $p['product_id'] ?>" <?= ($selectedProductId == $p['product_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" name="quantity" class="form-control" min="1" value="<?= htmlspecialchars($selectedQuantity) ?>" required>
        </div>
        <div class="col-md-2 align-self-end">
            <button type="submit" class="btn btn-primary w-100">Add</button>
        </div>
    </form>

    <h4>🛒 Cart</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th><th>Quantity</th><th>Price</th><th>Subtotal</th><th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php if (!empty($_SESSION['cart'])): ?>
            <?php foreach ($_SESSION['cart'] as $item): 
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
                <tr>
                    <td><?= htmlspecialchars($item['name']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td>$<?= number_format($subtotal, 2) ?></td>
                    <td>
                        <a href="?remove=<?= $item['product_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Remove <?= htmlspecialchars($item['name']) ?> from cart?');">
                            Remove
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" class="text-center">Cart is empty</td></tr>
        <?php endif; ?>
        </tbody>
    </table>

    <h4>Total: $<?= number_format($total, 2) ?></h4>

    <div class="mt-3 d-flex gap-2">
        <a href="seller_dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>

        <a href="sale_details.php" 
           class="btn btn-success <?= empty($_SESSION['cart']) ? 'disabled' : '' ?>" 
           <?= empty($_SESSION['cart']) ? 'aria-disabled="true" tabindex="-1"' : '' ?>>
           Complete Sale
        </a>
    </div>
</div>

</body>
</html>
