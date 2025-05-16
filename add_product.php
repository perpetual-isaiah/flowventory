<?php
session_start();
require_once 'config.php';

// Only Admin and Admin 2 are allowed
if (!isset($_SESSION['role_id']) || !in_array($_SESSION['role_id'], [1, 4])) {
    header("Location: index.php");
    exit();
}

$company_id = $_SESSION['company_id'];
$message = "";

// Fetch categories
$stmt = $pdo->prepare("SELECT category_id, category_name FROM categories WHERE company_id IS NULL OR company_id = ?");
$stmt->execute([$company_id]);
$categories = $stmt->fetchAll();

if (empty($categories)) {
    echo "No categories found.";
    exit;
}

// Fetch suppliers for this company
$stmt = $pdo->prepare("
    SELECT s.user_id, s.name 
    FROM suppliers s
    WHERE s.user_id IN (
        SELECT user_id FROM users WHERE role_id = 3 AND company_id = ?
    )
");
$stmt->execute([$company_id]);
$supplierList = $stmt->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $barcode = trim($_POST['barcode']);
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $suppliers = $_POST['suppliers'] ?? [];

    if (empty($name) || empty($barcode) || empty($category_id) || empty($price) || empty($suppliers)) {
        $message = "Please fill in all required fields.";
    } else {
        // Handle image upload
        $image_path = "";
        if (!empty($_FILES["image"]["name"])) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $image_path = $target_dir . basename($_FILES["image"]["name"]);
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {
                $image_path = ""; // fallback to empty
                $message = "Image upload failed.";
            }
        }

        try {
            // Use transaction for safety
            $pdo->beginTransaction();

            // Insert into products
            $stmt = $pdo->prepare("INSERT INTO products (name, barcode, category_id, price, image, company_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $barcode, $category_id, $price, $image_path, $company_id]);
            $product_id = $pdo->lastInsertId();

            // Prepare statements once
            $insertSupplierProduct = $pdo->prepare("INSERT INTO supplier_products (supplier_id, product_id, company_id, supply_price, quantity_supplied, is_approved) VALUES (?, ?, ?, ?, ?, 0)");
            $checkSupplierCompany = $pdo->prepare("SELECT 1 FROM supplier_company WHERE supplier_id = ? AND company_id = ?");
            $insertSupplierCompany = $pdo->prepare("INSERT INTO supplier_company (supplier_id, company_id) VALUES (?, ?)");

            foreach ($suppliers as $supplier_id) {
                // Insert supplier_products
                $insertSupplierProduct->execute([$supplier_id, $product_id, $company_id, null, 0]);

                // Check if supplier-company link exists
                $checkSupplierCompany->execute([$supplier_id, $company_id]);
                if ($checkSupplierCompany->rowCount() === 0) {
                    $insertSupplierCompany->execute([$supplier_id, $company_id]);
                }
            }

            $pdo->commit();
            $message = "Product added successfully!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Product</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
  <div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold">Add New Product</h2>
      <a href="admin_dashboard.php" class="btn btn-outline-secondary">&larr; Back to Dashboard</a>
    </div>

    <?php if ($message): ?>
      <div class="alert alert-info alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    <?php endif; ?>

    <div class="card shadow rounded-4">
      <div class="card-body p-4">
        <form method="POST" enctype="multipart/form-data">
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="name" class="form-label">Product Name</label>
              <input type="text" name="name" class="form-control" required>
            </div>

            <div class="col-md-6">
              <label for="barcode" class="form-label">Barcode</label>
              <input type="text" name="barcode" class="form-control" required>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label for="category_id" class="form-label">Category</label>
              <select class="form-select" name="category_id" required>
                <?php foreach ($categories as $category): ?>
                  <option value="<?= $category['category_id']; ?>">
                    <?= htmlspecialchars($category['category_name']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6">
              <label for="price" class="form-label">Selling Price</label>
              <input type="number" name="price" class="form-control" step="0.01" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="image" class="form-label">Product Image</label>
            <input type="file" name="image" class="form-control">
          </div>

          <div class="mb-4">
            <label for="suppliers" class="form-label">Assign Suppliers</label>
            <select name="suppliers[]" class="form-select" multiple required>
              <?php foreach ($supplierList as $supplier): ?>
                <option value="<?= $supplier['user_id'] ?>">
                  <?= htmlspecialchars($supplier['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple suppliers.</small>
          </div>

          <button type="submit" class="btn btn-primary w-100">Add Product</button>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
