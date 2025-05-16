<?php
session_start();
require_once 'config.php';

// Ensure the user is an Admin
if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php"); // Redirect non-admin users
    exit();
}


$company_id = $_SESSION['company_id'];

// Fetch company-specific data
$stmt = $pdo->prepare("SELECT SUM(price) FROM sales WHERE company_id = ?");
$stmt->execute([$company_id]);
$total_sales = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE company_id = ?");
$stmt->execute([$company_id]);
$total_inventory = $stmt->fetchColumn();

function getRoleName($role_id) {
    switch ($role_id) {
        case 1: return "Admin";
        case 2: return "Seller";
        case 3: return "User";
        default: return "Unknown";
    }
}
?>

<!DOCTYPE html>

<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f8f9fa;
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
    .main-content {
      margin-left: 270px;
      padding: 30px;
    }
    h1 {
      color: #343a40;
      font-size: 28px;
      margin-bottom: 20px;
    }
    .overview {
      display: flex;
      gap: 20px;
      margin-bottom: 30px;
    }
    .overview .box {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      flex: 1;
      text-align: center;
      transition: transform 0.3s;
    }
    .overview .box:hover {
      transform: translateY(-10px);
      box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    }
    .box h2 {
      font-size: 20px;
      color: #343a40;
    }
    .box p {
      font-size: 24px;
      font-weight: bold;
      color: #28a745;
    }
    .box i {
      font-size: 30px;
      color: #28a745;
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

  <!-- Main Content Area -->

  <div class="main-content">
    <h1>Admin Dashboard</h1>

```
<div class="overview">
  <div class="box">
    <i class="fas fa-dollar-sign"></i>
    <h2>Total Sales</h2>
    <p><?php echo '$' . number_format($total_sales, 2); ?></p>
  </div>
  <div class="box">
    <i class="fas fa-boxes"></i>
    <h2>Total Inventory</h2>
    <p><?php echo $total_inventory . ' items'; ?></p>
  </div>
</div>
```

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
