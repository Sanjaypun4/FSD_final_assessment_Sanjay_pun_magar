<?php
require_once "../includes/auth_check.php";
requireRole("super_admin");

require_once "../config/db.php";

/* Check ID */
if (!isset($_GET["id"])) {
    die("Student ID missing");
}

$studentId = (int) $_GET["id"];

/* Get student.user_id (string) */
$stmt = $conn->prepare(
    "SELECT user_id FROM students WHERE id = ?"
);
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Student not found");
}

$row = $result->fetch_assoc();
$userId = $row["user_id"]; // STRING (e.g. STD40)

/* Delete student first */
$stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
$stmt->bind_param("i", $studentId);
$stmt->execute();

/* Delete linked user */
$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
$stmt->bind_param("s", $userId);
$stmt->execute();

header("Location: manage_students.php");
exit;
