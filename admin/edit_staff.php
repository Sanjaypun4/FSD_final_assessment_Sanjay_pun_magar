<?php
require "../includes/auth_check.php";
requireRole("super_admin");
require "../config/db.php";

$id = (int)($_GET["id"] ?? 0);

$stmt = $conn->prepare("SELECT * FROM staff WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$staff = $stmt->get_result()->fetch_assoc();

if (!$staff) {
    die("Staff not found");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $first = $_POST["first_name"];
    $last  = $_POST["last_name"];
    $email = $_POST["email"];
    $dept  = $_POST["department"];

    $stmt = $conn->prepare(
        "UPDATE staff 
         SET first_name=?, last_name=?, email=?, department=? 
         WHERE id=?"
    );
    $stmt->bind_param("ssssi", $first, $last, $email, $dept, $id);
    $stmt->execute();

    // redirect BEFORE any HTML output
    header("Location: manage_staff.php");
    exit;
}

require "../includes/header.php";
?>

<h2>Edit Staff</h2>

<form method="post">
    <input type="text" name="first_name"
           value="<?= htmlspecialchars($staff['first_name']) ?>" required><br><br>

    <input type="text" name="last_name"
           value="<?= htmlspecialchars($staff['last_name']) ?>" required><br><br>

    <input type="email" name="email"
           value="<?= htmlspecialchars($staff['email']) ?>" required><br><br>

    <input type="text" name="department"
           value="<?= htmlspecialchars($staff['department']) ?>" required><br><br>

    <button type="submit">Update Staff</button>
</form>

<?php require "../includes/footer.php"; ?>
