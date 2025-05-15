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
    $email = trim($_POST['email']);  // Trim email for extra spaces

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format. Please enter a valid email address.");
    }

    // Validate email domain (check if domain has valid MX records)
    list($user, $domain) = explode('@', $email);
    if (!checkdnsrr($domain, 'MX')) {
        die("Invalid email domain. Please use a valid domain like @example.com.");
    }

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
        if ($role_id == 3) { // Supplier role_id
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            $city = $_POST['city'];

            $stmt = $pdo->prepare("INSERT INTO suppliers (user_id, name, email, phone, address, city)
                                   VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, "$first_name $last_name", $email, $phone, $address, $city]);
        }

        $_SESSION['temp_password'] = $temp_password;
        $_SESSION['new_user_email'] = $email;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
        
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
        <form method="POST" action="" onsubmit="return validateForm()">
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
                <input type="tel" name="phone" id="phone" pattern="\d+" title="Only numbers are allowed" required><br>
                <label>Address:</label>
                <input type="text" name="address" id="address" required><br>
                <label>City:</label>
                <input type="text" name="city" id="city" required><br>
            </div>

            <button type="submit" class="submit-btn">Add User</button>
        </form>

        <?php if (isset($_SESSION['temp_password'])): ?>
        <div id="toast" class="toast">
            <p>User <strong><?= $_SESSION['new_user_email'] ?></strong> added successfully.<br>
            Temporary Password: <strong><?= $_SESSION['temp_password'] ?></strong></p>
            <button onclick="document.getElementById('toast').style.display='none'">Close</button>
        </div>
        <?php
        unset($_SESSION['temp_password']);
        unset($_SESSION['new_user_email']);
        endif;
        ?>

        <a href="admin_dashboard.php" class="home-btn">Back to Home</a>

    </div>

    <script>
        // Function to show/hide Supplier fields
        function toggleSupplierFields() {
            const role = document.getElementById("role").value;
            const supplierFields = document.getElementById("supplierFields");

            // Show or hide fields based on role selection
            if (role == "3") {
                supplierFields.style.display = "block";
                document.getElementById('phone').removeAttribute('disabled');
                document.getElementById('address').removeAttribute('disabled');
                document.getElementById('city').removeAttribute('disabled');
            } else {
                supplierFields.style.display = "none";
                document.getElementById('phone').setAttribute('disabled', 'true');
                document.getElementById('address').setAttribute('disabled', 'true');
                document.getElementById('city').setAttribute('disabled', 'true');
            }
        }

        // Validate phone number and ensure it's numeric
        function validatePhoneInput() {
            const phoneInput = document.getElementById('phone');
            phoneInput.value = phoneInput.value.replace(/[^0-9]/g, ''); // Remove non-numeric characters
        }

        // Validate the form on submission
        function validateForm() {
            const role = document.getElementById("role").value;

            // If the user is a Supplier, validate the phone number
            if (role == "3") {  // Supplier
                const phone = document.getElementById('phone').value;
                if (phone && !/^\d+$/.test(phone)) {
                    alert("Phone number must contain only numbers.");
                    return false;
                }
            }
            return true; // Allow form submission if validation passes
        }
    </script>
</body>
</html>
