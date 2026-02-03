<?php
require_once "../includes/auth_check.php";
requireRole("super_admin");

require_once "../config/db.php";

/* Check student ID */
if (!isset($_GET["id"])) {
    die("Student ID missing");
}

$id = (int) $_GET["id"];

/* Fetch student */
$stmt = $conn->prepare(
    "SELECT first_name, last_name, email, dob, course, address
     FROM students WHERE id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Student not found");
}

$student = $result->fetch_assoc();

/* Update */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $stmt = $conn->prepare(
        "UPDATE students
         SET first_name=?, last_name=?, email=?, dob=?, course=?, address=?
         WHERE id=?"
    );
    $stmt->bind_param(
        "ssssssi",
        $_POST["first_name"],
        $_POST["last_name"],
        $_POST["email"],
        $_POST["dob"],
        $_POST["course"],
        $_POST["address"],
        $id
    );
    $stmt->execute();

    header("Location: manage_students.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
</head>
<body>

<h2>Edit Student</h2>

<form method="post">
    <input type="text" name="first_name" value="<?= htmlspecialchars($student['first_name']) ?>" required><br><br>
    <input type="text" name="last_name" value="<?= htmlspecialchars($student['last_name']) ?>" required><br><br>
    <input type="email" name="email" value="<?= htmlspecialchars($student['email']) ?>" required><br><br>
    <input type="date" name="dob" value="<?= htmlspecialchars($student['dob']) ?>" required><br><br>
    <input type="text" name="course" value="<?= htmlspecialchars($student['course']) ?>" required><br><br>
    <input type="text" name="address" value="<?= htmlspecialchars($student['address']) ?>" required><br><br>

    <button type="submit">Update Student</button>
</form>

<br>
<a href="manage_students.php">⬅ Back</a>

</body>
</html>
