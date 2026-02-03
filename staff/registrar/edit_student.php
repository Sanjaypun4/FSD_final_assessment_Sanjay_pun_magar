<?php
require "../../includes/auth_check.php";
requireRole("registrar");
require "../../config/db.php";
require "../../includes/header.php";

$id = (int)($_GET["id"] ?? 0);
if ($id === 0) {
    die("Invalid Student");
}

/* Fetch student */
$stmt = $conn->prepare("
    SELECT first_name, last_name, email, dob, address
    FROM students
    WHERE id = ?
");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    die("Student not found");
}

/* Update personal info only */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $first   = trim($_POST["first_name"]);
    $last    = trim($_POST["last_name"]);
    $email   = trim($_POST["email"]);
    $dob     = $_POST["dob"];
    $address = trim($_POST["address"]);

    $stmt = $conn->prepare("
        UPDATE students 
        SET first_name = ?, last_name = ?, email = ?, dob = ?, address = ?
        WHERE id = ?
    ");
    $stmt->bind_param(
        "sssssi",
        $first,
        $last,
        $email,
        $dob,
        $address,
        $id
    );
    $stmt->execute();

    header("Location: view_students.php");
    exit;
}
?>

<h2>Edit Student (Personal Information)</h2>

<form method="post">

    <label>
        First Name:<br>
        <input type="text" name="first_name"
               value="<?php echo htmlspecialchars($student["first_name"]); ?>"
               required>
    </label>
    <br><br>

    <label>
        Last Name:<br>
        <input type="text" name="last_name"
               value="<?php echo htmlspecialchars($student["last_name"]); ?>"
               required>
    </label>
    <br><br>

    <label>
        Email:<br>
        <input type="email" name="email"
               value="<?php echo htmlspecialchars($student["email"]); ?>"
               required>
    </label>
    <br><br>

    <label>
        Date of Birth:<br>
        <input type="date" name="dob"
               value="<?php echo htmlspecialchars($student["dob"]); ?>"
               required>
    </label>
    <br><br>

    <label>
        Address:<br>
        <input type="text" name="address"
               value="<?php echo htmlspecialchars($student["address"]); ?>"
               required>
    </label>
    <br><br>

    <button type="submit">Update Student</button>
</form>

<br>
<a href="view_students.php">⬅ Back to Manage Students</a>

<?php require "../../includes/footer.php"; ?>
