<?php
require "../../includes/auth_check.php";
requireRole("professor");
require "../../config/db.php";
require "../../includes/header.php";

/* ✅ GET COURSE FROM DB (FIXED) */
$stmt = $conn->prepare("
    SELECT course_name
    FROM courses
    WHERE professor_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $_SESSION["staff_id"]);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

$course = $row["course_name"] ?? "";

if ($course === "") {
    echo "<h2>My Students</h2>";
    echo "<p>❌ No course assigned to you.</p>";
    echo '<a href="dashboard.php">⬅ Back to Dashboard</a>';
    require "../../includes/footer.php";
    exit;
}

/* 1️⃣ FETCH STUDENTS */
$stmt = $conn->prepare("
    SELECT first_name, last_name, email
    FROM students
    WHERE course = ?
    ORDER BY first_name
");
$stmt->bind_param("s", $course);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>My Students</h2>
<p>Course: <strong><?= htmlspecialchars($course) ?></strong></p>

<table>
    <tr>
        <th>Name</th>
        <th>Email</th>
    </tr>

<?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row["first_name"] . " " . $row["last_name"]) ?></td>
            <td><?= htmlspecialchars($row["email"]) ?></td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="2">No students found</td>
    </tr>
<?php endif; ?>
</table>

<br>
<a href="dashboard.php">⬅ Back to Dashboard</a>

<?php require "../../includes/footer.php"; ?>
