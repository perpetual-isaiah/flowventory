<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) { // Admin only
    header("Location: index.php");
    exit();
}

if (isset($_GET['approve'])) {
    $entry_id = $_GET['approve'];

    // Fetch entry info
    $stmt = $pdo->prepare("SELECT * FROM supplier_product WHERE id = ?");
    $stmt->execute([$entry_id]);
    $entry = $stmt->fetch();

    if ($entry) {
        // Update product quantity
        $update = $pdo->prepare("UPDATE products SET quantity = quantity + ? WHERE product_id = ?");
        $update->execute([$entry['quantity_supplied'], $entry['product_id']]);

        // Mark as approved
        $approve = $pdo->prepare("UPDATE supplier_product SET is_approved = 1 WHERE id = ?");
        $approve->execute([$entry_id]);
    }
    header("Location: admin_approve_supplies.php");
    exit();
}

if (isset($_GET['reject'])) {
    $entry_id = $_GET['reject'];

    // Mark as rejected
    $reject = $pdo->prepare("UPDATE supplier_product SET is_approved = 0 WHERE id = ?");
    $reject->execute([$entry_id]);
    header("Location: admin_approve_supplies.php");
    exit();
}

$stmt = $pdo->query("
    SELECT e.*, p.product_name, s.name AS supplier_name 
    FROM supplier_product e
    JOIN products p ON p.product_id = e.product_id
    JOIN suppliers s ON s.id = e.supplier_id
    WHERE e.is_approved = 0
");
$entries = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve Supplies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Pending Supplier Entries</h2>
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
            <?php foreach ($entries as $entry): ?>
            <tr>
                <td><?= htmlspecialchars($entry['supplier_name']) ?></td>
                <td><?= htmlspecialchars($entry['product_name']) ?></td>
                <td><?= $entry['quantity_supplied'] ?></td>
                <td>$<?= $entry['supply_price'] ?></td>
                <td>
                    <a href="?approve=<?= $entry['id'] ?>" class="btn btn-success btn-sm">Approve</a>
                    <a href="?reject=<?= $entry['id'] ?>" class="btn btn-danger btn-sm">Reject</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
