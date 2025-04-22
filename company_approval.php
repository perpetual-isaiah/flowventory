<?php
// Fetch pending companies
$stmt = $pdo->prepare("SELECT * FROM companies WHERE status = 0");
$stmt->execute();
$pendingCompanies = $stmt->fetchAll();
?>

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
  <p>No pending companies.</p>
<?php endif; ?>
