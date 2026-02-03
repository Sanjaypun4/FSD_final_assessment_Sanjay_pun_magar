<?php
require "../../includes/auth_check.php";
requireRole("professor");
require "../../config/db.php";
require "../../includes/header.php";

/* 1️⃣ GET COURSE FROM DB (FIXED) */
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
    echo "<h2>Mark Attendance</h2>";
    echo "<p>❌ No course assigned.</p>";
    echo '<a href="dashboard.php">⬅ Back</a>';
    require "../../includes/footer.php";
    exit;
}

/* 2️⃣ FETCH STUDENTS */
$stmt = $conn->prepare("
    SELECT id, first_name, last_name
    FROM students
    WHERE course = ?
    ORDER BY first_name
");
$stmt->bind_param("s", $course);
$stmt->execute();
$students = $stmt->get_result();

/* 3️⃣ SAVE ATTENDANCE */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $studentId = (int)$_POST["student_id"];
    $date      = $_POST["date"];
    $status    = $_POST["status"];

    if (in_array($status, ["Present", "Absent"])) {

        $stmt = $conn->prepare("
            INSERT INTO attendance (student_id, course, date, status)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE status = VALUES(status)
        ");
        $stmt->bind_param("isss", $studentId, $course, $date, $status);
        $stmt->execute();

        echo "<p style='color:green;'>✅ Attendance saved</p>";
    }
}
?>

<h2>Mark Attendance</h2>
<p><strong>Course:</strong> <?= htmlspecialchars($course) ?></p>

<form method="post">

    <select name="student_id" required>
        <option value="">Select Student</option>
        <?php while ($s = $students->fetch_assoc()): ?>
            <option value="<?= $s["id"] ?>">
                <?= htmlspecialchars($s["first_name"] . " " . $s["last_name"]) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <br><br>

    <input type="date" name="date" required>
    <br><br>

    <select name="status" required>
        <option value="">Select</option>
        <option value="Present">Present</option>
        <option value="Absent">Absent</option>
    </select>

    <br><br>

    <button type="submit">Save Attendance</button>
</form>

<br>
<a href="dashboard.php">⬅ Back</a>

<?php require "../../includes/footer.php"; ?>
