<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$role_id = $_SESSION['role_id'];

// Only allow suppliers (role_id = 3)
if ($role_id != 3) {
    die("Access denied. This page is for suppliers only.");
}

// Fetch all products linked to this supplier with company info and supply details
$stmt = $pdo->prepare("
    SELECT p.name, p.description, p.stock, p.price AS product_price, p.image, 
           c.company_name,
           sp.supply_price, sp.quantity_supplied
    FROM supplier_products sp
    INNER JOIN products p ON sp.product_id = p.product_id
    INNER JOIN companies c ON sp.company_id = c.company_id
    WHERE sp.supplier_id = ?
    ORDER BY c.company_name, p.name
");
$stmt->execute([$user_id]);
$products = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Supplier Inventory</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet"
  />
  <style>
    body {
      font-family: Arial, sans-serif;
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

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th,
    td {
      text-align: left;
      padding: 12px 15px;
      border-bottom: 1px solidrgb(18, 20, 22);
      vertical-align: middle;
    }

    th {
      background-color:rgb(6, 10, 15);
      color: black;
      font-weight: 600;
      font-size: 14px;
      text-transform: uppercase;
    }

    img {
      max-width: 50px;
      max-height: 50px;
      object-fit: contain;
      border-radius: 5px;
    }

    h2 {
      color: #343a40;
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
      <div class="card-header">Products You Supply</div>
      <div class="card-body">
        <?php if (count($products) > 0): ?>
          <table class="table table-striped align-middle">
            <thead>
              <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Description</th>
                <th>Stock</th>
                <th>Product Price (₱)</th>
                <th>Supply Price (₱)</th>
                <th>Quantity Supplied</th>
                <th>Company</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($products as $product): ?>
                <tr>
                  <td>
                    <?php if (!empty($product['image'])): ?>
                      <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image" />
                    <?php else: ?>
                      <span>No image</span>
                    <?php endif; ?>
                  </td>
                  <td><?php echo htmlspecialchars($product['name']); ?></td>
                  <td><?php echo htmlspecialchars($product['description']); ?></td>
                  <td><?php echo htmlspecialchars($product['stock']); ?></td>
                  <td><?php echo number_format($product['product_price'], 2); ?></td>
                  <td><?php echo number_format($product['supply_price'], 2); ?></td>
                  <td><?php echo htmlspecialchars($product['quantity_supplied']); ?></td>
                  <td><?php echo htmlspecialchars($product['company_name']); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p>No products found.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>
