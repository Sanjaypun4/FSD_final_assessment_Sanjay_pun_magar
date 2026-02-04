<?php
require "../../includes/auth_check.php";
requireRole("professor");

require "../../config/db.php";
require "../../includes/header.php";

$course = $_SESSION["course"] ?? "";

if ($course === "" || $course === "Not Assigned") {
    echo "<h2>My Students</h2>";
    echo "<p>❌ No course assigned to you.</p>";
    echo '<a href="dashboard.php">⬅ Back to Dashboard</a>';
    require "../../includes/footer.php";
    exit;
}

$stmt = $conn->prepare("
    SELECT 
        s.first_name,
        s.last_name,
        s.email,
        u.user_id AS student_code
    FROM students s
    JOIN users u ON u.id = s.user_id
    WHERE s.course = ?
    ORDER BY s.first_name
");
$stmt->bind_param("s", $course);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>My Students</h2>
<p>Course: <strong><?= htmlspecialchars($course) ?></strong></p>

<table border="1" cellpadding="8">
    <tr>
        <th>Student ID</th>
        <th>Name</th>
        <th>Email</th>
    </tr>

<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row["student_code"]) ?></td>
            <td><?= htmlspecialchars($row["first_name"] . " " . $row["last_name"]) ?></td>
            <td><?= htmlspecialchars($row["email"]) ?></td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="3">No students found</td>
    </tr>
<?php endif; ?>
</table>

<br>
<a href="dashboard.php">⬅ Back to Dashboard</a>

<?php require "../../includes/footer.php"; ?>
