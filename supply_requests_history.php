<?php
session_start();
require_once 'config.php';

// Check if user is a logged-in Supplier
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$company_id = $_SESSION['company_id'];

// Fetch supplier requests
$stmt = $pdo->prepare("
    SELECT sr.*, p.name AS product_name 
    FROM supply_requests sr
    JOIN products p ON sr.product_id = p.product_id
    WHERE sr.supplier_id = ? AND sr.company_id = ?
    ORDER BY sr.request_date DESC
");
$stmt->execute([$user_id, $company_id]);
$requests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supply Request History</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100%;
            background-color: #343a40;
            color: white;
            padding-top: 20px;
        }
        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }
        .sidebar ul li a {
            color: white;
            text-decoration: none;
            padding: 12px 16px;
            display: block;
            transition: background-color 0.3s ease;
        }
        .sidebar ul li a:hover {
            background-color: #495057;
        }
        .container {
            margin-left: 270px;
            padding: 30px;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            font-size: 18px;
        }
        .badge {
            text-transform: capitalize;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <ul>
        <li><a href="supplier_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
        <li><a href="view_inventory.php"><i class="fas fa-box"></i> View Inventory</a></li>
        <li><a href="supply_requests_history.php"><i class="fas fa-history"></i> Request History</a></li>
        <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</div>

<div class="container">
    <div class="card">
        <div class="card-header">Your Supply Request History</div>
        <div class="card-body">

        <form method="POST" class="mb-4">
        <label for="company_id" class="form-label">Select Company</label>
        <select name="company_id" id="company_id" class="form-select" onchange="this.form.submit()">
            <?php foreach ($companies as $company): ?>
                <option value="<?= htmlspecialchars($company['company_id']) ?>"
                    <?= $company['company_id'] == $selected_company_id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($company['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>
    
            <?php if (count($requests) > 0): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Supply Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $row): ?>
                            <tr>
                                <td><?php echo date('Y-m-d H:i', strtotime($row['request_date'])); ?></td>
                                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['quantity_requested']); ?></td>
                                <td><?php echo htmlspecialchars(number_format($row['supply_price'], 2)); ?></td>
                                <td>
                                    <?php
                                    $status = $row['status'] ?? 'pending';
                                    $badge = match ($status) {
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge bg-<?php echo $badge; ?>">
                                        <?php echo ucfirst($status); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No supply requests found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
