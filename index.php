<?php
// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (isset($_SESSION["role"])) {

    switch ($_SESSION["role"]) {

        case "super_admin":
            header("Location: /student_management_system/admin/dashboard.php");
            break;

        case "registrar":
            header("Location: /student_management_system/staff/register/dashboard.php");
            break;

        case "professor":
            header("Location: /student_management_system/staff/professor/dashboard.php");
            break;

        case "finance":
            header("Location: /student_management_system/staff/finance/dashboard.php");
            break;

        case "student":
            header("Location: /student_management_system/student/dashboard.php");
            break;

        default:
            // Unknown role → logout for safety
            header("Location: /student_management_system/auth/logout.php");
            break;
    }

    exit;
}


header("Location: /student_management_system/auth/login.php");
exit;
