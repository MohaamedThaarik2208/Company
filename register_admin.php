
<?php
require "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $phone = trim($_POST["phone"] ?? "");
    $department = trim($_POST["department"] ?? "");

    if (
        $name === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($password) < 8 ||
        $phone === "" ||
        $department === ""
    ) {
        $message = "Fill all fields. Admin password needs 8 characters.";

    } else {

        $check = $conn->prepare(
            "SELECT id FROM users WHERE role = 'admin' LIMIT 1"
        );
        $check->execute();

        if ($check->get_result()->num_rows > 0) {
            $message = "An admin account already exists.";

        } else {

            $hash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $role = "admin";
            $status = "approved";

            $stmt = $conn->prepare(
                "INSERT INTO users
                (name, email, password, role, phone, department, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssssss",
                $name,
                $email,
                $hash,
                $role,
                $phone,
                $department,
                $status
            );

            if ($stmt->execute()) {
                $message =
                    "Admin created successfully. Delete register_admin.php now.";
            } else {
                $message = "Could not create admin. Check the email.";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Create Admin</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="form-box">
    <h2>Create First Admin</h2>

    <p class="hint">
        Create your first admin account here.
        Delete this file after setup.
    </p>

    <?php if ($message !== ""): ?>
        <p class="message">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Admin Name</label>
            <input type="text" name="name" required>
        </div>

        <div class="form-group">
            <label>Admin Email</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Admin Password (8+ characters)</label>
            <input type="password" name="password" minlength="8" required>
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input type="text" name="phone" required>
        </div>

        <div class="form-group">
            <label>Department</label>
            <input type="text" name="department" required>
        </div>

        <button class="btn full" type="submit">
            Create Admin
        </button>
    </form>
</div>

</body>
</html>
