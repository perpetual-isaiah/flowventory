<?php
session_start();
require_once 'config.php'; // contains $pdo for DB connection

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    header("Location: index.php");
    exit();
}

function getRoleName($role_id) {
    switch ($role_id) {
        case 1: return "Admin";
        case 2: return "Seller";
        case 3: return "Supplier";
        default: return "Unknown";
    }
}

$company_id = $_SESSION['company_id'] ?? null;

$message = '';

// Handle form submission for assigning supplier to company
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_supplier'])) {
    $supplier_id = $_POST['supplier_id'] ?? null;
    $assign_company_id = $_POST['assign_company_id'] ?? null;

    if ($supplier_id && $assign_company_id) {
        // Check if link exists
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM supplier_company WHERE supplier_id = ? AND company_id = ?");
        $checkStmt->execute([$supplier_id, $assign_company_id]);
        $exists = $checkStmt->fetchColumn();

        if ($exists) {
            $message = "This supplier is already assigned to the selected company.";
        } else {
            $insertStmt = $pdo->prepare("INSERT INTO supplier_company (supplier_id, company_id) VALUES (?, ?)");
            if ($insertStmt->execute([$supplier_id, $assign_company_id])) {
                $message = "Supplier successfully assigned to the company.";
            } else {
                $message = "Failed to assign supplier to company.";
            }
        }
    } else {
        $message = "Please select both supplier and company.";
    }
}

// Fetch suppliers for dropdown (only suppliers)
$suppliersStmt = $pdo->query("SELECT user_id, email FROM users WHERE role_id = 3 ORDER BY email");

// Fetch companies for dropdown
$companiesStmt = $pdo->query("SELECT company_id, company_name FROM companies ORDER BY company_name");

// Fetch users to show in the table
$sql = "
  SELECT u.user_id, u.email, u.role_id,
    GROUP_CONCAT(c.company_name SEPARATOR ', ') AS company_names
  FROM users u
  LEFT JOIN supplier_company sc ON u.user_id = sc.supplier_id
  LEFT JOIN companies c ON sc.company_id = c.company_id
  WHERE u.company_id = ? OR sc.company_id = ?
  GROUP BY u.user_id
  ORDER BY u.user_id ASC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$company_id, $company_id]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>User Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
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
      color: #fff;
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
    table {
      border-collapse: collapse;
      width: 100%;
      background: white;
      box-shadow: 0 0 10px rgb(0 0 0 / 0.1);
    }
    th, td {
      padding: 10px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }
    th {
      background-color: #f1f1f1;
    }
  </style>
</head>
<body>

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

  <div class="main-content">
    <h1>User Management</h1>
    <a href="add_user.php" class="btn btn-primary mb-3">Add New User</a>

    <?php if ($message): ?>
      <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <h4>Assign Existing Supplier to Another Company</h4>
    <form method="POST" class="row g-3 mb-4">
      <input type="hidden" name="assign_supplier" value="1" />
      <div class="col-md-5">
        <label for="supplier_id" class="form-label">Select Supplier</label>
        <select class="form-select" id="supplier_id" name="supplier_id" required>
          <option value="" selected disabled>Choose supplier</option>
          <?php while ($supplier = $suppliersStmt->fetch()): ?>
            <option value="<?= $supplier['user_id'] ?>"><?= htmlspecialchars($supplier['email']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="col-md-5">
        <label for="assign_company_id" class="form-label">Select Company</label>
        <select class="form-select" id="assign_company_id" name="assign_company_id" required>
          <option value="" selected disabled>Choose company</option>
          <?php
            // Re-run query because we fetched suppliersStmt above (PDO forward-only cursor)
            $companiesStmt = $pdo->query("SELECT company_id, company_name FROM companies ORDER BY company_name");
            while ($company = $companiesStmt->fetch()):
          ?>
            <option value="<?= $company['company_id'] ?>"><?= htmlspecialchars($company['company_name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="col-md-2 align-self-end">
        <button type="submit" class="btn btn-success">Assign</button>
      </div>
    </form>

    <table>
      <thead>
        <tr>
          <th>User ID</th>
          <th>Email</th>
          <th>Role</th>
          <th>Companies (for Suppliers)</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php
        while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['user_id']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . getRoleName($user['role_id']) . "</td>";
            $companies = ($user['role_id'] == 3) ? htmlspecialchars($user['company_names']) : '-';
            echo "<td>$companies</td>";
            echo "<td>
                    <a href='edit_user.php?id=" . urlencode($user['user_id']) . "' class='btn btn-warning btn-sm me-1'>Edit</a>
                    <a href='delete_user.php?id=" . urlencode($user['user_id']) . "' onclick=\"return confirm('Are you sure you want to delete this user?')\" class='btn btn-danger btn-sm'>Delete</a>
                  </td>";
            echo "</tr>";
        }
      ?>
      </tbody>
    </table>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
