<?php
require "../includes/auth_check.php";
requireRole("super_admin");

require "../config/db.php";
require "../includes/header.php";

/* Check student ID */
if (!isset($_GET["id"])) {
    die("Student ID missing");
}

$studentId = (int) $_GET["id"];

/* Fetch student + user */
$stmt = $conn->prepare("
    SELECT 
        s.id AS student_id,
        s.user_id AS user_pk,
        u.user_id AS login_id,
        s.first_name,
        s.last_name,
        s.email,
        s.dob,
        s.course,
        s.address
    FROM students s
    JOIN users u ON u.id = s.user_id
    WHERE s.id = ?
");
$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Student not found");
}

$student = $result->fetch_assoc();

/* Update */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $stmt = $conn->prepare("
        UPDATE students
        SET 
            first_name = ?,
            last_name  = ?,
            email      = ?,
            dob        = ?,
            course     = ?,
            address    = ?
        WHERE id = ?
    ");
    $stmt->bind_param(
        "ssssssi",
        $_POST["first_name"],
        $_POST["last_name"],
        $_POST["email"],
        $_POST["dob"],
        $_POST["course"],
        $_POST["address"],
        $studentId
    );
    $stmt->execute();

    header("Location: manage_students.php");
    exit;
}
?>

<h2>Edit Student</h2>

<form method="post">

    <p><strong>Login ID:</strong> <?= htmlspecialchars($student["login_id"]) ?></p>
    <p style="color:gray;">(Login ID cannot be changed)</p><br>

    <input type="text" name="first_name"
           value="<?= htmlspecialchars($student["first_name"]) ?>" required><br><br>

    <input type="text" name="last_name"
           value="<?= htmlspecialchars($student["last_name"]) ?>" required><br><br>

    <input type="email" name="email"
           value="<?= htmlspecialchars($student["email"]) ?>" required><br><br>

    <input type="date" name="dob"
           value="<?= htmlspecialchars($student["dob"]) ?>" required><br><br>

    <input type="text" name="course"
           value="<?= htmlspecialchars($student["course"]) ?>" required><br><br>

    <input type="text" name="address"
           value="<?= htmlspecialchars($student["address"]) ?>" required><br><br>

    <button type="submit">Update Student</button>
</form>

<br>
<a href="manage_students.php">⬅ Back</a>

<?php require "../includes/footer.php"; ?>
