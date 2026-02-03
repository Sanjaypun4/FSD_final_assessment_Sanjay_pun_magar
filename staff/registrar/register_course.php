<?php
require "../../includes/auth_check.php";
requireRole("registrar");
require "../../config/db.php";
require "../../includes/header.php";

$message = "";
$message_type = ""; // success | error

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $student_user_id = trim($_POST["student_user_id"] ?? "");
    $course = trim($_POST["course"] ?? "");

    if ($student_user_id === "" || $course === "") {
        $message = "❌ All fields are required.";
        $message_type = "error";
    } else {

        // Find student by USER LOGIN ID (STUxxxx)
        $stmt = $conn->prepare("
            SELECT students.id
            FROM students
            JOIN users ON students.user_id = users.id
            WHERE users.user_id = ?
        ");
        $stmt->bind_param("s", $student_user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {

            $message = "❌ Student not found.";
            $message_type = "error";

        } else {

            $student = $result->fetch_assoc();

            // Update student's course
            $stmt2 = $conn->prepare(
                "UPDATE students SET course = ? WHERE id = ?"
            );
            $stmt2->bind_param("si", $course, $student["id"]);

            if ($stmt2->execute()) {
                $message = "✅ Student registered for course successfully.";
                $message_type = "success";
            } else {
                $message = "❌ Failed to register course. Try again.";
                $message_type = "error";
            }
        }
    }
}
?>

<h2>Register Student for Course</h2>

<?php if ($message): ?>
    <p><?php echo htmlspecialchars($message); ?></p>
<?php endif; ?>

<form method="post">

    <label>
        Student User ID (e.g. STU2582):<br>
        <input type="text" name="student_user_id" required>
    </label>

    <br><br>

    <label>
        Course:<br>
        <input type="text" name="course" required>
    </label>

    <br><br>

    <button type="submit">Register Course</button>

</form>

<br>
<a href="dashboard.php">⬅ Back to Dashboard</a>

<?php require "../../includes/footer.php"; ?>
