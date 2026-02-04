<?php
require "../includes/auth_check.php";
requireRole("student");

require "../config/db.php";
require "../includes/header.php";

/* ✅ Logged-in student numeric ID (students.id) */
$studentId = $_SESSION["student_id"] ?? null;

if (!$studentId) {
    echo "<p style='color:red;'>❌ Student record not found.</p>";
    require "../includes/footer.php";
    exit;
}

/* ✅ Fetch student profile using students.id */
$stmt = $conn->prepare("
    SELECT id, first_name, last_name, email, course, address, dob
    FROM students
    WHERE id = ?
    LIMIT 1
");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    echo "<p style='color:red;'>❌ Student record not found.</p>";
    require "../includes/footer.php";
    exit;
}
?>

<h2>My Profile</h2>

<table border="1" cellpadding="8">
<tr>
    <th>Student ID</th>
    <td><?= htmlspecialchars($student["id"]) ?></td>
</tr>
<tr>
    <th>Name</th>
    <td><?= htmlspecialchars($student["first_name"] . " " . $student["last_name"]) ?></td>
</tr>
<tr>
    <th>Email</th>
    <td><?= htmlspecialchars($student["email"] ?? "N/A") ?></td>
</tr>
<tr>
    <th>Course</th>
    <td><?= htmlspecialchars($student["course"] ?? "N/A") ?></td>
</tr>
<tr>
    <th>Date of Birth</th>
    <td><?= htmlspecialchars($student["dob"] ?? "N/A") ?></td>
</tr>
<tr>
    <th>Address</th>
    <td><?= htmlspecialchars($student["address"] ?? "N/A") ?></td>
</tr>
</table>

<br>
<a href="dashboard.php">⬅ Back</a>

<?php require "../includes/footer.php"; ?>
