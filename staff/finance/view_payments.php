<?php
require "../../includes/auth_check.php";
requireRole("finance");
require "../../config/db.php";
require "../../includes/header.php";

/* Fetch fee records */
$sql = "
    SELECT 
        students.id AS student_id,
        users.user_id AS student_code,
        students.first_name,
        students.last_name,
        fees.total_fee,
        fees.paid_amount,
        (fees.total_fee - fees.paid_amount) AS remaining_fee,
        fees.status,
        fees.updated_at
    FROM fees
    INNER JOIN students ON fees.student_id = students.id
    INNER JOIN users ON students.user_id = users.id
    ORDER BY students.first_name
";

$result = $conn->query($sql);
?>

<h2 class="page-title">Student Fee Records</h2>

<div class="table-wrapper">
<table>
    <thead>
        <tr>
            <th>Student ID</th>
            <th>Name</th>
            <th>Total Fee</th>
            <th>Paid</th>
            <th>Remaining</th>
            <th>Status</th>
            <th>Last Updated</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row["student_code"]) ?></td>
                <td><?= htmlspecialchars($row["first_name"] . " " . $row["last_name"]) ?></td>
                <td><?= number_format($row["total_fee"], 2) ?></td>
                <td><?= number_format($row["paid_amount"], 2) ?></td>
                <td><?= number_format($row["remaining_fee"], 2) ?></td>
                <td><?= ucfirst(htmlspecialchars($row["status"])) ?></td>
                <td><?= htmlspecialchars($row["updated_at"]) ?></td>
                <td>
                    <a href="update_fee.php?id=<?= (int)$row['student_id'] ?>" 
                       class="btn btn-primary">
                        Update Fee
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="8" style="text-align:center;">
                No fee records found.
            </td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
</div>

<br>
<a href="dashboard.php" class="btn">⬅ Back to Dashboard</a>

<?php require "../../includes/footer.php"; ?>
