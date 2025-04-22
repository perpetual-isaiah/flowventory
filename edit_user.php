<?php
session_start();
require_once 'config.php';

// Ensure the user is an Admin
if ($_SESSION['role_id'] != 1) {
    header("Location: index.php"); // Redirect non-admin users
    exit();
}

$user_id = $_GET['id'];

// Fetch the user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found.";
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $role_id = $_POST['role_id'];

    // Update user in the database
    $stmt = $pdo->prepare("UPDATE users SET email = ?, role_id = ? WHERE user_id = ?");
    $stmt->execute([$email, $role_id, $user_id]);

    header("Location: admin_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit User</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
  <div class="container">
    <h1>Edit User</h1>

    <form method="POST">
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
      </div>
      <div class="mb-3">
        <label for="role_id" class="form-label">Role</label>
        <select class="form-control" id="role_id" name="role_id" required>
          <option value="1" <?php echo $user['role_id'] == 1 ? 'selected' : ''; ?>>Admin</option>
          <option value="2" <?php echo $user['role_id'] == 2 ? 'selected' : ''; ?>>Seller</option>
          <option value="3" <?php echo $user['role_id'] == 3 ? 'selected' : ''; ?>>Supplier</option>
        </select>
      </div>

      <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
  </div>
</body>
</html>
