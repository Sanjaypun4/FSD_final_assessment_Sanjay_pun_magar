<?php
require "../includes/auth_check.php";
requireRole("super_admin");
require "../config/db.php";
require "../includes/header.php";

/* Fetch staff */
$sql = "
SELECT 
    staff.id,
    users.user_id,
    staff.first_name,
    staff.last_name,
    staff.email,
    staff.department,
    staff.role_type
FROM staff
JOIN users ON staff.user_id = users.id
ORDER BY staff.first_name
";

$result = $conn->query($sql);
?>

<div class="main-content">

    <h2 class="page-title">Manage Staff</h2>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row["user_id"]) ?></td>
                    <td><?= htmlspecialchars($row["first_name"] . " " . $row["last_name"]) ?></td>
                    <td><?= htmlspecialchars($row["email"]) ?></td>
                    <td><?= htmlspecialchars($row["department"]) ?></td>
                    <td><?= htmlspecialchars($row["role_type"]) ?></td>
                    <td class="table-actions">
                        <a href="edit_staff.php?id=<?= $row['id'] ?>">Edit</a>
                        <a href="delete_staff.php?id=<?= $row['id'] ?>"
                           class="delete"
                           onclick="return confirm('Are you sure you want to delete this staff member?');">
                           Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <br>
    <a href="dashboard.php" class="btn btn-primary">⬅ Back to Dashboard</a>

</div>

<?php require "../includes/footer.php"; ?>
