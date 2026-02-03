<?php
require "../../includes/auth_check.php";
requireRole("finance");
require "../../config/db.php";
require "../../includes/header.php";

/* Fetch students */
$students = $conn->query("
    SELECT students.id, students.first_name, students.last_name, users.user_id
    FROM students
    JOIN users ON students.user_id = users.id
    ORDER BY students.first_name
");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $student_id  = (int)$_POST["student_id"];
    $total_fee   = (float)$_POST["total_fee"];
    $paid_amount = (float)$_POST["paid_amount"];

    if ($student_id <= 0 || $total_fee <= 0) {
        die("❌ Invalid input");
    }

    if ($paid_amount >= $total_fee) {
        $paid_amount = $total_fee;
        $status = "paid";
    } elseif ($paid_amount > 0) {
        $status = "partial";
    } else {
        $status = "unpaid";
    }

    $stmt = $conn->prepare("
        INSERT INTO fees (student_id, total_fee, paid_amount, status, updated_at)
        VALUES (?, ?, ?, ?, NOW())
    ");

    if (!$stmt) {
        die("❌ Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("idds", $student_id, $total_fee, $paid_amount, $status);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>✅ Fee added successfully</p>";
    } else {
        echo "<p style='color:red;'>❌ " . $stmt->error . "</p>";
    }
}
?>

<h2>Add Student Fee</h2>

<form method="post">

    <label>
        Select Student:<br>
        <select name="student_id" required>
            <option value="">-- Select Student --</option>
            <?php while ($s = $students->fetch_assoc()): ?>
                <option value="<?= $s['id'] ?>">
                    <?= htmlspecialchars($s['first_name'] . " " . $s['last_name']) ?>
                    (<?= htmlspecialchars($s['user_id']) ?>)
                </option>
            <?php endwhile; ?>
        </select>
    </label>

    <br><br>

    <label>
        Total Fee:<br>
        <input type="number" name="total_fee" step="0.01" required>
    </label>

    <br><br>

    <label>
        Paid Amount:<br>
        <input type="number" name="paid_amount" step="0.01" value="0">
    </label>

    <br><br>

    <button type="submit">Save Fee</button>

</form>

<br>
<a href="view_payments.php">⬅ Back</a>

<?php require "../../includes/footer.php"; ?>
