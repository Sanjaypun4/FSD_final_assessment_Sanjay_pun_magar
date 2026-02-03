<?php
require "../../includes/auth_check.php";
requireRole("finance");
require "../../config/db.php";
require "../../includes/header.php";

/* Validate student ID */
$studentId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($studentId <= 0) {
    echo "<p style='color:red;'>❌ Invalid student selected.</p>";
    echo "<a href='update_fee_list.php'>⬅ Go back</a>";
    require "../../includes/footer.php";
    exit;
}

/* Fetch student + fee info */
$stmt = $conn->prepare("
    SELECT 
        students.first_name,
        students.last_name,
        users.user_id AS student_code,
        fees.total_fee,
        fees.paid_amount
    FROM fees
    JOIN students ON fees.student_id = students.id
    JOIN users ON students.user_id = users.id
    WHERE students.id = ?
");

$stmt->bind_param("i", $studentId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p style='color:red;'>❌ Fee record not found for this student.</p>";
    echo "<a href='update_fee_list.php'>⬅ Go back</a>";
    require "../../includes/footer.php";
    exit;
}

$data = $result->fetch_assoc();

/* Handle payment update */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $newPayment = (float)$_POST["new_payment"];

    if ($newPayment <= 0) {
        echo "<p style='color:red;'>❌ Invalid payment amount</p>";
    } else {

        $paidAmount = $data["paid_amount"] + $newPayment;
        $totalFee   = $data["total_fee"];

        if ($paidAmount >= $totalFee) {
            $paidAmount = $totalFee;
            $status = "paid";
        } elseif ($paidAmount > 0) {
            $status = "partial";
        } else {
            $status = "unpaid";
        }

        $update = $conn->prepare("
            UPDATE fees
            SET paid_amount = ?, status = ?, updated_at = NOW()
            WHERE student_id = ?
        ");
        $update->bind_param("dsi", $paidAmount, $status, $studentId);

        if ($update->execute()) {
            echo "<p style='color:green;'>✅ Fee updated successfully</p>";

            // 🔄 Reload updated data
            $stmt->execute();
            $data = $stmt->get_result()->fetch_assoc();
        } else {
            echo "<p style='color:red;'>❌ Update failed</p>";
        }
    }
}
?>

<h2>Update Student Fee</h2>

<p>
    <strong>Student:</strong>
    <?= htmlspecialchars($data["first_name"] . " " . $data["last_name"]) ?>
    (<?= htmlspecialchars($data["student_code"]) ?>)
</p>

<p><strong>Total Fee:</strong> <?= number_format($data["total_fee"], 2) ?></p>
<p><strong>Already Paid:</strong> <?= number_format($data["paid_amount"], 2) ?></p>
<p><strong>Remaining:</strong>
    <?= number_format($data["total_fee"] - $data["paid_amount"], 2) ?>
</p>

<form method="post">
    <label>
        New Payment Amount:<br>
        <input type="number" name="new_payment" step="0.01" min="1" required>
    </label>

    <br><br>
    <button type="submit">Update Fee</button>
</form>

<br>
<a href="update_fee_list.php">⬅ Back to Student List</a>

<?php require "../../includes/footer.php"; ?>
