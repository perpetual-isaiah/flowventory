<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 5) {
    header('Location: login.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users");
$stmt->execute();
$users = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Management</title>
    <link rel="stylesheet" href="sastyles.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <h2>Super Admin</h2>
        <a href="super_admin_dashboard.php">Dashboard</a>
        <a href="super_admin_approval.php">Pending Approvals</a>
        <a href="sa_user_management.php">Users</a>
        <a href="settings.php">Settings</a>
        <a href="logout.php">Logout</a>
    </div>

    <div class="content">
    <h1>👥 Registered Users</h1>
        <?php if (count($users) > 0): ?>
            <table>
                <tr>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($users as $user): ?>
                    <tr id="user-row-<?= $user['user_id']; ?>">
                        <td><?= htmlspecialchars($user['email']); ?></td>
                        <td><?= htmlspecialchars($user['role_id']); ?></td>
                        <td>
                            <button class="delete-btn" onclick="deleteUser(<?= $user['user_id']; ?>)">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No registered users.</p>
        <?php endif; ?>
    </div>

    <script>
function deleteUser(userId) {
    if (confirm("Are you sure you want to delete this user?")) {
        fetch('sa_delete_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `user_id=${userId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`user-row-${userId}`).remove();
                showToast(data.message);
            } else {
                showToast(data.message || 'An error occurred.');
            }
        })
        .catch(() => {
            showToast('Failed to delete the user.');
        });
    }
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%);
        background: #333; color: #fff; padding: 10px 20px; border-radius: 8px;
        z-index: 9999; font-size: 14px;
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
}
</script>

</body>
</html>
