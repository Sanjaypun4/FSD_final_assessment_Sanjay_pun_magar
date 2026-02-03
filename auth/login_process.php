<?php
session_start();
require_once "../includes/config.php";
require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

$user_id  = trim($_POST["user_id"] ?? "");
$password = trim($_POST["password"] ?? "");

/* AUTH + STAFF + COURSE */
$stmt = $conn->prepare("
    SELECT 
        u.id,
        u.user_id,
        u.password,
        u.role,
        s.id AS staff_id,
        s.course
    FROM users u
    LEFT JOIN staff s ON s.user_id = u.id
    WHERE u.user_id = ?
    LIMIT 1
");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || !password_verify($password, $user["password"])) {
    header("Location: " . BASE_URL . "auth/login.php?error=Wrong+credentials");
    exit;
}

/* 🔐 SESSION */
session_regenerate_id(true);

$_SESSION["uid"]      = $user["id"];        // numeric users.id
$_SESSION["user_id"]  = $user["user_id"];   // PRO7481
$_SESSION["role"]     = $user["role"];
$_SESSION["staff_id"] = $user["staff_id"] ?? null;

/* ✅ CRITICAL: SET COURSE FOR PROFESSOR */
if ($user["role"] === "professor") {
    $_SESSION["course"] = $user["course"] ?? "Not Assigned";
}

/* 🚀 REDIRECT */
switch ($user["role"]) {

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

    default:
        session_destroy();
        header("Location: " . BASE_URL . "auth/login.php?error=Invalid+role");
        break;
}

exit;
