<?php
session_start();
require_once 'config.php';

// Ensure user is logged in and is a Supplier (role_id = 3)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$company_id = $_SESSION['company_id'];

// Fetch user info
$stmt = $pdo->prepare("SELECT first_name, last_name, email FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch product count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE company_id = ?");
$stmt->execute([$company_id]);
$product_count = $stmt->fetchColumn();

// Fetch products
$stmt = $pdo->prepare("SELECT product_id, name FROM products WHERE company_id = ?");
$stmt->execute([$company_id]);
$products = $stmt->fetchAll();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? null;
    $quantity = $_POST['quantity'] ?? null;
    $supply_price = $_POST['supply_price'] ?? null;

    if ($product_id && $quantity && $supply_price && is_numeric($quantity) && is_numeric($supply_price)) {
        // Step 1: Find the Admin for the supplier's company
        $stmtAdmin = $pdo->prepare("SELECT user_id FROM users WHERE company_id = ? AND role_id = 1 LIMIT 1");
        $stmtAdmin->execute([$company_id]);
        $admin = $stmtAdmin->fetch();

        if ($admin) {
            $admin_id = $admin['user_id'];

            // Step 2: Insert supply request
            $stmt = $pdo->prepare("
                INSERT INTO supply_requests 
                (supplier_id, company_id, product_id, quantity_requested, supply_price, admin_id) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $success = $stmt->execute([
                $user_id,
                $company_id,
                $product_id,
                $quantity,
                $supply_price,
                $admin_id
            ]);

            echo "<script>alert('" . ($success ? "Supply request submitted for approval." : "Submission failed.") . "');</script>";
        } else {
            echo "<script>alert('No Admin found for your company.');</script>";
        }
    } else {
        echo "<script>alert('All fields are required and must be valid numbers.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supplier Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .btn {
            border-radius: 5px;
            padding: 10px 20px;
            margin-top: 10px;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .btn:hover {
            opacity: 0.8;
        }
        h2, h4 {
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
    <!-- User Info Card -->
    <div class="card mb-4">
        <div class="card-header">
            Welcome, <?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?>!
        </div>
        <div class="card-body">
            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
        </div>
    </div>

    <!-- Product Count Card -->
    <div class="card">
        <div class="card-header">
            Inventory Overview
        </div>
        <div class="card-body">
            <p>Total Products in Your Company: <strong><?php echo $product_count; ?></strong></p>
            <a href="view_inventory.php" class="btn btn-primary">View Inventory</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>

    <!-- Add Product Supply Card -->
    <div class="card mt-4">
        <div class="card-header">
            Add Product Supply
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="product_id" class="form-label">Select Product</label>
                    <select name="product_id" id="product_id" class="form-select" required>
                        <option value="">-- Choose Product --</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo $product['product_id']; ?>">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="quantity" class="form-label">Quantity Supplied</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                </div>

                <div class="mb-3">
                    <label for="supply_price" class="form-label">Supply Price (per unit)</label>
                    <input type="number" step="0.01" class="form-control" id="supply_price" name="supply_price" required>
                </div>

                <button type="submit" class="btn btn-primary">Submit for Approval</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>
