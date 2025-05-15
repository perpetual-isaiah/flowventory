<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>Forgot Password</h2>
  <form action="send_reset.php" method="POST">
    <label for="email">Enter your email address:</label><br>
    <input type="email" name="email" required><br><br>
    <input type="submit" value="Send Reset Link">
  </form>
</body>
</html>
