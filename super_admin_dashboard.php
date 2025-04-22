<?php
session_start();
require_once 'config.php';

// Check if the user is logged in and has Super Admin role (role_id = 5)
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 5) {
    header('Location: login.php'); // Redirect to login page if not logged in or not Super Admin
    exit();
}

// Fetch pending companies for approval
$stmt = $pdo->prepare("SELECT * FROM companies WHERE status = 0");
$stmt->execute();
$pendingCompanies = $stmt->fetchAll();

// Fetch total number of registered users
$stmt2 = $pdo->prepare("SELECT COUNT(*) FROM users");
$stmt2->execute();
$totalUsers = $stmt2->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your styles here -->
</head>
<body>
    <div class="dashboard-container">
        <h1>Super Admin Dashboard</h1>
        
        <p>Total Users: <?php echo $totalUsers; ?></p>
        
        <h2>Pending Company Approvals</h2>
        <?php if (count($pendingCompanies) > 0): ?>
            <table>
                <tr>
                    <th>Company Name</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($pendingCompanies as $company): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($company['company_name']); ?></td>
                        <td>
                            <form action="approve_company.php" method="POST">
                                <input type="hidden" name="company_id" value="<?php echo $company['company_id']; ?>">
                                <input type="submit" name="approve" value="Approve">
                                <input type="submit" name="reject" value="Reject">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No pending companies for approval.</p>
        <?php endif; ?>
        
        <a href="logout.php">Logout</a> <!-- Add logout functionality -->
    </div>
</body>
</html>
