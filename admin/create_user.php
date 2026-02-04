<?php
require "../includes/auth_check.php";
requireRole("super_admin");

require "../config/db.php";
require "../includes/header.php";

$message = "";
$generated_id = "";
$generated_password = "";

function generateUserId($role)
{
    return strtoupper(substr($role, 0, 3)) . rand(1000, 9999);
}

function generatePassword()
{
    return substr(
        str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789@#"),
        0,
        10
    );
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $role = $_POST["role"] ?? "";

    $first = trim($_POST["first_name"]);
    $last  = trim($_POST["last_name"]);
    $email = trim($_POST["email"]);
    $dob   = $_POST["dob"];
    $address = trim($_POST["address"]);

    $course = trim($_POST["course"] ?? "");
    $department = trim($_POST["department"] ?? "");

    /* Generate credentials */
    $login_user_id   = generateUserId($role);
    $plain_password = generatePassword();
    $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);

    /* Ensure unique login ID */
    $check = $conn->prepare("SELECT id FROM users WHERE user_id = ?");
    $check->bind_param("s", $login_user_id);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        die("Generated User ID already exists. Try again.");
    }

    $conn->begin_transaction();

    try {

        $stmt = $conn->prepare(
            "INSERT INTO users (user_id, password, role)
             VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $login_user_id, $hashed_password, $role);
        $stmt->execute();

        $userPk = $conn->insert_id; // 🔥 REAL IDENTITY

        if ($role === "student") {

            if (empty($course)) {
                throw new Exception("Course is required for students.");
            }

            $stmt2 = $conn->prepare(
                "INSERT INTO students
                (user_id, first_name, last_name, email, dob, course, address)
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt2->bind_param(
                "issssss",
                $userPk,
                $first,
                $last,
                $email,
                $dob,
                $course,
                $address
            );
            $stmt2->execute();

        }
        
        else {

            if (empty($department)) {
                throw new Exception("Department is required for staff.");
            }

            if ($role === "professor" && empty($course)) {
                throw new Exception("Course is required for professor.");
            }

            $stmt2 = $conn->prepare(
                "INSERT INTO staff
                (user_id, first_name, last_name, email, dob, department, address, role_type, course)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt2->bind_param(
                "issssssss",
                $userPk,
                $first,
                $last,
                $email,
                $dob,
                $department,
                $address,
                $role,
                $course
            );
            $stmt2->execute();
        }

        $conn->commit();

        $message = "✅ User created successfully!";
        $generated_id = $login_user_id;
        $generated_password = $plain_password;

    } catch (Exception $e) {

        $conn->rollback();
        $message = "❌ Error: " . $e->getMessage();
    }
}
?>

<h2>Register Student / Staff</h2>

<?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if ($generated_id): ?>
    <h3>Generated Login Credentials</h3>
    <p><strong>User ID:</strong> <?= htmlspecialchars($generated_id) ?></p>
    <p><strong>Password:</strong> <?= htmlspecialchars($generated_password) ?></p>
    <p style="color:red;">⚠ Password is shown only once.</p>
<?php endif; ?>

<form method="post">

    <h3>Personal Information</h3>
    <input type="text" name="first_name" placeholder="First Name" required><br><br>
    <input type="text" name="last_name" placeholder="Last Name" required><br><br>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="date" name="dob" required><br><br>
    <input type="text" name="address" placeholder="Address" required><br><br>

    <h3>Role Information</h3>
    <select name="role" required>
        <option value="">Select Role</option>
        <option value="student">Student</option>
        <option value="registrar">Registrar</option>
        <option value="professor">Professor</option>
        <option value="finance">Finance Staff</option>
    </select><br><br>

    <input type="text" name="course" placeholder="Course (Student / Professor)"><br><br>
    <input type="text" name="department" placeholder="Department (Staff only)"><br><br>

    <button type="submit">Register & Generate Credentials</button>
</form>

<br>
<a href="dashboard.php">⬅ Back</a>

<?php require "../includes/footer.php"; ?>
