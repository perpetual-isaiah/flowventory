<?php
require 'config.php';
session_start();

// Only Admins can view
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    die("Unauthorized access.");
}

$company_id = $_SESSION['company_id'];

// Filters
$filters = [];
$params = [':company_id' => $company_id];

if (!empty($_GET['cashier_id'])) {
    $filters[] = "s.user_id = :cashier_id";
    $params[':cashier_id'] = $_GET['cashier_id'];
}
if (!empty($_GET['category_id'])) {
    $filters[] = "p.category_id = :category_id";
    $params[':category_id'] = $_GET['category_id'];
}
if (!empty($_GET['from_date']) && !empty($_GET['to_date'])) {
    $filters[] = "s.sale_date BETWEEN :from_date AND :to_date";
    $params[':from_date'] = $_GET['from_date'];
    $params[':to_date'] = $_GET['to_date'];
}

$filterSql = count($filters) ? 'AND ' . implode(' AND ', $filters) : '';

$sql = "
    SELECT 
        s.sale_id,
        CONCAT(u.first_name, ' ', u.last_name) AS cashier_name,
        u.user_id,
        u.email,
        p.name AS product_name,
        c.category_name AS category_name,
        s.quantity,
        s.price,
        s.discount,
        s.total_amount,
        s.sale_date
    FROM sales s
    JOIN users u ON s.user_id = u.user_id
    JOIN products p ON s.product_id = p.product_id
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE s.company_id = :company_id
    $filterSql
    ORDER BY s.sale_date DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get filter options
$cashiers = $pdo->query("SELECT user_id, CONCAT(first_name, ' ', last_name) AS name FROM users WHERE role_id = 2 AND company_id = $company_id")->fetchAll();
$categories = $pdo->query("SELECT category_id, category_name FROM categories")->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; padding: 20px; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 25px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background: #343a40; color: white; }
        tr:nth-child(even) { background-color: #fafafa; }
        button {
            background-color: #007bff; border: none; color: white;
            padding: 10px 15px; margin-right: 10px; border-radius: 5px;
        }
        button:hover { background-color: #0056b3; cursor: pointer; }
        .sidebar {
            position: fixed; top: 0; left: 0;
            width: 220px; height: 100%; background-color: #343a40;
            
        }
        .sidebar ul { list-style: none; padding: 0; }
        .sidebar ul li { padding: 15px; }
        .sidebar ul li a {
            color: white; text-decoration: none; display: block;
        }
        .sidebar ul li a:hover {
            background: #495057; border-radius: 5px;
        }
        .main-content { margin-left: 240px; padding: 20px; }

        form.filter-form label {
            margin-right: 15px;
        }
        form.filter-form select, form.filter-form input[type="date"] {
            padding: 5px;
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
    <h1>Cashier Sales Report</h1>

    <form method="GET" class="filter-form" style="margin-bottom: 20px;">
        <label>Cashier:
            <select name="cashier_id">
                <option value="">All</option>
                <?php foreach ($cashiers as $c): ?>
                    <option value="<?= $c['user_id'] ?>" <?= ($_GET['cashier_id'] ?? '') == $c['user_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>Category:
            <select name="category_id">
                <option value="">All</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['category_id'] ?>" <?= ($_GET['category_id'] ?? '') == $cat['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['category_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>From:
            <input type="date" name="from_date" value="<?= $_GET['from_date'] ?? '' ?>">
        </label>
        <label>To:
            <input type="date" name="to_date" value="<?= $_GET['to_date'] ?? '' ?>">
        </label>

        <button type="submit">Filter</button>
    </form>

    <button onclick="exportToExcel()">Export to Excel</button>
    <button onclick="exportToPDF()">Export to PDF</button>

    <?php if ($sales): ?>
    <table>
        <thead>
            <tr>
                <th>Sale ID</th>
                <th>Cashier</th>
                <th>Email</th>
                <th>Product</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Discount</th>
                <th>Total</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sales as $s): ?>
                <tr>
                    <td><?= $s['sale_id'] ?></td>
                    <td><?= htmlspecialchars($s['cashier_name']) ?></td>
                    <td><?= htmlspecialchars($s['email']) ?></td>
                    <td><?= htmlspecialchars($s['product_name']) ?></td>
                    <td><?= htmlspecialchars($s['category_name']) ?></td>
                    <td><?= $s['quantity'] ?></td>
                    <td><?= '$' . number_format($s['price'], 2) ?></td>
                    <td><?= '$' . number_format($s['discount'], 2) ?></td>
                    <td><?= '$' . number_format($s['total_amount'], 2) ?></td>
                    <td><?= $s['sale_date'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <p>No sales data found for your filters.</p>
    <?php endif; ?>
</div>

<script>
function exportToExcel() {
    const table = document.querySelector("table");
    const wb = XLSX.utils.table_to_book(table, { sheet: "Sales Report" });
    XLSX.writeFile(wb, "sales_report.xlsx");
}

function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    doc.text("Sales Report", 14, 16);
    const table = document.querySelector("table");

    const headers = [...table.querySelectorAll("thead th")].map(th => th.innerText);
    const rows = [...table.querySelectorAll("tbody tr")].map(row =>
        [...row.querySelectorAll("td")].map(td => td.innerText)
    );

    doc.autoTable({
        startY: 20,
        head: [headers],
        body: rows,
        styles: { fontSize: 8 }
    });

    doc.save("sales_report.pdf");
}
</script>
</body>
</html>
