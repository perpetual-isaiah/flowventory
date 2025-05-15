<?php 
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

// Toast message logic
$toastMessage = "";
$toastType = "";

if (isset($_GET['approve'])) {
    $entry_id = $_GET['approve'];
    $stmt = $pdo->prepare("SELECT * FROM supplier_product WHERE id = ?");
    $stmt->execute([$entry_id]);
    $entry = $stmt->fetch();

    if ($entry) {
        $update = $pdo->prepare("UPDATE products SET quantity = quantity + ? WHERE product_id = ?");
        $update->execute([$entry['quantity_supplied'], $entry['product_id']]);

        $approve = $pdo->prepare("UPDATE supplier_product SET is_approved = 1 WHERE id = ?");
        $approve->execute([$entry_id]);

        $_SESSION['toast_message'] = "Supply entry approved successfully!";
        $_SESSION['toast_type'] = "success";
    }

    header("Location: admin_approve_supplies.php");
    exit();
}

if (isset($_GET['reject'])) {
    $entry_id = $_GET['reject'];

    $reject = $pdo->prepare("UPDATE supplier_product SET is_approved = 0 WHERE id = ?");
    $reject->execute([$entry_id]);

    $_SESSION['toast_message'] = "Supply entry rejected.";
    $_SESSION['toast_type'] = "danger";

    header("Location: admin_approve_supplies.php");
    exit();
}

// Show toast if set
if (isset($_SESSION['toast_message'])) {
    $toastMessage = $_SESSION['toast_message'];
    $toastType = $_SESSION['toast_type'];
    unset($_SESSION['toast_message'], $_SESSION['toast_type']);
}

// Get entries
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
    <meta charset="UTF-8">
    <title>Approve Supplies</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

    <h2 class="mb-4">Pending Supplier Entries</h2>

    <?php if ($toastMessage): ?>
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div id="liveToast" class="toast align-items-center text-white bg-<?= $toastType ?> border-0 show" role="alert">
            <div class="d-flex">
                <div class="toast-body">
                    <?= htmlspecialchars($toastMessage) ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Supplier</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Supply Price</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($entries): ?>
                    <?php foreach ($entries as $entry): ?>
                        <tr>
                            <td><?= htmlspecialchars($entry['supplier_name']) ?></td>
                            <td><?= htmlspecialchars($entry['product_name']) ?></td>
                            <td><?= $entry['quantity_supplied'] ?></td>
                            <td>$<?= number_format($entry['supply_price'], 2) ?></td>
                            <td>
                                <a href="?approve=<?= $entry['id'] ?>" 
                                   class="btn btn-success btn-sm"
                                   onclick="return confirm('Are you sure you want to approve this supply entry?');">
                                   Approve
                                </a>
                                <a href="?reject=<?= $entry['id'] ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Are you sure you want to reject this supply entry?');">
                                   Reject
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">No pending supply entries.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
