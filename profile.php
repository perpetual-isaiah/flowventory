<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$updateSuccess = false;
$errorMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['first_name'];
    $lastName  = $_POST['last_name'];
    $email     = $_POST['email'];
    $password  = $_POST['password'];

    try {
        if (!empty($password)) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET first_name=?, last_name=?, email=?, password_hash=? WHERE user_id=?");
            $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET first_name=?, last_name=?, email=? WHERE user_id=?");
            $stmt->execute([$firstName, $lastName, $email, $user_id]);
        }
        $updateSuccess = true;
    } catch (PDOException $e) {
        $errorMessage = "Error updating profile: " . $e->getMessage();
    }
}

$stmt = $pdo->prepare("SELECT first_name, last_name, email FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    #strengthMessage {
      font-weight: bold;
    }
    .weak { color: red; }
    .medium { color: orange; }
    .strong { color: green; }
  </style>
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h2>My Profile</h2>
        </div>
        <div class="card-body">
            <form method="post" class="mt-4" onsubmit="return confirmUpdate();">
                <div class="mb-3">
                    <label class="form-label">First Name:</label>
                    <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Last Name:</label>
                    <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email:</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">New Password <small>(leave blank to keep current)</small>:</label>
                    <input type="password" id="password" name="password" class="form-control" onkeyup="checkPasswordStrength(this.value)">
                    <div id="strengthMessage" class="mt-1"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Confirm New Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control">
                    <div id="confirmMessage" class="text-danger mt-1"></div>
                </div>

                <button type="submit" class="btn btn-success">Update Profile</button>
                <a href="user_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>

                <?php if ($updateSuccess): ?>
                    <div class="alert alert-success mt-3">Profile updated successfully! Redirecting...</div>
                    <script>
                        setTimeout(() => {
                            window.location.href = 'user_dashboard.php';
                        }, 2000); // 2-second delay
                    </script>
                <?php endif; ?>

            </form>
        </div>
    </div>
</div>

<script>
function checkPasswordStrength(password) {
    const strengthMessage = document.getElementById('strengthMessage');
    const confirmMessage = document.getElementById('confirmMessage');
    const confirmPassword = document.getElementById('confirm_password').value;

    let strength = "Weak";
    let color = "text-danger";

    if (password.length >= 8 && /[A-Z]/.test(password) &&
        /[a-z]/.test(password) && /[0-9]/.test(password) &&
        /[\W]/.test(password)) {
        strength = "Strong";
        color = "text-success";
    } else if (password.length >= 6) {
        strength = "Moderate";
        color = "text-warning";
    }

    strengthMessage.className = `mt-1 ${color}`;
    strengthMessage.textContent = `Password Strength: ${strength}`;

    // Check if passwords match (live feedback)
    if (confirmPassword && password !== confirmPassword) {
        confirmMessage.textContent = "Passwords do not match!";
    } else {
        confirmMessage.textContent = "";
    }
}

// Confirm match check when typing confirm password
document.getElementById('confirm_password').addEventListener('keyup', function () {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    const confirmMessage = document.getElementById('confirmMessage');

    if (confirmPassword !== password) {
        confirmMessage.textContent = "Passwords do not match!";
    } else {
        confirmMessage.textContent = "";
    }
});

function confirmUpdate() {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm_password').value;

    if (password !== confirm) {
        alert("Passwords do not match!");
        return false;
    }

    if (password && !confirm) {
        alert("Please confirm your new password.");
        return false;
    }

    return confirm("Are you sure you want to update your profile?");
}
</script>

</body>
</html>
