<?php
session_start();
require_once 'config.php';

// Ensure the user is logged in and has admin/manager role
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 4])) 
    {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $product_name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $description = $_POST['description'];
    $barcode = uniqid(); // Auto-generate unique barcode

    // Validate price and stock
    if ($price <= 0) {
        echo "Price must be a positive number.";
        exit();
    }

    if ($stock < 0) {
        echo "Stock must be zero or a positive number.";
        exit();
    }

    // Check if product already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE name = ? AND company_id = ?");
    $stmt->execute([$product_name, $_SESSION['company_id']]);
    $existingProduct = $stmt->fetchColumn();

    if ($existingProduct > 0) {
        echo "A product with this name already exists.";
    } else {
        // Handle file upload
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $image = $_FILES['product_image'];
            $imageName = time() . '_' . basename($image['name']);
            $targetDirectory = 'uploads/';
            $targetFile = $targetDirectory . $imageName;
            
            // Check file type (optional)
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($image['type'], $allowedTypes)) {
                if (move_uploaded_file($image['tmp_name'], $targetFile)) {
                    // Insert product data including image path and barcode
                    $stmt = $pdo->prepare("INSERT INTO products (company_id, name, category_id, price, stock, description, product_image, barcode) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$_SESSION['company_id'], $product_name, $category_id, $price, $stock, $description, $targetFile, $barcode]);
                    header("Location: view_inventory.php?message=Product added successfully");
                } else {
                    // Handle upload failure
                    echo "Failed to upload image.";
                }
            } else {
                echo "Only JPG, PNG, and GIF images are allowed.";
            }
        } else {
            // Insert product data without an image but with barcode
            $stmt = $pdo->prepare("INSERT INTO products (company_id, name, category_id, price, stock, description, barcode) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['company_id'], $product_name, $category_id, $price, $stock, $description, $barcode]);
            header("Location: aview_inventory.php?message=Product added successfully");
            
        }
    }
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM categories");
$stmt->execute();
$categories = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { margin-top: 20px; }
        .form-group { margin-bottom: 1.5rem; }
    </style>
</head>
<body>

<body>
<div class="container mt-4">
  <h2>Add New Product</h2>
  
  <!-- Toast Notification -->
  <div class="toast-container position-fixed top-0 end-0 p-3" id="toast-container"></div>

  <form id="productForm" method="POST" action="add_product.php" enctype="multipart/form-data" onsubmit="return validateForm()">
    <div class="form-group">
      <label for="product_name">Product Name:</label>
      <input type="text" class="form-control" id="product_name" name="product_name" required>
    </div>

    <div class="form-group">
      <label for="category_id">Category:</label>
      <select class="form-control" id="category_id" name="category_id" required>
        <?php foreach ($categories as $category): ?>
          <option value="<?php echo $category['category_id']; ?>"><?php echo $category['category_name']; ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="form-group">
      <label for="price">Price:</label>
      <input type="number" class="form-control" id="price" name="price" required>
    </div>

    <div class="form-group">
      <label for="stock">Stock Quantity:</label>
      <input type="number" class="form-control" id="stock" name="stock" required>
    </div>

    <div class="form-group">
      <label for="description">Product Description:</label>
      <textarea class="form-control" id="description" name="description" rows="3"></textarea>
    </div>

    <div class="form-group">
      <label for="product_image">Product Image (Optional):</label>
      <input type="file" class="form-control" id="product_image" name="product_image" accept="image/*" onchange="previewImage(event)">
      <img id="preview" class="mt-2" style="max-width: 200px; display: none;" />
    </div>

    <button type="submit" class="btn btn-primary">Add Product</button>
  </form>
</div>

<!-- JS Scripts -->
<script>
  function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('preview');

    if (file) {
      preview.src = URL.createObjectURL(file);
      preview.style.display = 'block';
    } else {
      preview.src = '';
      preview.style.display = 'none';
    }
  }

  function showToast(message, isSuccess = true) {
    const toastContainer = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white ${isSuccess ? 'bg-success' : 'bg-danger'} border-0`;
    toast.role = 'alert';
    toast.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    `;
    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
  }

  function validateForm() {
    const price = parseFloat(document.getElementById("price").value);
    const stock = parseInt(document.getElementById("stock").value);

    if (price <= 0) {
      showToast("Price must be a positive number", false);
      return false;
    }

    if (stock < 0) {
      showToast("Stock cannot be negative", false);
      return false;
    }

    return true;
  }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
