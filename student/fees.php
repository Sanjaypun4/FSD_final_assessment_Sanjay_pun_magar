<?php
require "../includes/auth_check.php";
requireRole("student");

require "../config/db.php";
require "../includes/header.php";

$userId = $_SESSION["user_id"];

/* ✅ STEP 1: Get student_id */
$stmt = $conn->prepare("
    SELECT id 
    FROM students 
    WHERE user_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$studentRow = $stmt->get_result()->fetch_assoc();

if (!$studentRow) {
    echo "<p style='color:red;'>❌ Student record not found.</p>";
    require "../includes/footer.php";
    exit;
}

$studentId = (int)$studentRow["id"];

/* ✅ STEP 2: Fetch fee record */
$stmt = $conn->prepare("
    SELECT total_fee, paid_amount, status, updated_at
    FROM fees
    WHERE student_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$fee = $stmt->get_result()->fetch_assoc();

/* ✅ Default values if no record */
$totalFee   = $fee["total_fee"]   ?? 0;
$paidAmount = $fee["paid_amount"] ?? 0;
$status     = $fee["status"]      ?? "Not Assigned";
$updatedAt  = $fee["updated_at"]  ?? "N/A";

$remaining = max(0, $totalFee - $paidAmount);
?>

<h2>My Fee Status</h2>

<table border="1" cellpadding="10" width="50%">
<tr>
    <th>Total Fee</th>
    <td><?= htmlspecialchars($totalFee) ?></td>
</tr>
<tr>
    <th>Paid Amount</th>
    <td><?= htmlspecialchars($paidAmount) ?></td>
</tr>
<tr>
    <th>Remaining</th>
    <td><?= htmlspecialchars($remaining) ?></td>
</tr>
<tr>
    <th>Status</th>
    <td><?= htmlspecialchars($status) ?></td>
</tr>
<tr>
    <th>Last Updated</th>
    <td><?= htmlspecialchars($updatedAt) ?></td>
</tr>
</table>

<br>
<a href="dashboard.php">⬅ Back to Dashboard</a>

<?php require "../includes/footer.php"; ?>
