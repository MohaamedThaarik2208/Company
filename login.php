<?php
require_once "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT id, name, email, password, role, status
         FROM users WHERE email = ? AND role = 'employee'"
    );
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (!password_verify($password, $user["password"])) {
            $message = "Invalid email or password.";
        } elseif ($user["status"] === "pending") {
            $message = "Your registration is waiting for admin approval.";
        } elseif ($user["status"] === "rejected") {
            $message = "Your registration was rejected. Contact the admin.";
        } elseif ($user["status"] === "approved") {
            session_regenerate_id(true);
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["name"] = $user["name"];
            $_SESSION["role"] = "employee";

            header("Location: employee_dashboard.php");
            exit;
        } else {
            $message = "Your account status is invalid. Contact the admin.";
        }
    } else {
        $message = "Invalid email or password.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <h2>Integra Solutions </h2>
    <a href="index.php">Home</a>
</nav>

<div class="form-box">
    <h2>Employee Login</h2>

    <?php if ($message !== ""): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button class="btn full" type="submit">Login</button>
    </form>

    <p>New employee? <a href="register.php">Register here</a></p>
</div>

</body>
</html>