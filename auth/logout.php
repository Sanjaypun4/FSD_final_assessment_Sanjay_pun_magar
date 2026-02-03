<?php
session_start();

$_SESSION = [];

session_destroy();

header("Location: /~np03cs4a240387/student_management_system/auth/login.php");
exit;
