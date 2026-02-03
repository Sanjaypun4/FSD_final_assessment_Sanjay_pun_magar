<?php
require "../../includes/auth_check.php";
requireRole("finance");
require "../../config/db.php";
require "../../includes/header.php";

$sql = "
    SELECT 
        students.id AS student_id,
        users.user_id AS student_code,
        students.first_name,
        students.last_name,
        IFNULL(fees.total_fee, 0) AS total_fee,
        IFNULL(fees.paid_amount, 0) AS paid_amount
    FROM students
    JOIN users ON students.user_id = users.id
    LEFT JOIN fees ON fees.student_id = students.id
    ORDER BY students.first_name
";

$result = $conn->query($sql);
?>

<h2>Update Student Fee</h2>

<table border="1" cellpadding="8">
<tr>
    <th>Student ID</th>
    <th>Name</th>
    <th>Total Fee</th>
    <th>Paid</th>
    <th>Action</th>
</tr>

<?php if ($result && $result->num_rows > 0): ?>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row["student_code"]) ?></td>
    <td><?= htmlspecialchars($row["first_name"] . " " . $row["last_name"]) ?></td>
    <td><?= number_format($row["total_fee"], 2) ?></td>
    <td><?= number_format($row["paid_amount"], 2) ?></td>
    <td>
        <a href="update_fee.php?id=<?= (int)$row["student_id"] ?>" class="btn">
            Update Fee
        </a>
    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="5">No students found.</td>
</tr>
<?php endif; ?>
</table>

<br>
<a href="dashboard.php">⬅ Back</a>

<?php require "../../includes/footer.php"; ?>
