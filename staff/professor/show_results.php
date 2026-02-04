<?php
require "../../includes/auth_check.php";
requireRole("professor");
require "../../config/db.php";
require "../../includes/header.php";

$course = $_SESSION["course"] ?? "";

if ($course === "" || $course === "Not Assigned") {
    echo "<h2>Results</h2>";
    echo "<p>❌ No course assigned to you.</p>";
    echo '<a href="dashboard.php">⬅ Back</a>';
    require "../../includes/footer.php";
    exit;
}

$stmt = $conn->prepare("
    SELECT 
        CONCAT(s.first_name, ' ', s.last_name) AS student_name,
        g.marks,
        g.result
    FROM grades g
    JOIN students s ON g.student_id = s.id
    WHERE g.course = ?
    ORDER BY student_name
");
$stmt->bind_param("s", $course);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Results</h2>
<p><strong>Course:</strong> <?= htmlspecialchars($course) ?></p>

<table border="1" cellpadding="8">
<tr>
    <th>Name</th>
    <th>Marks</th>
    <th>Result</th>
</tr>

<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row["student_name"]) ?></td>
            <td><?= htmlspecialchars($row["marks"]) ?></td>
            <td><?= htmlspecialchars($row["result"]) ?></td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="3">No results found.</td>
</tr>
<?php endif; ?>
</table>

<br>
<a href="dashboard.php">⬅ Back</a>

<?php require "../../includes/footer.php"; ?>
