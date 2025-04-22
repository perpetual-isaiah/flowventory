<?php
session_start();
require_once 'config.php';

// Check if the user is logged in and is a Super Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 5) {
    header("Location: login.php");
    exit;
}

$message = "";

// Approve or Reject company
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $companyId = $_POST['company_id'];

    if (isset($_POST['approve'])) {
        $stmt = $pdo->prepare("UPDATE companies SET status = 1 WHERE company_id = ?");
        $stmt->execute([$companyId]);
        $message = "✅ Company approved!";
    }

    if (isset($_POST['reject'])) {
        // Optional: Also delete users associated with the rejected company
        $stmt = $pdo->prepare("DELETE FROM users WHERE company_id = ?");
        $stmt->execute([$companyId]);

        $stmt = $pdo->prepare("DELETE FROM companies WHERE company_id = ?");
        $stmt->execute([$companyId]);
        $message = "❌ Company rejected and removed.";
    }
}

// Fetch latest pending companies
$stmt = $pdo->prepare("SELECT * FROM companies WHERE status = 0");
$stmt->execute();
$pendingCompanies = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Super Admin - Approve Companies</title>
    <style>
        table {
            border-collapse: collapse;
            width: 60%;
        }
        th, td {
            border: 1px solid #999;
            padding: 8px;
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            color: white;
            background-color: #4CAF50;
            border-radius: 4px;
            width: fit-content;
        }
    </style>
</head>
<body>

<h2>Pending Company Approvals</h2>

<?php if ($message): ?>
    <div class="message"><?php echo $message; ?></div>
<?php endif; ?>

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
                    <form action="" method="POST" style="display: inline;">
                        <input type="hidden" name="company_id" value="<?php echo $company['company_id']; ?>">
                        <button type="submit" name="approve">✅ Approve</button>
                        <button type="submit" name="reject" onclick="return confirm('Are you sure you want to reject this company?');">❌ Reject</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No pending companies.</p>
<?php endif; ?>

</body>
</html>
