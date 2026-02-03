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

$studentId = $studentRow["id"];

/* ✅ STEP 2: Fetch results */
$stmt = $conn->prepare("
    SELECT course, marks, result
    FROM grades
    WHERE student_id = ?
    ORDER BY course
");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>My Results</h2>

<table border="1" cellpadding="8">
<tr>
    <th>Course</th>
    <th>Marks</th>
    <th>Result</th>
</tr>

<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row["course"]) ?></td>
            <td><?= htmlspecialchars($row["marks"] ?? "N/A") ?></td>
            <td><?= htmlspecialchars($row["result"] ?? "N/A") ?></td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="3">No results available.</td>
    </tr>
<?php endif; ?>
</table>

<br>
<a href="dashboard.php">⬅ Back</a>

<?php require "../includes/footer.php"; ?>
