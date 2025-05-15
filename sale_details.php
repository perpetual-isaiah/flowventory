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

$modalMessage = "";
$modalType = ""; // success or error

// Cancel sale
if (isset($_POST['cancel_sale'])) {
    $_SESSION['cart'] = [];
    $_SESSION['sale_started'] = false;
    header("Location: start_sale.php");
    exit();
}

// Undo last added item
if (isset($_POST['undo_last']) && !empty($_SESSION['cart'])) {
    array_pop($_SESSION['cart']);
    $modalMessage = "Last item removed from cart.";
    $modalType = "success";
}

// Add product to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_name'], $_POST['quantity']) && !isset($_POST['undo_last']) && !isset($_POST['cancel_sale'])) {
    $product_name = trim($_POST['product_name']);
    $quantity = (int) $_POST['quantity'];

    // 🔐 Query only products from the user's company
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name = ? AND company_id = ?");
    $stmt->execute([$product_name, $_SESSION['company_id']]);
    $product = $stmt->fetch();

    if ($product) {
        if ($quantity > $product['stock']) {
            $modalMessage = "Not enough stock for <strong>{$product['name']}</strong>. Available: {$product['stock']}.";
            $modalType = "error";
        } else {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            $_SESSION['cart'][] = [
                'product_id' => $product['product_id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity
            ];

            $modalMessage = "Product <strong>{$product['name']}</strong> added to cart.";
            $modalType = "success";
        }
    } else {
        $modalMessage = "Product not found or does not belong to your company.";
        $modalType = "error";
    }
}

$total = 0;

// 🔐 Fetch products only from the logged-in user's company
$products = $pdo->prepare("SELECT name, stock FROM products WHERE company_id = ? ORDER BY name ASC");
$products->execute([$_SESSION['company_id']]);
$products = $products->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sale Details</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; padding: 20px; background-color: #f5f7fa; }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        th { background-color: #e1eaff; }
        .button {
            padding: 10px 18px;
            background-color: #0066cc;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin: 10px 5px 0 0;
        }
        .button:hover { opacity: 0.9; }
        .button.red { background-color: #e74c3c; }
        .button.orange { background-color: #f39c12; }

        .modal {
            display: <?= $modalMessage ? 'block' : 'none' ?>;
            position: fixed;
            z-index: 10;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 30px;
            width: 50%;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
            text-align: center;
        }
        .success { color: green; }
        .error { color: red; }
        form.inline { display: inline; }
        select, input[type="number"] {
            padding: 8px;
            margin-right: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .status-text {
            display: inline-block;
            font-size: 0.9em;
            margin-left: 10px;
        }
    </style>
</head>
<body>

<div class="modal" id="messageModal">
    <div class="modal-content">
        <h3 class="<?= $modalType ?>"><?= $modalMessage ?></h3>
        <button onclick="document.getElementById('messageModal').style.display='none'" class="button">OK</button>
    </div>
</div>

<h2>🛒 Point of Sale - Current Sale</h2>

<form method="POST">
    <label for="product_name">Product:</label>
    <select name="product_name" id="product_name" required onchange="updateStockHint()">
        <option value="">-- Select Product --</option>
        <?php foreach ($products as $p): ?>
            <option value="<?= htmlspecialchars($p['name']) ?>" data-stock="<?= $p['stock'] ?>">
                <?= htmlspecialchars($p['name']) ?> (Stock: <?= $p['stock'] ?>)
            </option>
        <?php endforeach; ?>
    </select>
    Quantity: <input type="number" name="quantity" id="quantity" required min="1" value="1">
    <span class="status-text" id="stockHint"></span>
    <button type="submit" class="button">Add to Cart</button>
</form>

<form method="POST" class="inline">
    <input type="hidden" name="undo_last" value="1">
    <button type="submit" class="button orange">↩ Undo Last</button>
</form>

<form method="POST" class="inline">
    <input type="hidden" name="cancel_sale" value="1">
    <button type="submit" class="button red">✖ Cancel Sale</button>
</form>

<br><br>

<table>
    <tr><th>Product</th><th>Quantity</th><th>Price</th><th>Subtotal</th></tr>
    <?php if (!empty($_SESSION['cart'])): ?>
        <?php foreach ($_SESSION['cart'] as $item): 
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;
        ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td><?= number_format($item['price'], 2) ?></td>
                <td><?= number_format($subtotal, 2) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="4" style="text-align:center;">🛒 Cart is empty</td></tr>
    <?php endif; ?>
</table>

<h3>Total: ₦<?= number_format($total, 2) ?></h3>

<form method="POST" action="complete_sale.php">
    <?php if (!empty($_SESSION['cart'])): ?>
        <label for="payment_method">Payment Method:</label>
        <select name="payment_method" required>
            <option value="">--Select--</option>
            <option value="cash">Cash</option>
            <option value="card">Card</option>
            <option value="mobile">Mobile Payment</option>
        </select>
        <br><br>
        <button type="submit" class="button">✅ Complete Sale</button>
    <?php else: ?>
        <p style="color: grey;">Add items to cart to complete sale.</p>
    <?php endif; ?>
</form>

<script>
    setTimeout(() => {
        const modal = document.getElementById("messageModal");
        if (modal) modal.style.display = "none";
    }, 3000);

    function updateStockHint() {
        const select = document.getElementById("product_name");
        const selected = select.options[select.selectedIndex];
        const stock = selected.getAttribute("data-stock");
        const hint = document.getElementById("stockHint");
        hint.innerText = stock ? `Available: ${stock}` : "";
    }
</script>

</body>
</html>
