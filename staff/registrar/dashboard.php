<?php
require "../../includes/auth_check.php";
requireRole("registrar");
require "../../includes/header.php";
?>

<h2 class="registrar-page-title">Registrar Dashboard</h2>

<div class="registrar-dashboard">

    <!-- Manage Students -->
    <a href="view_students.php" class="registrar-card">
        <div class="registrar-icon">🎓</div>
        <div class="registrar-text">Manage Student Records</div>
    </a>

    <!-- Register Courses -->
    <a href="register_course.php" class="registrar-card">
        <div class="registrar-icon">📚</div>
        <div class="registrar-text">Register Students for Courses</div>
    </a>

    <!-- Issue Transcripts -->
    <a href="issue_transcript.php" class="registrar-card">
        <div class="registrar-icon">📄</div>
        <div class="registrar-text">Issue Transcripts</div>
    </a>

</div>

<?php require "../../includes/footer.php"; ?>
