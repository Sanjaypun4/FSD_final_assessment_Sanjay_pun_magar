<?php
require "../includes/auth_check.php";
requireRole("super_admin");
require "../config/db.php";

$id = (int)($_GET["id"] ?? 0);

$stmt = $conn->prepare("DELETE FROM staff WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: manage_staff.php");
exit;
