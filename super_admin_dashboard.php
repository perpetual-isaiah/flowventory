<?php
session_start();
require_once 'config.php';

// Ensure only Super Admin (role_id = 5) can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 5) {
    header('Location: login.php');
    exit();
}

// Fetch pending companies for approval
$stmt = $pdo->prepare("SELECT * FROM companies WHERE status = 0");
$stmt->execute();
$pendingCompanies = $stmt->fetchAll();

// Fetch all approved companies
$stmt2 = $pdo->prepare("SELECT * FROM companies WHERE status = 1");
$stmt2->execute();
$approvedCompanies = $stmt2->fetchAll();

// Fetch all users
$stmt3 = $pdo->prepare("SELECT * FROM users");
$stmt3->execute();
$users = $stmt3->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Super Admin Dashboard</title>
    <link rel="stylesheet" href="sastyles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<style>
    .modal {
  display: none;
  position: fixed;
  z-index: 999;
  left: 0; top: 0;
  width: 100%; height: 100%;
  background: rgba(0, 0, 0, 0.5);
}

.modal-content {
  background: #fff;
  padding: 20px;
  margin: 15% auto;
  border-radius: 8px;
  width: 90%;
  max-width: 400px;
  text-align: center;
}

.modal-actions button {
  margin: 10px;
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
}

#confirmYes {
  background-color: #d9534f;
  color: white;
}

#confirmNo {
  background-color: #6c757d;
  color: white;
}

</style>
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
        <div class="dashboard-container">
            <h1>👑 Super Admin Dashboard</h1>

            <h2>✅ Approved Companies</h2>
            <?php if (count($approvedCompanies) > 0): ?>
                <table>
                    <tr>
                        <th>Company Name</th>
                        <th>Action</th>
                    </tr>
                    <?php foreach ($approvedCompanies as $company): ?>
                        <tr id="company-row-<?= $company['company_id']; ?>">
                            <td><?= htmlspecialchars($company['company_name']); ?></td>
                            <td>
                                <button class="delete-btn" onclick="deleteCompany(<?= $company['company_id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p>No approved companies.</p>
            <?php endif; ?>

            

        <!-- Toast notification -->
        <div id="toast" class="toast"></div>
        

        <!-- Confirmation Modal -->
<div id="confirmModal" class="modal">
  <div class="modal-content">
    <p id="confirmMessage">Are you sure you want to delete this company?</p>
    <div class="modal-actions">
      <button id="confirmYes">Yes</button>
      <button id="confirmNo">Cancel</button>
    </div>
  </div>

</div>

    </div>

    <script>
       function showToast(message) {
    const toast = document.getElementById("toast");
    toast.textContent = message;
    toast.className = "toast show";  // Show toast by adding 'show' class

    // After 3 seconds, hide the toast
    setTimeout(() => {
        toast.className = toast.className.replace("show", ""); // Remove 'show' class to hide
    }, 3000);
}

let companyToDelete = null;

function deleteCompany(companyId) {
    companyToDelete = companyId;
    document.getElementById("confirmMessage").textContent = "Are you sure you want to delete this company?";
    document.getElementById("confirmModal").style.display = "block";
}

document.getElementById("confirmYes").onclick = () => {
    if (companyToDelete !== null) {
        fetch('delete_company.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `company_id=${companyToDelete}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById(`company-row-${companyToDelete}`).remove();
                showToast(data.message);
            } else {
                showToast(data.message || 'An error occurred.');
            }
            closeModal();
        })
        .catch(() => {
            showToast('Failed to delete the company.');
            closeModal();
        });
    }
};

document.getElementById("confirmNo").onclick = closeModal;

function closeModal() {
    document.getElementById("confirmModal").style.display = "none";
    companyToDelete = null;
}



    </script>
</body>
</html>
