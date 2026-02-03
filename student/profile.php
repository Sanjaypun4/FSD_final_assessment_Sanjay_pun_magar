<?php
require "../includes/auth_check.php";
requireRole("student");

require "../config/db.php";
require "../includes/header.php";

$userId = $_SESSION["user_id"];

/* ✅ STEP 1: Get student record using user_id */
$stmt = $conn->prepare("
    SELECT 
        s.id,
        s.first_name,
        s.last_name,
        s.email,
        s.course,
        s.address,
        s.dob,
        u.user_id AS student_code
    FROM students s
    JOIN users u ON u.id = s.user_id
    WHERE s.user_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

/* ❌ No student record */
if (!$student) {
    echo "<p style='color:red;'>❌ Student profile not found.</p>";
    require "../includes/footer.php";
    exit;
}
?>

<h2>My Profile</h2>

<table border="1" cellpadding="8">
<tr>
    <th>Student ID</th>
    <td><?= htmlspecialchars($student["student_code"]) ?></td>
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
