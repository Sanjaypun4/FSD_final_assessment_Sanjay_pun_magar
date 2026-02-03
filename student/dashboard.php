<?php
require "../includes/auth_check.php";
requireRole("student");
require "../includes/header.php";
?>

<h2 class="finance-page-title">Student Dashboard</h2>

<div class="finance-dashboard">

    <a href="profile.php" class="finance-card">
        <div class="finance-icon">👤</div>
        <div class="finance-text">My Profile</div>
    </a>

    <a href="results.php" class="finance-card">
        <div class="finance-icon">📄</div>
        <div class="finance-text">My Results</div>
    </a>

    <a href="attendance.php" class="finance-card">
        <div class="finance-icon">📅</div>
        <div class="finance-text">My Attendance</div>
    </a>

    <a href="fees.php" class="finance-card">
        <div class="finance-icon">💰</div>
        <div class="finance-text">My Fees</div>
    </a>

    <a href="results.php" class="finance-card">
        <div class="finance-icon">🎓</div>
        <div class="finance-text">View Transcript</div>
    </a>

</div>

<?php require "../includes/footer.php"; ?>
