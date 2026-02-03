<?php
require "../includes/auth_check.php";
requireRole("super_admin");
require "../includes/header.php";
?>

<h1 class="admin-dashboard-title">Admin Dashboard</h1>

<div class="admin-dashboard-cards">

    <a href="<?= BASE_URL ?>admin/create_user.php" class="admin-card">
        <div class="admin-icon">👤</div>
        <div class="admin-text">Add Staff / Student</div>
    </a>

    <a href="<?= BASE_URL ?>admin/manage_students.php" class="admin-card">
        <div class="admin-icon">🎓</div>
        <div class="admin-text">Manage Students</div>
    </a>

    <a href="<?= BASE_URL ?>admin/manage_staff.php" class="admin-card">
        <div class="admin-icon">🧑‍💼</div>
        <div class="admin-text">Manage Staff</div>
    </a>

    <a href="<?= BASE_URL ?>auth/logout.php" class="admin-card admin-logout">
        <div class="admin-icon">⏻</div>
        <div class="admin-text">Logout</div>
    </a>

</div>

<?php require "../includes/footer.php"; ?>
