<?php
require 'config.php'; // sets up $pdo

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];

    // Check if email exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // Insert token into password_resets table
        $stmt = $pdo->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
        $stmt->execute([$email, $token, $expires]);

        $resetLink = "http://localhost/Inventory-Management/reset_password.php?token=$token";

        echo "<h3>Password reset link (for preview/testing):</h3>";
        echo "<a href='$resetLink'>$resetLink</a>";
    } else {
        echo "<p>Email address not found.</p>";
    }
} else {
?>
    <!-- Password Reset Request Form -->
    <form action="" method="POST">
        <label for="email">Enter your email address:</label><br>
        <input type="email" name="email" required>
        <br><br>
        <button type="submit">Send Reset Link</button>
    </form>
<?php
}
?>
