<?php
require "../../includes/auth_check.php";
requireRole("finance");
require "../../includes/header.php";
?>

<h2 class="finance-page-title">Finance Staff Dashboard</h2>

<div class="finance-dashboard">

    <a href="add_fee.php" class="finance-card">
        <div class="finance-icon">➕</div>
        <div class="finance-text">Add Student Fee</div>
    </a>

    <!-- FIXED LINK -->
    <a href="update_fee_list.php" class="finance-card">
        <div class="finance-icon">✏️</div>
        <div class="finance-text">Update Student Fee</div>
    </a>

    <a href="reports.php" class="finance-card">
        <div class="finance-icon">📊</div>
        <div class="finance-text">Financial Reports</div>
    </a>

</div>

<?php require "../../includes/footer.php"; ?>
