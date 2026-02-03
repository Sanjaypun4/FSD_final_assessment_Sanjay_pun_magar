<?php
require "../../includes/auth_check.php";
requireRole("finance");
require "../../config/db.php";
require "../../includes/header.php";

$sql = "
    SELECT 
        SUM(total_fee) AS total_fees,
        SUM(paid_amount) AS total_collected,
        SUM(total_fee - paid_amount) AS total_due
    FROM fees
";

$data = $conn->query($sql)->fetch_assoc();
?>

<h2>Financial Summary Report</h2>

<table border="1" cellpadding="8">
<tr>
    <th>Total Fees</th>
    <th>Total Collected</th>
    <th>Total Due</th>
</tr>
<tr>
    <td><?= number_format($data["total_fees"], 2) ?></td>
    <td><?= number_format($data["total_collected"], 2) ?></td>
    <td><?= number_format($data["total_due"], 2) ?></td>
</tr>
</table>

<br>
<a href="dashboard.php">⬅ Back</a>

<?php require "../../includes/footer.php"; ?>
