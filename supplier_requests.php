<?php
session_start();
require_once 'config.php';

// Only allow Admin (role_id = 1)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

$admin_id = $_SESSION['user_id'];
$company_id = $_SESSION['company_id'];

// Approve supply request
if (isset($_GET['approve'])) {
    $request_id = $_GET['approve'];

    $stmt = $pdo->prepare("SELECT * FROM supply_requests WHERE request_id = ?");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch();

    if ($request && $request['company_id'] == $company_id) {
        // Get current stock and price of the product
        $productStmt = $pdo->prepare("SELECT stock, price FROM products WHERE product_id = ?");
        $productStmt->execute([$request['product_id']]);
        $product = $productStmt->fetch();

        $oldQty = $product ? $product['stock'] : 0;
        $oldPrice = $product ? $product['price'] : 0;
        $newQty = $oldQty + $request['quantity_requested'];
        $newPrice = $request['supply_price'];

        // Check if the supplier-product link already exists
        $check = $pdo->prepare("SELECT * FROM supplier_products 
            WHERE supplier_id = ? AND product_id = ?");
        $check->execute([$request['supplier_id'], $request['product_id']]);
        $existing = $check->fetch();

        if ($existing) {
            $update = $pdo->prepare("UPDATE supplier_products 
                SET quantity_supplied = quantity_supplied + ?, supply_price = ?, is_approved = 1 
                WHERE supplier_id = ? AND product_id = ?");
            $update->execute([
                $request['quantity_requested'],
                $request['supply_price'],
                $request['supplier_id'],
                $request['product_id']
            ]);
        } else {
            $insert = $pdo->prepare("INSERT INTO supplier_products 
                (supplier_id, company_id, product_id, quantity_supplied, supply_price, is_approved) 
                VALUES (?, ?, ?, ?, ?, 1)");
            $insert->execute([
                $request['supplier_id'],
                $request['company_id'],
                $request['product_id'],
                $request['quantity_requested'],
                $request['supply_price']
            ]);
        }

        // Update product quantity and price
        $updateProduct = $pdo->prepare("UPDATE products SET stock = ?, price = ? WHERE product_id = ?");
        $updateProduct->execute([$newQty, $newPrice, $request['product_id']]);

        // Mark request as approved
        $approve = $pdo->prepare("UPDATE supply_requests SET status = 'Approved' WHERE request_id = ?");
        $approve->execute([$request_id]);

        // Log history
        $logStmt = $pdo->prepare("
            INSERT INTO supply_request_history 
            (company_id, product_id, supplier_id, admin_id, old_quantity, new_quantity, old_price, new_price, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'approved')
        ");
        $logStmt->execute([
            $request['company_id'],
            $request['product_id'],
            $request['supplier_id'],
            $admin_id,
            $oldQty,
            $newQty,
            $oldPrice,
            $newPrice
        ]);
    }

    header("Location: supplier_requests.php");
    exit();
}

// Reject supply request
if (isset($_GET['reject'])) {
    $request_id = $_GET['reject'];

    $stmt = $pdo->prepare("SELECT * FROM supply_requests WHERE request_id = ?");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch();

    if ($request && $request['company_id'] == $company_id) {
        $reject = $pdo->prepare("UPDATE supply_requests SET status = 'Rejected' WHERE request_id = ?");
        $reject->execute([$request_id]);

        // Log rejection
        $logStmt = $pdo->prepare("
            INSERT INTO supply_request_history 
            (company_id, product_id, supplier_id, admin_id, status, rejection_reason)
            VALUES (?, ?, ?, ?, 'rejected', ?)
        ");
        $logStmt->execute([
            $request['company_id'],
            $request['product_id'],
            $request['supplier_id'],
            $admin_id,
            "Rejected by admin" // Optional: you can replace with user input
        ]);
    }

    header("Location: supplier_requests.php");
    exit();
}

// Fetch pending requests
try {
    $stmt = $pdo->prepare("
        SELECT r.*, p.name AS product_name, s.name AS supplier_name
        FROM supply_requests r
        LEFT JOIN products p ON r.product_id = p.product_id
        LEFT JOIN suppliers s ON r.supplier_id = s.user_id
        WHERE r.status = 'Pending' AND r.company_id = :company_id
    ");
    $stmt->execute(['company_id' => $company_id]);
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="container mt-5">
    <h2>Pending Supplier Requests</h2>
    <a href="product_management.php" class="btn btn-outline-secondary mb-3">&larr; Back</a>

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
                        <td><?= (int)$entry['quantity_requested'] ?></td>
                        <td>$<?= number_format($entry['supply_price'], 2) ?></td>
                        <td>
                            <button class="btn btn-success btn-sm action-btn" data-id="<?= $entry['request_id'] ?>" data-action="approve">Approve</button>
                            <button class="btn btn-danger btn-sm action-btn" data-id="<?= $entry['request_id'] ?>" data-action="reject">Reject</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="text-center">No pending requests for your company.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".action-btn").forEach(function (button) {
            button.addEventListener("click", function () {
                const requestId = this.getAttribute("data-id");
                const action = this.getAttribute("data-action");
                const actionText = action === "approve" ? "Approve" : "Reject";

                Swal.fire({
                    title: `Are you sure you want to ${actionText.toLowerCase()} this request?`,
                    icon: action === "approve" ? "success" : "warning",
                    showCancelButton: true,
                    confirmButtonColor: action === "approve" ? "#28a745" : "#d33",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: `Yes, ${actionText.toLowerCase()} it!`
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `?${action}=${requestId}`;
                    }
                });
            });
        });
    });
    </script>
</body>
</html>
