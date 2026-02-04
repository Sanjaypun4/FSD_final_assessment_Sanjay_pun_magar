<?php
require "../includes/auth_check.php";
requireRole("student");

require "../config/db.php";
require "../includes/header.php";

/* ✅ Logged-in student numeric ID */
$studentId = $_SESSION["student_id"] ?? null;

if (!$studentId) {
    echo "<p style='color:red;'>❌ Student record not found.</p>";
    require "../includes/footer.php";
    exit;
}

/* ✅ Fetch LATEST fee record */
$stmt = $conn->prepare("
    SELECT total_fee, paid_amount, status, updated_at
    FROM fees
    WHERE student_id = ?
    ORDER BY updated_at DESC
    LIMIT 1
");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();
$fee = $result->fetch_assoc();

/* ✅ Safe defaults */
$totalFee   = 0;
$paidAmount = 0;
$status     = "Not Assigned";
$updatedAt  = "N/A";

if ($fee) {
    $totalFee   = (float)$fee["total_fee"];
    $paidAmount = (float)$fee["paid_amount"];
    $status     = $fee["status"] ?: "Assigned";
    $updatedAt  = $fee["updated_at"]
        ? date("Y-m-d", strtotime($fee["updated_at"]))
        : "N/A";
}

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
