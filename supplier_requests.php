<?php
session_start();
require_once 'config.php';

// Only allow Admins (role_id = 1)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

// Approve supply request
if (isset($_GET['approve'])) {
    $request_id = $_GET['approve'];

    // Fetch the supply request
    $stmt = $pdo->prepare("SELECT * FROM supply_requests WHERE request_id = ?");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch();

    if ($request) {
        // Insert or update supplier_products
        $check = $pdo->prepare("SELECT * FROM supplier_products 
            WHERE supplier_id = ? AND product_id = ?");
        $check->execute([$request['supplier_id'], $request['product_id']]);
        $existing = $check->fetch();

        if ($existing) {
            // Update existing record
            $update = $pdo->prepare("UPDATE supplier_products 
                SET quantity_supplied = quantity_supplied + ?, supply_price = ?, is_approved = 1 
                WHERE supplier_id = ? AND product_id = ?");
            $update->execute([
                $request['quantity_requested'],  // FIXED HERE
                $request['supply_price'],
                $request['supplier_id'],
                $request['product_id']
            ]);
        } else {
            // Insert new supplier-product relationship
            $insert = $pdo->prepare("INSERT INTO supplier_products 
                (supplier_id, company_id, product_id, quantity_supplied, supply_price, is_approved) 
                VALUES (?, ?, ?, ?, ?, 1)");
            $insert->execute([
                $request['supplier_id'],
                $request['company_id'],
                $request['product_id'],
                $request['quantity_requested'],  // FIXED HERE
                $request['supply_price']
            ]);
        }

        // Update product quantity
        $updateQty = $pdo->prepare("UPDATE products SET quantity = quantity + ? WHERE product_id = ?");
        $updateQty->execute([
            $request['quantity_requested'],  // FIXED HERE
            $request['product_id']
        ]);

        // Mark the request as approved
        $approve = $pdo->prepare("UPDATE supply_requests SET is_approved = 1 WHERE request_id = ?");
        $approve->execute([$request_id]);
    }

    header("Location: supplier_requests.php");
    exit();
}


// Reject supply request
if (isset($_GET['reject'])) {
    $request_id = $_GET['reject'];
    $reject = $pdo->prepare("UPDATE supply_requests SET is_approved = 2 WHERE request_id = ?");
    $reject->execute([$request_id]);
    header("Location: supplier_requests.php");
    exit();
}

// Fetch pending requests using LEFT JOIN
try {
    $stmt = $pdo->query("
    SELECT r.*, p.name AS product_name, s.name AS supplier_name
    FROM supply_requests r
    LEFT JOIN products p ON r.product_id = p.product_id
    LEFT JOIN suppliers s ON r.supplier_id = s.user_id
    WHERE r.is_approved = 0
");

    $requests = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supplier Requests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Pending Supplier Requests</h2>
    <a href="product_management.php" class="btn btn-outline-secondary mb-3">&larr; Back</a>

    <!-- Debug: Show request count -->
    <p><strong>Fetched Requests:</strong> <?= count($requests) ?></p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Supplier</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Supply Price</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($requests): ?>
                <?php foreach ($requests as $entry): ?>
                    <tr>
                        <td><?= htmlspecialchars($entry['supplier_name'] ?? 'Unknown') ?></td>
                        <td><?= htmlspecialchars($entry['product_name'] ?? 'Unknown') ?></td>
                        <td><?= $entry['quantity_requested'] ?></td>
                        <td>$<?= number_format($entry['supply_price'], 2) ?></td>
                        <td>
                            <a href="?approve=<?= $entry['request_id'] ?>" class="btn btn-success btn-sm">Approve</a>
                            <a href="?reject=<?= $entry['request_id'] ?>" class="btn btn-danger btn-sm">Reject</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">No pending requests.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
