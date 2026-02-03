<?php
require "../../includes/auth_check.php";
requireRole("registrar");
require "../../config/db.php";
require "../../includes/header.php";

$sql = "
    SELECT 
        students.id,
        users.user_id,
        students.first_name,
        students.last_name,
        students.email,
        students.course
    FROM students
    JOIN users ON students.user_id = users.id
    ORDER BY students.first_name
";

$result = $conn->query($sql);
?>

<h2>Manage Students</h2>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>User ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Course</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row["user_id"]) ?></td>
            <td><?= htmlspecialchars($row["first_name"] . " " . $row["last_name"]) ?></td>
            <td><?= htmlspecialchars($row["email"]) ?></td>
            <td><?= htmlspecialchars($row["course"] ?? "Not Assigned") ?></td>
            <td>
                <a href="edit_student.php?id=<?= $row['id'] ?>">
                    Edit Personal Info
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

<br>
<a href="dashboard.php">⬅ Back to Dashboard</a>

<?php require "../../includes/footer.php"; ?>
