<?php
require_once "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm = $_POST["confirm_password"] ?? "";
    $phone = trim($_POST["phone"] ?? "");
    $department = trim($_POST["department"] ?? "");

    if (
        $name === "" || $email === "" || $password === "" ||
        $phone === "" || $department === ""
    ) {
        $message = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $message = "Password must have at least 8 characters.";
    } elseif ($password !== $confirm) {
        $message = "Passwords do not match.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $message = "This email is already registered.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = "employee";
            $status = "pending";

            $stmt = $conn->prepare(
                "INSERT INTO users
                (name, email, password, role, phone, department, status)
                VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "sssssss",
                $name, $email, $hash, $role,
                $phone, $department, $status
            );

            if ($stmt->execute()) {
                $message = "Registration successful! Wait for admin approval.";
            } else {
                $message = "Registration failed. Please try again.";
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
    <title>Employee Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <h2> Integra Solutions </h2>
    <a href="index.php">Home</a>
</nav>

<div class="form-box">
    <h2>Employee Registration</h2>

    <?php if ($message !== ""): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Employee Name</label>
        <input type="text" name="name" required>

        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password (minimum 8 characters)</label>
        <input type="password" name="password" minlength="8" required>

        <label>Confirm Password</label>
        <input type="password" name="confirm_password" minlength="8" required>

        <label>Phone</label>
        <input type="text" name="phone" required>

        <label>Department</label>
        <input type="text" name="department" required>

        <button class="btn full" type="submit">Register Employee</button>
    </form>

    <p>Already registered? <a href="login.php">Employee Login</a></p>
</div>

</body>
</html>