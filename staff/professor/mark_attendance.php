<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require "../../includes/auth_check.php";
requireRole("professor");
require "../../config/db.php";
require "../../includes/header.php";

$course = $_SESSION["course"] ?? "";

if ($course === "" || $course === "Not Assigned") {
    echo "<h2>Mark Attendance</h2>";
    echo "<p style='color:red;'>❌ No course assigned.</p>";
    echo '<a href="dashboard.php">⬅ Back</a>';
    require "../../includes/footer.php";
    exit;
}

$stmt = $conn->prepare("
    SELECT id, first_name, last_name
    FROM students
    WHERE course = ?
    ORDER BY first_name
");
$stmt->bind_param("s", $course);
$stmt->execute();
$students = $stmt->get_result();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $studentId = (int)($_POST["student_id"] ?? 0);
    $date      = $_POST["date"] ?? "";
    $status    = $_POST["status"] ?? "";

    if ($studentId > 0 && $date !== "" && in_array($status, ["Present", "Absent"], true)) {

        $stmt = $conn->prepare("
            INSERT INTO attendance (student_id, course, `date`, status)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE status = VALUES(status)
        ");

        if (!$stmt) {
            die("Attendance query error: " . $conn->error);
        }

        $stmt->bind_param("isss", $studentId, $course, $date, $status);
        $stmt->execute();

        echo "<p style='color:green;'>✅ Attendance saved successfully</p>";
    } else {
        echo "<p style='color:red;'>❌ Invalid input</p>";
    }
}
?>

<h2>Mark Attendance</h2>

<form method="post">

    <label>
        Student:
        <select name="student_id" required>
            <option value="">Select Student</option>
            <?php while ($s = $students->fetch_assoc()): ?>
                <option value="<?= $s['id'] ?>">
                    <?= htmlspecialchars($s['first_name'] . " " . $s['last_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
    </label>

    <br><br>

    <label>
        Date:
        <input type="date" name="date" required>
    </label>

    <br><br>

    <label>
        Status:
        <select name="status" required>
            <option value="">Select</option>
            <option value="Present">Present</option>
            <option value="Absent">Absent</option>
        </select>
    </label>

    <br><br>

    <button type="submit">Save Attendance</button>

</form>

<br>
<a href="dashboard.php">⬅ Back to Dashboard</a>

<?php require "../../includes/footer.php"; ?>
