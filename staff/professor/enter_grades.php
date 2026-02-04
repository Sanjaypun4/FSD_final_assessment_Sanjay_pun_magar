<?php
require "../../includes/auth_check.php";
requireRole("professor");
require "../../config/db.php";
require "../../includes/header.php";

/* ✅ SINGLE SOURCE OF TRUTH */
$course = $_SESSION["course"] ?? "";

if ($course === "" || $course === "Not Assigned") {
    echo "<h2>Enter Grades</h2>";
    echo "<p>❌ No course assigned to you.</p>";
    echo '<a href="dashboard.php">⬅ Back</a>';
    require "../../includes/footer.php";
    exit;
}

/* 1️⃣ FETCH STUDENTS */
$stmt = $conn->prepare("
    SELECT id, first_name, last_name
    FROM students
    WHERE course = ?
    ORDER BY first_name
");
$stmt->bind_param("s", $course);
$stmt->execute();
$students = $stmt->get_result();

/* 2️⃣ SAVE / UPDATE GRADE */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $studentId = (int)($_POST["student_id"] ?? 0);
    $marks     = (int)($_POST["marks"] ?? -1);
    $resultVal = $_POST["result"] ?? "";

    if ($studentId <= 0 || $marks < 0 || $marks > 100 || $resultVal === "") {
        echo "<p style='color:red;'>❌ Invalid input</p>";
    } else {

        $stmt = $conn->prepare("
            INSERT INTO grades (student_id, course, marks, result)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                marks = VALUES(marks),
                result = VALUES(result)
        ");
        $stmt->bind_param("isis", $studentId, $course, $marks, $resultVal);
        $stmt->execute();

        echo "<p style='color:green;'>✅ Grade saved</p>";
    }
}
?>

<h2>Enter Grades</h2>
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

    <input type="number" name="marks" min="0" max="100" required>

    <br><br>

    <select name="result" required>
        <option value="">Select Result</option>
        <option value="Pass">Pass</option>
        <option value="Fail">Fail</option>
    </select>

    <br><br>

    <button type="submit">Save Grade</button>
</form>

<br>
<a href="dashboard.php">⬅ Back</a>

<?php require "../../includes/footer.php"; ?>
