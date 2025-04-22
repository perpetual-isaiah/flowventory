<?php
session_start();
require_once 'config.php';

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$company_id = $_SESSION['company_id'];

// Get limit and offset from the URL or set default values
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Default limit is 10
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0; // Default offset is 0

// Fetch user info
$stmt = $pdo->prepare("SELECT first_name, last_name, email FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch product count for this company
$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE company_id = ?");
$stmt->execute([$company_id]);
$product_count = $stmt->fetchColumn();

// Fetch products with pagination using named parameters for both limit and offset
$stmt = $pdo->prepare("SELECT * FROM products WHERE company_id = :company_id LIMIT :limit OFFSET :offset");

// Bind parameters using bindParam
$stmt->bindParam(':company_id', $company_id, PDO::PARAM_INT);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);

// Execute the query
$stmt->execute();

// Fetch all products
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Dashboard - Inventory Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f6f9;
      margin: 0;
      padding: 0;
    }

    .sidebar {
      height: 100vh;
      width: 250px;
      position: fixed;
      top: 0;
      left: 0;
      background-color: #343a40;
      padding-top: 20px;
      padding-right: 20px;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
    }

    .sidebar ul li {
      padding: 15px 0;
    }

    .sidebar ul li a {
      color: #ffffff;
      text-decoration: none;
      font-size: 18px;
      padding-left: 20px;
      display: block;
    }

    .sidebar ul li a:hover {
      background-color: #007bff;
      border-radius: 5px;
    }

    .container {
      margin-left: 270px;
      padding-top: 20px;
    }

    .card {
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }

    .card-header {
      background-color: #007bff;
      color: #fff;
      font-size: 1.25rem;
      padding: 15px;
      border-radius: 10px 10px 0 0;
    }

    .card-body {
      padding: 20px;
      background-color: #fff;
    }

    .table th, .table td {
      vertical-align: middle;
    }

    .pagination {
      justify-content: center;
    }

    .btn-custom {
      background-color: #28a745;
      color: white;
      border-radius: 5px;
      padding: 10px 20px;
      font-size: 16px;
    }

    .btn-custom:hover {
      background-color: #218838;
    }

    .btn-secondary-custom {
      background-color: #6c757d;
      color: white;
      border-radius: 5px;
      padding: 10px 20px;
      font-size: 16px;
    }

    .btn-secondary-custom:hover {
      background-color: #5a6268;
    }

    .pagination .page-item .page-link {
      border-radius: 5px;
      padding: 10px 20px;
      margin: 0 5px;
    }

    .pagination .page-item.active .page-link {
      background-color: #007bff;
      color: white;
    }

    .pagination .page-item .page-link:hover {
      background-color: #007bff;
      color: white;
    }
  </style>
</head>
<body>

<div class="sidebar">
  <ul>
    <li><a href="user_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
    <li><a href="profile.php"><i class="fas fa-user"></i> My Profile</a></li>
    <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
  </ul>
</div>

<div class="container">
  <div class="card">
    <div class="card-header">
      <h2>Welcome, <?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?>!</h2>
    </div>
    <div class="card-body">
      <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
      <hr>

      <h4>Inventory Overview</h4>
      <p>Total Products in Your Company: <strong><?php echo $product_count; ?></strong></p>

      <div class="table-responsive">
        <h5>Products</h5>
        <table class="table table-striped">
          <thead>
            <tr>
              <th>Product ID</th>
              <th>Product Name</th>
              <th>Price</th>
              <th>Stock</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $product): ?>
              <tr>
                <td><?php echo htmlspecialchars($product['product_id']); ?></td>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo htmlspecialchars($product['price']); ?></td>
                <td><?php echo htmlspecialchars($product['stock']); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="pagination">
        <a href="view_inventory.php?limit=<?php echo $limit; ?>&offset=<?php echo max(0, $offset - $limit); ?>" class="btn-secondary-custom <?php echo $offset <= 0 ? 'disabled' : ''; ?>">Previous</a>
        <a href="view_inventory.php?limit=<?php echo $limit; ?>&offset=<?php echo min($product_count - 1, $offset + $limit); ?>" class="btn-secondary-custom <?php echo $offset + $limit >= $product_count ? 'disabled' : ''; ?>">Next</a>
      </div>
    </div>
  </div>
  
  <a href="logout.php" class="btn-custom mt-3">Logout</a>
</div>

</body>
</html>
