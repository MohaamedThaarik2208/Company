
<?php
session_start();

if (isset($_SESSION["user_id"])) {
    if (($_SESSION["role"] ?? "") === "admin") {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: employee_dashboard.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Integra Solutions</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <h2>Integra Solutions</h2>

    <div>
        <a href="index.php">Home</a>
        <a href="register.php">Register</a>
        <a href="login.php">Employee Login</a>
        <a href="admin_login.php">Admin Login</a>
    </div>
</nav>

<section class="hero">
    <div class="hero-card">
        <h1>Company Management System</h1>

        <p>
            Employees can register and wait for admin approval.
            Approved employees can log in and view their own details.
            Admin can approve employees and view all employee details.
        </p>

        <a class="btn" href="register.php">
            Employee Registration
        </a>

        <a class="btn secondary" href="login.php">
            Employee Login
        </a>

        <a class="btn" href="admin_login.php">
            Admin Login
        </a>
    </div>
</section>

</body>
</html>
