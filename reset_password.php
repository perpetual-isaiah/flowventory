<?php
if (!isset($_GET['token'])) {
    die("Invalid request.");
}
$token = $_GET['token'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
</head>
<body>
  <h2>Reset Password</h2>
  <form action="update_password.php" method="POST">
    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
    <label>New Password:</label><br>
    <input type="password" name="new_password" required><br><br>
    <label>Confirm Password:</label><br>
    <input type="password" name="confirm_password" required><br><br>
    <input type="submit" value="Update Password">
  </form>
</body>
</html>
