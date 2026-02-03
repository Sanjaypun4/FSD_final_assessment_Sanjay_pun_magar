<?php
require_once __DIR__ . "/config.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION["role"])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

$role = $_SESSION["role"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Record Management System</title>

    <!-- ALWAYS absolute path -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/main.css">
</head>
<body>

<!-- TOP BAR -->
<div class="topbar">
    <div class="title">Student Record Management System</div>
    <div class="user-info">
        Welcome: <strong><?= htmlspecialchars($role) ?></strong>
    </div>
</div>

<div class="wrapper">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <ul>

        <?php if ($role === "super_admin"): ?>
            <li><a href="<?= BASE_URL ?>admin/dashboard.php">Dashboard</a></li>
            <li><a href="<?= BASE_URL ?>admin/create_user.php">Add Staff / Student</a></li>
            <li><a href="<?= BASE_URL ?>admin/manage_students.php">Manage Students</a></li>
            <li><a href="<?= BASE_URL ?>admin/manage_staff.php">Manage Staff</a></li>

        <?php elseif ($role === "registrar"): ?>
            <li><a href="<?= BASE_URL ?>staff/registrar/dashboard.php">Dashboard</a></li>
            <li><a href="<?= BASE_URL ?>staff/registrar/view_students.php">Manage Student Records</a></li>
            <li><a href="<?= BASE_URL ?>staff/registrar/register_course.php">Register Courses</a></li>
            <li><a href="<?= BASE_URL ?>staff/registrar/issue_transcript.php">Issue Transcripts</a></li>

        <?php elseif ($role === "professor"): ?>
            <li><a href="<?= BASE_URL ?>staff/professor/dashboard.php">Dashboard</a></li>
            <li><a href="<?= BASE_URL ?>staff/professor/view_students.php">View Students</a></li>
            <li><a href="<?= BASE_URL ?>staff/professor/mark_attendance.php">Attendance</a></li>
            <li><a href="<?= BASE_URL ?>staff/professor/enter_grades.php">Enter Grades</a></li>
            <li><a href="<?= BASE_URL ?>staff/professor/show_results.php">Results</a></li>

        <?php elseif ($role === "finance"): ?>
            <li><a href="<?= BASE_URL ?>staff/finance/dashboard.php">Dashboard</a></li>
            <li><a href="<?= BASE_URL ?>staff/finance/update_fee.php">Update Fees</a></li>
            <li><a href="<?= BASE_URL ?>staff/finance/update_fee_list.php">Fee List</a></li>
            <li><a href="<?= BASE_URL ?>staff/finance/view_payments.php">Payments</a></li>
            <li><a href="<?= BASE_URL ?>staff/finance/reports.php">Reports</a></li>

        <?php elseif ($role === "student"): ?>
            <li><a href="<?= BASE_URL ?>student/dashboard.php">Dashboard</a></li>
            <li><a href="<?= BASE_URL ?>student/profile.php">Profile</a></li>
            <li><a href="<?= BASE_URL ?>student/attendance.php">Attendance</a></li>
            <li><a href="<?= BASE_URL ?>student/fees.php">Fees</a></li>
            <li><a href="<?= BASE_URL ?>student/results.php">Results</a></li>

        <?php endif; ?>

            <li class="logout">
                <a href="<?= BASE_URL ?>auth/logout.php">Logout</a>
            </li>

        </ul>
    </aside>

    <!-- MAIN CONTENT START -->
    <main class="main-content">
