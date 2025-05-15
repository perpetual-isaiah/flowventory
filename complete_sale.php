<?php
session_start();
require_once 'config.php';

// Only allow Seller role (role_id = 2)
if ($_SESSION['role_id'] != 2) {
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['sale_started']) || !$_SESSION['sale_started']) {
    header("Location: start_sale.php");
    exit();
}

$receipt = null;
$errorMessage = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['cart'])) {
        $errorMessage = "Cart is empty. Cannot complete sale.";
    } else {
        $payment_method = $_POST['payment_method'] ?? null;
        if (!$payment_method) {
            $errorMessage = "Payment method is required.";
        } else {
            try {
                $pdo->beginTransaction();

                $totalSaleAmount = 0;
                $soldItems = [];

                foreach ($_SESSION['cart'] as $item) {
                    $total_amount = $item['price'] * $item['quantity'];

                    // Insert sale row
                    $stmt = $pdo->prepare("INSERT INTO sales (product_id, quantity, price, sale_date, company_id, user_id, discount, total_amount) VALUES (?, ?, ?, NOW(), ?, ?, 0.00, ?)");
                    $stmt->execute([
                        $item['product_id'],
                        $item['quantity'],
                        $item['price'],
                        $_SESSION['company_id'],
                        $_SESSION['user_id'] ?? null,
                        $total_amount
                    ]);

                    // Update stock
                   $stmtUpdateStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ? AND company_id = ? AND stock >= ?");
                    $stmtUpdateStock->execute([
                        $item['quantity'],
                        $item['product_id'],
                        $_SESSION['company_id'],
                        $item['quantity']
                    ]);


                    if ($stmtUpdateStock->rowCount() === 0) {
                        throw new Exception("Insufficient stock for product ID {$item['product_id']}.");
                    }

                    $totalSaleAmount += $total_amount;
                    $soldItems[] = $item;
                }

                $pdo->commit();

                // Prepare receipt data
                $receipt = [
                    'items' => $soldItems,
                    'total' => $totalSaleAmount,
                    'payment_method' => htmlspecialchars($payment_method),
                    'date' => date('Y-m-d H:i:s'),
                    'user_id' => $_SESSION['user_id'] ?? 'N/A',
                ];

                // Clear cart and sale flag
                $_SESSION['cart'] = [];
                $_SESSION['sale_started'] = false;

            } catch (Exception $e) {
                $pdo->rollBack();
                $errorMessage = "Sale failed: " . $e->getMessage();
            }
        }
    }
} else {
    header("Location: start_sale.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sale Receipt</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 30px; background: #f7f9fc; }
        .receipt {
            background: white;
            padding: 20px 30px;
            max-width: 600px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; margin-bottom: 20px; color: #333; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #0066cc;
            color: white;
        }
        .total-row td {
            font-weight: bold;
            font-size: 1.1em;
        }
        .info {
            margin-bottom: 20px;
            font-size: 0.9em;
            color: #555;
        }
        .btn-back {
            display: block;
            text-align: center;
            padding: 12px 25px;
            background-color: #0066cc;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 0 auto;
            width: 180px;
        }
        .btn-back:hover {
            background-color: #004999;
        }
        .error {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: #f8d7da;
            color: #842029;
            border-radius: 8px;
            border: 1px solid #f5c2c7;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>

<?php if ($errorMessage): ?>
    <div class="error"><?= htmlspecialchars($errorMessage) ?></div>
    <a href="start_sale.php" class="btn-back">Back to Sale</a>
<?php elseif ($receipt): ?>
    <div class="receipt">
        <h2>🧾 Sale Receipt</h2>
        <div class="info">
            <strong>Date:</strong> <?= htmlspecialchars($receipt['date']) ?><br>
            <strong>Processed By User ID:</strong> <?= htmlspecialchars($receipt['user_id']) ?><br>
            <strong>Payment Method:</strong> <?= htmlspecialchars(ucfirst($receipt['payment_method'])) ?>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Price (₦)</th>
                    <th>Subtotal (₦)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($receipt['items'] as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($item['price'], 2) ?></td>
                        <td><?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr class="total-row">
                    <td colspan="3" style="text-align:right;">Total:</td>
                    <td><?= number_format($receipt['total'], 2) ?></td>
                </tr>
            </tbody>
        </table>

        <a href="start_sale.php" class="btn-back">Start New Sale</a>
    </div>
<?php endif; ?>

</body>
</html>
