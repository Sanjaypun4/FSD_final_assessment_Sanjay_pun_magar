<?php
require "../../includes/auth_check.php";
requireRole("professor");
require "../../config/db.php";
require "../../includes/header.php";

/* Fetch assigned course */
$stmt = $conn->prepare("
    SELECT course_name
    FROM courses
    WHERE professor_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $_SESSION["staff_id"]);
$stmt->execute();

$course = $stmt->get_result()->fetch_assoc()["course_name"] ?? "Not Assigned";
?>

<h2 class="page-title">Professor Dashboard</h2>

<p class="info-text">
    Assigned Course: <strong><?= htmlspecialchars($_SESSION["course"] ?: "Not Assigned") ?></strong>
</p>

<div class="prof-dashboard">

    <a href="<?= BASE_URL ?>staff/professor/view_students.php" class="prof-card">
        <div class="icon">👨‍🎓</div>
        <div class="text">
            View Students
            <small>My Course</small>
        </div>
    </a>

    <a href="<?= BASE_URL ?>staff/professor/enter_grades.php" class="prof-card">
        <div class="icon">✍️</div>
        <div class="text">Enter Grades</div>
    </a>

    <a href="<?= BASE_URL ?>staff/professor/mark_attendance.php" class="prof-card">
        <div class="icon">📅</div>
        <div class="text">Mark Attendance</div>
    </a>

    <a href="<?= BASE_URL ?>staff/professor/show_results.php" class="prof-card">
        <div class="icon">📊</div>
        <div class="text">Show Results</div>
    </a>

    <a href="<?= BASE_URL ?>staff/professor/show_attendance.php" class="prof-card">
        <div class="icon">📋</div>
        <div class="text">Show Attendance</div>
    </a>

</div>

<?php require "../../includes/footer.php"; ?>
