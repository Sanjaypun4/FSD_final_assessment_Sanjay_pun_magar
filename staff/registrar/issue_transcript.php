<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../../includes/auth_check.php";
requireRole("registrar");
require "../../config/db.php";
require "../../includes/header.php";

$student = null;
$grades  = null;
$message = "";

if (isset($_GET["student_user_id"])) {

    $student_user_id = trim($_GET["student_user_id"]);

    /* ✅ Fetch student */
    $stmt = $conn->prepare("
        SELECT 
            s.id AS student_id,
            u.user_id AS student_code,
            s.first_name,
            s.last_name,
            s.course
        FROM students s
        JOIN users u ON u.id = s.user_id
        WHERE u.user_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $student_user_id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();

    /* ✅ Fetch grades ONLY (safe) */
    if ($student) {
        $stmt2 = $conn->prepare("
            SELECT 
                course,
                marks,
                result
            FROM grades
            WHERE student_id = ?
        ");
        $stmt2->bind_param("i", $student["student_id"]);
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
<p><strong>User ID:</strong> <?= htmlspecialchars($student["student_code"]) ?></p>
<p><strong>Name:</strong> <?= htmlspecialchars($student["first_name"] . " " . $student["last_name"]) ?></p>
<p><strong>Program:</strong> <?= htmlspecialchars($student["course"]) ?></p>

<h3>Transcript</h3>

<?php if ($grades && $grades->num_rows > 0): ?>
<table border="1" cellpadding="8">
<tr>
    <th>Course</th>
    <th>Marks</th>
    <th>Result</th>
</tr>

<?php while ($row = $grades->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row["course"]) ?></td>
    <td><?= htmlspecialchars($row["marks"]) ?></td>
    <td><?= htmlspecialchars($row["result"]) ?></td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<p>No grades available for this student.</p>
<?php endif; ?>

<?php elseif ($message): ?>
<p style="color:red;"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<br>
<a href="dashboard.php">⬅ Back to Dashboard</a>

<?php require "../../includes/footer.php"; ?>
