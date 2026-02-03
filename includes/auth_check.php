<?php
require_once __DIR__ . "/config.php";

/* Start session only if not started */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* Role-based access control */
function requireRole($roles)
{
    /* Not logged in */
    if (!isset($_SESSION["role"])) {
        header("Location: " . BASE_URL . "auth/login.php");
        exit;
    }

    /* Convert single role to array */
    if (!is_array($roles)) {
        $roles = [$roles];
    }

    /* Role not allowed */
    if (!in_array($_SESSION["role"], $roles)) {
        http_response_code(403);
        echo "<h2>403 Forbidden – Access Denied</h2>";
        echo "<p>You do not have permission to access this page.</p>";
        exit;
    }
}
