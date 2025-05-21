<?php
session_start();
require_once 'config.php';

// Allow only Admins (role_id = 1)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get the company ID for the logged-in admin
$stmt = $pdo->prepare("SELECT company_id FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$company_id = $stmt->fetchColumn();

// Handle update form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone_number'] ?? '';

    $update = $pdo->prepare("UPDATE companies SET address = ?, phone_number = ? WHERE company_id = ?");
    $update->execute([$address, $phone, $company_id]);

    $success = "Company information updated successfully!";
}

// Fetch company info
$stmt = $pdo->prepare("SELECT * FROM companies WHERE company_id = ?");
$stmt->execute([$company_id]);
$company = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Company Profile</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
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
  </style>
</head>
<body>

  <!-- Sidebar Navigation -->
  <div class="sidebar">
    <ul>
      <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li><a href="aview_inventory.php"><i class="fas fa-boxes"></i> View Inventory</a></li>
      <li><a href="user_management.php"><i class="fas fa-users"></i> Manage Users</a></li>
      <li><a href="product_management.php"><i class="fas fa-box"></i> Manage Products</a></li>
      <li><a href="supply_history.php"><i class="fas fa-history"></i> Request History</a></li>
      <li><a href="transaction_reports.php"><i class="fas fa-chart-line"></i> Reports</a></li>
      <li><a href="company_profile.php"><i class="fas fa-building"></i> Company Profile</a></li>
      <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </div>

  <!-- Main Content -->
  <div class="main-content">
    <h2 class="mb-4">Manage Company Profile</h2>

    <?php if (!empty($success)): ?>
      <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if ($company): ?>
      <form method="POST" class="card p-4 shadow-sm bg-white rounded" style="max-width: 600px;">
        <div class="mb-3">
          <label class="form-label">Company Name</label>
          <input type="text" class="form-control" value="<?php echo htmlspecialchars($company['company_name']); ?>" disabled>
        </div>

        <div class="mb-3">
          <label class="form-label">Address</label>
          <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($company['address'] ?? ''); ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Phone Number</label>
          <input type="text" name="phone_number" class="form-control" value="<?php echo htmlspecialchars($company['phone_number'] ?? ''); ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Status</label>
          <input type="text" class="form-control" value="<?php 
            switch ($company['status']) {
              case 1: echo 'Active'; break;
              case 0: echo 'Pending'; break;
              case -1: echo 'Suspended'; break;
              default: echo 'Unknown';
            }
          ?>" disabled>
        </div>

        <button type="submit" class="btn btn-primary">Update Info</button>
      </form>
    <?php else: ?>
      <div class="alert alert-warning">Company information not found.</div>
    <?php endif; ?>
  </div>

</body>
</html>
