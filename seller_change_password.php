<?php
session_start();
require_once 'config.php';

// Only sellers can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: index.php");
    exit();
}

$errors = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user && password_verify($current, $user['password_hash'])) {
        if ($new === $confirm) {
            if (strlen($new) >= 6 && preg_match('/[A-Z]/', $new) && preg_match('/[a-z]/', $new) &&
                preg_match('/[0-9]/', $new) && preg_match('/[\W]/', $new)) {

                $hashed = password_hash($new, PASSWORD_DEFAULT);
                $update = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
                $update->execute([$hashed, $user_id]);
                $success = "Password changed successfully.";
            } else {
                $errors[] = "Password must be at least 6 characters and include upper, lower, number, and symbol.";
            }
        } else {
            $errors[] = "New passwords do not match.";
        }
    } else {
        $errors[] = "Current password is incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .form-wrapper {
      max-width: 400px;
      margin: 60px auto;
      padding: 30px;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .password-toggle {
      cursor: pointer;
      position: absolute;
      right: 10px;
      top: 10px;
      z-index: 10;
    }
    .position-relative .form-control {
      padding-right: 40px;
    }
    #strengthText {
      font-weight: bold;
      margin-top: 5px;
    }
  </style>
</head>
<body>
  <div class="form-wrapper">
    <h4 class="mb-4 text-center">Change Password</h4>

    <?php if (!empty($success)): ?>
      <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $error) echo "<div>$error</div>"; ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3 position-relative">
        <label class="form-label">Current Password</label>
        <input type="password" name="current_password" class="form-control" id="currentPassword" required>
        <span class="password-toggle" onclick="togglePassword('currentPassword')">👁️</span>
      </div>
      <div class="mb-3 position-relative">
        <label class="form-label">New Password</label>
        <input type="password" name="new_password" class="form-control" id="newPassword" required oninput="checkStrength(this.value)">
        <span class="password-toggle" onclick="togglePassword('newPassword')">👁️</span>
        <div id="strengthText" class="text-muted small"></div>
      </div>
      <div class="mb-3 position-relative">
        <label class="form-label">Confirm New Password</label>
        <input type="password" name="confirm_password" class="form-control" id="confirmPassword" required>
        <span class="password-toggle" onclick="togglePassword('confirmPassword')">👁️</span>
      </div>
      <div class="d-grid">
        <button type="submit" class="btn btn-primary">Update Password</button>
        <a href="seller_dashboard.php" class="btn btn-outline-secondary mt-2">Back</a>
      </div>
    </form>
  </div>

  <script>
    function togglePassword(id) {
      const input = document.getElementById(id);
      input.type = input.type === 'password' ? 'text' : 'password';
    }

    function checkStrength(password) {
      let strengthText = document.getElementById("strengthText");
      let strength = 0;

      if (password.length >= 6) strength++;
      if (/[A-Z]/.test(password)) strength++;
      if (/[a-z]/.test(password)) strength++;
      if (/[0-9]/.test(password)) strength++;
      if (/[\W]/.test(password)) strength++;

      switch (strength) {
        case 0:
        case 1:
        case 2:
          strengthText.textContent = "Weak password";
          strengthText.style.color = "red";
          break;
        case 3:
        case 4:
          strengthText.textContent = "Medium strength";
          strengthText.style.color = "orange";
          break;
        case 5:
          strengthText.textContent = "Strong password";
          strengthText.style.color = "green";
          break;
      }
    }
  </script>
</body>
</html>
