<?php
require "../../includes/auth_check.php";
requireRole("professor");
require "../../config/db.php";
require "../../includes/header.php";

$month = $_GET["month"] ?? date("m");
$year  = $_GET["year"]  ?? date("Y");

$course = $_SESSION["course"] ?? "";

if ($course === "" || $course === "Not Assigned") {
    echo "<h2>Attendance Sheet</h2>";
    echo "<p>❌ No course assigned.</p>";
    echo '<a href="dashboard.php">⬅ Back</a>';
    require "../../includes/footer.php";
    exit;
}

$stmt = $conn->prepare("
    SELECT 
        CONCAT(s.first_name, ' ', s.last_name) AS student_name,
        a.date,
        a.status
    FROM attendance a
    JOIN students s ON a.student_id = s.id
    WHERE a.course = ?
      AND MONTH(a.date) = ?
      AND YEAR(a.date) = ?
    ORDER BY a.date
");
$stmt->bind_param("sii", $course, $month, $year);
$stmt->execute();
$result = $stmt->get_result();

$dates = [];
$data  = [];

while ($row = $result->fetch_assoc()) {
    $dates[$row["date"]] = true;
    $data[$row["student_name"]][$row["date"]] =
        ($row["status"] === "Present") ? "P" : "A";
}

$dates = array_keys($dates);
?>

<h2>Attendance Sheet</h2>

<p>
<strong>Course:</strong> <?= htmlspecialchars($course) ?><br>
<strong>Month:</strong> <?= date("F", mktime(0,0,0,$month,1)) ?> <?= $year ?>
</p>

<form method="get">
    Month:
    <select name="month">
        <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>" <?= ($m == $month) ? "selected" : "" ?>>
                <?= date("F", mktime(0,0,0,$m,1)) ?>
            </option>
        <?php endfor; ?>
    </select>

    Year:
    <select name="year">
        <?php for ($y = date("Y") - 5; $y <= date("Y"); $y++): ?>
            <option value="<?= $y ?>" <?= ($y == $year) ? "selected" : "" ?>>
                <?= $y ?>
            </option>
        <?php endfor; ?>
    </select>

    <button type="submit">View</button>
</form>

<br>

<table border="1" cellpadding="8">
<tr>
    <th>Student Name</th>
    <?php foreach ($dates as $d): ?>
        <th><?= date("d", strtotime($d)) ?></th>
    <?php endforeach; ?>
</tr>

<?php if (!empty($data)): ?>
    <?php foreach ($data as $student => $records): ?>
    <tr>
        <td><?= htmlspecialchars($student) ?></td>
        <?php foreach ($dates as $d): ?>
            <td><?= $records[$d] ?? "-" ?></td>
        <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
<?php else: ?>
<tr>
    <td colspan="<?= count($dates) + 1 ?>">No attendance found.</td>
</tr>
<?php endif; ?>
</table>

<br>
<a href="dashboard.php">⬅ Back</a>

<?php require "../../includes/footer.php"; ?>
