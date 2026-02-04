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

/* ✅ Selected month (YYYY-MM) */
$month = $_GET["month"] ?? date("Y-m");

/* ✅ Convert month to date range */
$startDate = $month . "-01";
$endDate   = date("Y-m-t", strtotime($startDate));

/* ✅ Fetch attendance using DATE RANGE (SAFE) */
$stmt = $conn->prepare("
    SELECT date, status
    FROM attendance
    WHERE student_id = ?
    AND date BETWEEN ? AND ?
    ORDER BY date ASC
");
$stmt->bind_param("iss", $studentId, $startDate, $endDate);
$stmt->execute();
$result = $stmt->get_result();

/* Summary */
$total = 0;
$present = 0;
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
    $total++;
    if (strtolower($row["status"]) === "present") {
        $present++;
    }
}

$absent = $total - $present;
$percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;
?>

<h2>My Attendance</h2>

<!-- Month Filter -->
<form method="get">
    <label>
        Select Month:
        <input type="month" name="month" value="<?= htmlspecialchars($month) ?>">
    </label>
    <button type="submit">View</button>
</form>

<br>

<!-- Summary -->
<table border="1" cellpadding="8">
<tr>
    <th>Total Classes</th>
    <th>Present</th>
    <th>Absent</th>
    <th>Attendance %</th>
</tr>
<tr>
    <td><?= $total ?></td>
    <td><?= $present ?></td>
    <td><?= $absent ?></td>
    <td><?= $percentage ?>%</td>
</tr>
</table>

<br>

<!-- Attendance Table -->
<table border="1" cellpadding="10" width="50%">
<tr>
    <th>Date</th>
    <th>Status</th>
</tr>

<?php if ($total > 0): ?>
    <?php foreach ($data as $row): ?>
        <tr>
            <td><?= htmlspecialchars(date("Y-m-d", strtotime($row["date"]))) ?></td>
            <td><?= htmlspecialchars($row["status"]) ?></td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="2">No attendance records for this month.</td>
    </tr>
<?php endif; ?>
</table>

<br>
<a href="dashboard.php">⬅ Back to Dashboard</a>

<?php require "../includes/footer.php"; ?>
