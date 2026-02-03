<?php
session_start();

require_once "../includes/config.php";

/* Redirect if already logged in */
if (isset($_SESSION["role"])) {
    switch ($_SESSION["role"]) {
        case "super_admin":
            header("Location: " . BASE_URL . "admin/dashboard.php");
            break;
        case "registrar":
            header("Location: " . BASE_URL . "staff/registrar/dashboard.php");
            break;
        case "professor":
            header("Location: " . BASE_URL . "staff/professor/dashboard.php");
            break;
        case "finance":
            header("Location: " . BASE_URL . "staff/finance/dashboard.php");
            break;
        case "student":
            header("Location: " . BASE_URL . "student/dashboard.php");
            break;
    }
    exit;
}

$error = $_GET["error"] ?? "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Student Record Management System</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Login</h2>

        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= BASE_URL ?>auth/login_process.php">
            <input type="text" name="user_id" placeholder="User ID" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</div>

</body>
</html>
