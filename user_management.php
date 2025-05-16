<?php
session_start();
require_once 'config.php';

// Ensure the user is an Admin
if ($_SESSION['role_id'] != 1) {
    header("Location: index.php"); // Redirect non-admin users
    exit();
}

$company_id = $_SESSION['company_id'];

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
  <title>User Management</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
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
    .btn {
      margin-right: 5px;
    }
    table {
      border-collapse: collapse;
      width: 100%;
    }
    th, td {
      padding: 10px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }
    th {
      background-color: #f1f1f1;
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
    <h1>User Management</h1>

    <a href="add_user.php" class="btn btn-primary">Add New User</a>

    <table>
      <thead>
        <tr>
          <th>User ID</th>
          <th>Email</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $stmt = $pdo->prepare("SELECT * FROM users WHERE company_id = ?");
        $stmt->execute([$company_id]);
        while ($user = $stmt->fetch()) {
            echo "<tr>
                    <td>{$user['user_id']}</td>
                    <td>{$user['email']}</td>
                    <td>" . getRoleName($user['role_id']) . "</td>
                    <td>
                        <a href='edit_user.php?id={$user['user_id']}' class='btn btn-warning btn-sm'>Edit</a>
                        <a href='delete_user.php?id={$user['user_id']}' onclick=\"return confirm('Are you sure you want to delete this user?')\" class='btn btn-danger btn-sm'>Delete</a>
                    </td>
                  </tr>";
        }
        ?>
      </tbody>
    </table>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
