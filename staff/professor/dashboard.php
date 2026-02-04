<?php

require "../../includes/auth_check.php";
requireRole("professor");
require "../../config/db.php";
require "../../includes/header.php";

/* Get assigned course from session (single source of truth) */
$course = $_SESSION["course"] ?? "Not Assigned";
?>

<h2 class="page-title">Professor Dashboard</h2>

<p class="info-text">
    Assigned Course: <strong><?= htmlspecialchars($course) ?></strong>
</p>

<?php if ($course === "Not Assigned" || $course === ""): ?>
    <p class="error-text">❌ No course assigned to you.</p>
<?php endif; ?>

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
