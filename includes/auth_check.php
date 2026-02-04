<?php
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/../config/db.php";


/* Start session only if not started */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Not logged in */
if (!isset($_SESSION["role"]) || !isset($_SESSION["user_id"])) {
    header("Location: " . BASE_URL . "auth/login.php");
    exit;
}


if ($_SESSION["role"] === "student" && !isset($_SESSION["student_id"])) {

    $stmt = $conn->prepare("
        SELECT id 
        FROM students 
        WHERE user_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("s", $_SESSION["user_id"]); // ✅ STRING
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if ($row) {
        $_SESSION["student_id"] = $row["id"];
    } else {
        // HARD FAIL: student exists in users but not students
        session_destroy();
        echo "<h3 style='color:red;'>❌ Student record missing. Contact admin.</h3>";
        exit;
    }
}

/* Role-based access control */
function requireRole($roles)
{
    if (!is_array($roles)) {
        $roles = [$roles];
    }

    if (!isset($_SESSION["role"]) || !in_array($_SESSION["role"], $roles)) {
        http_response_code(403);
        echo "<h2>403 Forbidden – Access Denied</h2>";
        exit;
    }
}
