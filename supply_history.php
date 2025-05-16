<?php
session_start();
require_once 'config.php';

// Only allow Admin (role_id = 1)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

$company_id = $_SESSION['company_id'];

// Get search term from user input (if provided)
$searchTerm = $_GET['search'] ?? '';

// Fetch history data with search filter
try {
    $query = "
        SELECT h.*, p.name AS product_name, CONCAT(u.first_name, ' ', u.last_name) AS supplier_name
        FROM supply_request_history h
        LEFT JOIN products p ON h.product_id = p.product_id
        LEFT JOIN users u ON h.supplier_id = u.user_id
        WHERE h.company_id = ?
    ";

    if (!empty($searchTerm)) {
        $query .= " AND CONCAT(u.first_name, ' ', u.last_name) LIKE ?";
    }

    $query .= " ORDER BY h.request_time DESC";

    $stmt = $pdo->prepare($query);
    $params = [$company_id];
    if (!empty($searchTerm)) {
        $params[] = "%$searchTerm%";
    }
    
    $stmt->execute($params);
    $history = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supply Request History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f9fa;
            margin-left: 260px;
        }
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            height: 100%;
            width: 250px;
            background-color: #343a40;
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }
        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }
        .sidebar ul li {
            padding: 15px 20px;
        }
        .sidebar ul li a {
            color: #ffffff;
            text-decoration: none;
            display: block;
            font-size: 16px;
        }
        .sidebar ul li a:hover {
            background-color: #495057;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .container {
            padding: 30px;
        }
        .search-box {
            max-width: 300px;
        }
    </style>
</head>

<body>

<!-- Sidebar Navigation -->
<div class="sidebar">
    <ul>
        <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="aview_inventory.php"><i class="fas fa-cogs"></i> View Inventory</a></li>
        <li><a href="user_management.php"><i class="fas fa-users"></i> Manage Users</a></li>
        <li><a href="product_management.php"><i class="fas fa-cogs"></i> Manage Products</a></li>
        <li><a href="supply_history.php"><i class="fas fa-history"></i> Request History</a></li>
        <li><a href="transaction_reports.php"><i class="fas fa-chart-line"></i> Reports</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="container">
    <h2>Supply Request History</h2>
    <a href="product_management.php" class="btn btn-outline-secondary mb-3">&larr; Back</a>

    <!-- Search Input -->
    <input type="text" id="supplierSearch" class="form-control search-box mb-3" placeholder="Search Supplier...">

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
        <tr>
            <th>Date</th>
            <th>Supplier</th>
            <th>Product</th>
            <th>Old Qty</th>
            <th>New Qty</th>
            <th>Old Price</th>
            <th>New Price</th>
            <th>Status</th>
            <th>Rejection Reason</th>
        </tr>
        </thead>
        <tbody id="historyTable">
        <?php if ($history): ?>
            <?php foreach ($history as $row): ?>
                <tr>
                    <td><?= date('Y-m-d H:i', strtotime($row['request_time'])) ?></td>
                    <td class="supplier"><?= htmlspecialchars($row['supplier_name'] ?? 'Unknown') ?></td>
                    <td><?= htmlspecialchars($row['product_name'] ?? 'Unknown') ?></td>
                    <td><?= is_null($row['old_quantity']) ? '-' : (int)$row['old_quantity'] ?></td>
                    <td><?= is_null($row['new_quantity']) ? '-' : (int)$row['new_quantity'] ?></td>
                    <td><?= is_null($row['old_price']) ? '-' : '$' . number_format($row['old_price'], 2) ?></td>
                    <td><?= is_null($row['new_price']) ? '-' : '$' . number_format($row['new_price'], 2) ?></td>
                    <td>
                        <?php if ($row['status'] === 'approved'): ?>
                            <span class="badge bg-success">Approved</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Rejected</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['rejection_reason'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="9" class="text-center">No request history available.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    document.getElementById('supplierSearch').addEventListener('keyup', function () {
        let searchValue = this.value.toLowerCase();
        document.querySelectorAll('#historyTable tr').forEach(row => {
            let supplierName = row.querySelector('.supplier').textContent.toLowerCase();
            row.style.display = supplierName.includes(searchValue) ? '' : 'none';
        });
    });
</script>

</body>
</html>
