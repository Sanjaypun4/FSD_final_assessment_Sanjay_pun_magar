<?php
require "../config/db.php";

$q = $_GET['q'] ?? '';

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
WHERE 
    users.user_id LIKE ?
    OR students.first_name LIKE ?
    OR students.last_name LIKE ?
    OR students.email LIKE ?
ORDER BY students.first_name
";

$stmt = $conn->prepare($sql);
$search = "%$q%";
$stmt->bind_param("ssss", $search, $search, $search, $search);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()):
?>
<tr>
    <td><?= htmlspecialchars($row["user_id"]) ?></td>
    <td><?= htmlspecialchars($row["first_name"] . " " . $row["last_name"]) ?></td>
    <td><?= htmlspecialchars($row["email"]) ?></td>
    <td><?= htmlspecialchars($row["course"]) ?></td>
    <td class="table-actions">
        <a href="../admin/edit_student.php?id=<?= $row['id'] ?>">Edit</a>
        <a href="../admin/delete_student.php?id=<?= $row['id'] ?>"
           class="delete"
           onclick="return confirm('Are you sure?');">
           Delete
        </a>
    </td>
</tr>
<?php endwhile; ?>
