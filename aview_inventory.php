<?php
session_start();
require_once 'config.php';

// Ensure the user is an Admin
if ($_SESSION['role_id'] != 1) {
    header("Location: index.php"); // Redirect non-admin users
    exit();
}

$company_id = $_SESSION['company_id'];

// Fetch products data for the specific company
$stmt = $pdo->prepare("SELECT p.*, c.category_name FROM products p JOIN categories c ON p.category_id = c.category_id WHERE p.company_id = ?");
$stmt->execute([$company_id]);
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Inventory - Admin</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f8f9fa;
    }
    .sidebar {
      position: fixed;
      top: 0; left: 0;
      height: 100%;
      width: 220px;
      background-color: #343a40;
      padding-top: 20px;
    }
    .sidebar ul {
      list-style-type: none;
      padding: 0;
    }
    .sidebar ul li {
      padding: 15px;
    }
    .sidebar ul li a {
      color: #ffffff;
      text-decoration: none;
      display: block;
    }
    .sidebar ul li a:hover {
      background-color: #495057;
      border-radius: 4px;
    }
    .main-content {
      margin-left: 240px;
      padding: 20px;
    }
    h1, h2 {
      color: #343a40;
    }
    .section {
      margin-bottom: 40px;
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
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
      <li><a href="transaction_reports.php"><i class="fas fa-chart-line"></i> Reports</a></li>
      <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </div>

  <!-- Main Content Area -->
  <div class="main-content">
    <h1>Inventory Management</h1>

    <div class="section">
      <h2>Product List</h2>
      <table class="table">
        <thead>
          <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Category</th>
            <th>Stock</th>
            <th>Price</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($products as $product) {
              echo "<tr>
                      <td>{$product['product_id']}</td>
                      <td>{$product['name']}</td>
                      <td>{$product['category_name']}</td>
                      <td>{$product['stock']}</td>
                      <td>\${$product['price']}</td>
                      <td>
                          <a href='edit_product.php?id={$product['product_id']}' class='btn btn-warning btn-sm'>Edit</a>
                          <a href='delete_product.php?id={$product['product_id']}' onclick=\"return confirm('Are you sure you want to delete this product?')\" class='btn btn-danger btn-sm'>Delete</a>
                      </td>
                    </tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
