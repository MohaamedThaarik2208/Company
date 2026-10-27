
<?php
require_once "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT id, name, email, password, role
         FROM admins
         WHERE email = ? AND role = 'admin'
         LIMIT 1"
    );

    if (!$stmt) {
        die("SQL Error: " . htmlspecialchars($conn->error));
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password, $admin["password"])) {

        session_regenerate_id(true);

        $_SESSION["user_id"] = $admin["id"];
        $_SESSION["name"] = $admin["name"];
        $_SESSION["role"] = "admin";

        header("Location: admin_dashboard.php");
        exit;

    } else {
        $message = "Invalid admin email or password.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <h2>Integra Solutions </h2>
    <a href="index.php">Home</a>
</nav>

<div class="form-box">
    <h2>Admin Login</h2>

    <?php if ($message !== ""): ?>
        <p class="message">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <label>Admin Email</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button class="btn full" type="submit">
            Admin Login
        </button>
    </form>
</div>

</body>
</html>
