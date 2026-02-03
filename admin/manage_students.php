<?php
require "../includes/auth_check.php";
requireRole("super_admin");
require "../config/db.php";
require "../includes/header.php";

/* Fetch students */
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

<div class="main-content">

    <h2 class="page-title">Student List</h2>

    <!-- SEARCH -->
    <div class="search-box">
        <input type="text" id="search" placeholder="Search by User ID, Name or Email">
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Course</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody id="studentTable">
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row["user_id"]) ?></td>
                    <td><?= htmlspecialchars($row["first_name"] . " " . $row["last_name"]) ?></td>
                    <td><?= htmlspecialchars($row["email"]) ?></td>
                    <td><?= htmlspecialchars($row["course"]) ?></td>
                    <td class="table-actions">
                        <a href="edit_student.php?id=<?= $row['id'] ?>">Edit</a>
                        <a href="delete_student.php?id=<?= $row['id'] ?>"
                           class="delete"
                           onclick="return confirm('Are you sure you want to delete this student?');">
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

<!-- SEARCH JS -->
<script src="../assets/js/student_search.js"></script>

<?php require "../includes/footer.php"; ?>
