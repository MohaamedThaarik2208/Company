
<?php
session_start();
require "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (($_SESSION["role"] ?? "") !== "employee") {
    header("Location: admin_dashboard.php");
    exit;
}

$id = (int)$_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT id, name, email, phone, department, status, created_at
     FROM users
     WHERE id = ? AND role = 'employee'
     LIMIT 1"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$employee = $result->fetch_assoc();
$stmt->close();

if (!$employee) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Employee Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <h2>Employee Dashboard</h2>
    <div>
        <span>Welcome, <?= htmlspecialchars($employee["name"]) ?></span>
        <a class="nav-button" href="logout.php">Logout</a>
    </div>
</nav>

<main class="dashboard">
    <h1>My Details</h1>

    <p class="hint">
        You can view your own profile details here.
    </p>

    <div class="profile-card">
        <div class="profile-avatar">
            <?= htmlspecialchars(strtoupper(substr($employee["name"], 0, 1))) ?>
        </div>

        <h2><?= htmlspecialchars($employee["name"]) ?></h2>

        <span class="status approved">Approved Employee</span>

        <div class="detail-grid">
            <div>
                <span>Employee ID</span>
                <strong><?= (int)$employee["id"] ?></strong>
            </div>

            <div>
                <span>Full Name</span>
                <strong><?= htmlspecialchars($employee["name"]) ?></strong>
            </div>

            <div>
                <span>Email</span>
                <strong><?= htmlspecialchars($employee["email"]) ?></strong>
            </div>

            <div>
                <span>Phone</span>
                <strong><?= htmlspecialchars($employee["phone"]) ?></strong>
            </div>

            <div>
                <span>Department</span>
                <strong><?= htmlspecialchars($employee["department"]) ?></strong>
            </div>

            <div>
                <span>Registered Date</span>
                <strong><?= htmlspecialchars($employee["created_at"]) ?></strong>
            </div>
        </div>
    </div>
</main>

</body>
</html>
