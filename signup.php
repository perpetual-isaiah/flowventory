<?php
require_once 'config.php';

function isAllowedEmailDomain($email) {
    $allowed_domains = ['yahoo.com', 'hotmail.com', 'gmail.com', 'bing.com', 'icloud.com', 'outlook.com'];
    $domain = strtolower(substr(strrchr($email, "@"), 1));
    return in_array($domain, $allowed_domains);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $firstName = htmlspecialchars(trim($_POST['first_name']));
    $lastName = htmlspecialchars(trim($_POST['last_name']));
    $companyName = htmlspecialchars(trim($_POST['company_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];

    // Validate email domain
    if (!isAllowedEmailDomain($email)) {
        ?>
        <script>
            alert("Signup failed: Only emails from yahoo, hotmail, gmail, bing, icloud, or outlook are allowed.");
            window.history.back();
        </script>
        <?php
        exit;
    }

    // Validate password strength
    if (
        strlen($password) < 8 ||
        !preg_match("/[A-Z]/", $password) ||
        !preg_match("/[0-9]/", $password) ||
        !preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $password)
    ) {
        ?>
        <script>
            alert("Password must be at least 8 characters, include an uppercase letter, a number, and a special character.");
            window.history.back();
        </script>
        <?php
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $role_id = 1; // Admin role for new company

    try {
        // Check if company already exists
        $stmt = $pdo->prepare("SELECT company_id FROM companies WHERE company_name = ?");
        $stmt->execute([$companyName]);
        $company = $stmt->fetch();

        if ($company) {
            $company_id = $company['company_id'];
        } else {
            // Insert new company with status = 0 (pending approval)
            $stmt = $pdo->prepare("INSERT INTO companies (company_name, status) VALUES (?, 0)");
            $stmt->execute([$companyName]);
            $company_id = $pdo->lastInsertId();
        }

        // Insert user
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password_hash, company_id, role_id)
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $company_id, $role_id]);
        ?>
        <script>
            alert("Signup successful! Your company is pending approval.");
            window.location.href = "index.php";
        </script>
        <?php
        exit;

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
