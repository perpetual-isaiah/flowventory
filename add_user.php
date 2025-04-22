<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

// Redirect if not logged in or not an Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role_id = $_POST['role_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];

    $company_id = $_SESSION['company_id'];
    $temp_password = bin2hex(random_bytes(4)); // 8-char temporary password
    $hashedPassword = password_hash($temp_password, PASSWORD_DEFAULT);

    try {
        // Insert into users table
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password_hash, role_id, company_id) 
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$first_name, $last_name, $email, $hashedPassword, $role_id, $company_id]);

        $user_id = $pdo->lastInsertId();

        // If Supplier, insert into suppliers table too
        if ($role_id == 3) { // Changed to Supplier role_id
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            $city = $_POST['city'];

            $stmt = $pdo->prepare("INSERT INTO suppliers (user_id, name, email, phone, address, city)
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, "$first_name $last_name", $email, $phone, $address, $city]);
        }

        echo "<p>User added successfully. Temporary password: <strong>$temp_password</strong></p>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Add New User</h2>
        <form method="POST" action="">
            <label>First Name:</label>
            <input type="text" name="first_name" required><br>

            <label>Last Name:</label>
            <input type="text" name="last_name" required><br>

            <label>Email:</label>
            <input type="email" name="email" required><br>

            <label>Role:</label>
            <select name="role_id" id="role" required onchange="toggleSupplierFields()">
                <option value="4">Admin 2</option>
                <option value="2">Seller</option>
                <option value="3">Supplier</option>
            </select><br>

            <div id="supplierFields" class="supplier-fields" style="display: none;">
                <h4>Supplier Info</h4>
                <label>Phone:</label>
                <input type="text" name="phone"><br>
                <label>Address:</label>
                <input type="text" name="address"><br>
                <label>City:</label>
                <input type="text" name="city"><br>
            </div>

            <button type="submit" class="submit-btn">Add User</button>
        </form>

        <a href="admin_dashboard.php" class="home-btn">Back to Home</a>
    </div>

    <script>
        function toggleSupplierFields() {
            const role = document.getElementById("role").value;
            document.getElementById("supplierFields").style.display = (role == "3") ? "block" : "none";
        }
    </script>
</body>
</html>
