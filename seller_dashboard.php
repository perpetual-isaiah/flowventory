<?php
session_start();
require_once 'config.php';

// Secure session check
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: index.php");
    exit();
}

$company_id = $_SESSION['company_id'] ?? 0;

// Fetch total sales for the day
$stmt = $pdo->prepare("SELECT SUM(total_amount) FROM sales WHERE company_id = ? AND DATE(sale_date) = CURDATE()");
$stmt->execute([$company_id]);
$total_sales = $stmt->fetchColumn() ?? 0;

// Fetch the latest transactions
$stmt = $pdo->prepare("SELECT * FROM sales WHERE company_id = ? ORDER BY sale_date DESC LIMIT 5");
$stmt->execute([$company_id]);
$recent_sales = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Seller Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: #f5f7fa;">
  <div class="container my-5">
    <div class="text-center mb-4">
      <h2>Welcome, Cashier</h2>
      <div class="d-flex justify-content-center flex-wrap gap-3">
        <a href="start_sale.php" class="btn btn-primary">🛒 Start New Sale</a>
        <a href="process_sale.php" class="btn btn-primary">➕ Add Items to Sale</a>
        <a href="sale_details.php" class="btn btn-primary">📄 Complete Sale</a>
        <a href="seller_change_password.php" class="btn btn-warning">🔒 Change Password</a>
        <a href="logout.php" class="btn btn-danger">🚪 Logout</a>
      </div>
    </div>

    <!-- Sales Summary -->
    <div class="row mb-4">
      <div class="col-md-4">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title">Total Sales Today</h5>
            <p class="card-text fs-5 fw-bold"><?php echo '$' . number_format($total_sales, 2); ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Search Section -->
    <div class="mb-4">
      <h4>Search for Products</h4>
      <form method="GET" action="process_sale.php">
        <div class="input-group">
          <input type="text" class="form-control" name="product_search" placeholder="Search by name or scan barcode" required>
          <button class="btn btn-outline-secondary" type="submit">Search</button>
        </div>
      </form>
    </div>

    <!-- Recent Transactions -->
    <div>
      <h4>Recent Transactions</h4>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Transaction ID</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Details</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($recent_sales) > 0): ?>
            <?php foreach ($recent_sales as $sale): ?>
              <tr>
                <td><?php echo $sale['sale_id']; ?></td>
                <td><?php echo '$' . number_format($sale['total_amount'], 2); ?></td>
                <td><?php echo $sale['sale_date']; ?></td>
                <td><a href="sale_details.php?id=<?php echo $sale['sale_id']; ?>" class="btn btn-sm btn-info">View</a></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="4" class="text-center">No transactions found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
