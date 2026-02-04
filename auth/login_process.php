<?php
session_start();

require_once "../includes/config.php";
require_once "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}

$loginId  = trim($_POST["user_id"] ?? "");
$password = trim($_POST["password"] ?? "");

$stmt = $conn->prepare("
    SELECT 
        u.id        AS uid,
        u.user_id   AS login_id,
        u.password,
        u.role,
        st.id       AS student_id,
        sf.id       AS staff_id,
        sf.course
    FROM users u
    LEFT JOIN students st ON st.user_id = u.id
    LEFT JOIN staff sf ON sf.user_id = u.id
    WHERE u.user_id = ?
    LIMIT 1
");
$stmt->bind_param("s", $loginId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || !password_verify($password, $user["password"])) {
    header("Location: " . BASE_URL . "auth/login.php?error=Wrong+credentials");
    exit;
}

session_regenerate_id(true);

$_SESSION["uid"]     = (int)$user["uid"];      // users.id
$_SESSION["user_id"] = $user["login_id"];      // PRO4763
$_SESSION["role"]    = $user["role"];

if ($user["role"] === "student") {
    if (!$user["student_id"]) {
        session_destroy();
        header("Location: " . BASE_URL . "auth/login.php?error=Student+record+missing");
        exit;
    }
    $_SESSION["student_id"] = (int)$user["student_id"];
}

if ($user["role"] === "professor" && $user["staff_id"]) {
    $_SESSION["staff_id"] = (int)$user["staff_id"];
    $_SESSION["course"]   = $user["course"];
}

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
}

exit;
