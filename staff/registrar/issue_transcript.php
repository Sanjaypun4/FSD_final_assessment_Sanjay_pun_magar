<?php
require "../../includes/auth_check.php";
requireRole("registrar");
require "../../config/db.php";
require "../../includes/header.php";

$student = null;
$grades = null;
$message = "";

if (isset($_GET["student_user_id"])) {

    $student_user_id = trim($_GET["student_user_id"]);

    // 🔹 Get student info
    $stmt = $conn->prepare("
        SELECT students.id, users.user_id, students.first_name, students.last_name, students.course
        FROM students
        JOIN users ON students.user_id = users.id
        WHERE users.user_id = ?
    ");

    if (!$stmt) {
        die("Student query error: " . $conn->error);
    }

    $stmt->bind_param("s", $student_user_id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();

    // 🔹 Get grades only if student exists
    if ($student) {
        $stmt2 = $conn->prepare("
            SELECT course, grade, attendance
            FROM grades
            WHERE student_id = ?
        ");

        if (!$stmt2) {
            die("Grades query error: " . $conn->error);
        }

        $stmt2->bind_param("i", $student["id"]);
        $stmt2->execute();
        $grades = $stmt2->get_result();
    } else {
        $message = "❌ Student not found.";
    }
}
?>

<h2>Issue Transcript</h2>

<form method="get">
    <label>
        Student User ID:
        <input type="text" name="student_user_id" required>
    </label>
    <button type="submit">View Transcript</button>
</form>

<hr>

<?php if ($student): ?>

<h3>Student Information</h3>
<p><strong>User ID:</strong> <?php echo htmlspecialchars($student["user_id"]); ?></p>
<p><strong>Name:</strong> <?php echo htmlspecialchars($student["first_name"] . " " . $student["last_name"]); ?></p>
<p><strong>Program:</strong> <?php echo htmlspecialchars($student["course"]); ?></p>

<h3>Transcript</h3>

<?php if ($grades && $grades->num_rows > 0): ?>
<table border="1" cellpadding="8">
<tr>
    <th>Course</th>
    <th>Grade</th>
    <th>Attendance</th>
</tr>

<?php while ($row = $grades->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($row["course"]); ?></td>
    <td><?php echo htmlspecialchars($row["grade"]); ?></td>
    <td><?php echo htmlspecialchars($row["attendance"]); ?>%</td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<p>No grades available for this student.</p>
<?php endif; ?>

<?php elseif ($message): ?>
<p style="color:red;"><?php echo $message; ?></p>
<?php endif; ?>

<br>
<a href="dashboard.php">⬅ Back to Dashboard</a>

<?php require "../../includes/footer.php"; ?>
