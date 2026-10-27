<?php
require_once "db.php";

if (
    !isset($_SESSION["user_id"]) ||
    ($_SESSION["role"] ?? "") !== "admin"
) {
    header("Location: admin_login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $employeeId = filter_input(INPUT_POST, "employee_id", FILTER_VALIDATE_INT);
    $action = $_POST["action"] ?? "";

    if ($employeeId && in_array($action, ["approved", "rejected"], true)) {
        $stmt = $conn->prepare(
            "UPDATE users SET status = ?
             WHERE id = ? AND role = 'employee'"
        );
        $stmt->bind_param("si", $action, $employeeId);

        if ($stmt->execute() && $stmt->affected_rows >= 0) {
            $message = "Employee status updated.";
        } else {
            $message = "Could not update employee status.";
        }

        $stmt->close();
    } else {
        $message = "Invalid action.";
    }
}

$result = $conn->query(
    "SELECT id, name, email, phone, department, status, created_at
     FROM users
     WHERE role = 'employee'
     ORDER BY created_at DESC"
);

if (!$result) {
    die("Unable to load employee details. Check your users table.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <h2>Admin Dashboard</h2>
    <div class="nav-links">
        <span>Welcome, <?= htmlspecialchars($_SESSION["name"]) ?></span>
        <a href="logout.php">Logout</a>
    </div>
</nav>

<main class="container">
    <h1>Employee Management</h1>
    <p>View registrations and approve or reject employee accounts.</p>

    <?php if ($message !== ""): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Registered</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($employee = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= (int)$employee["id"] ?></td>
                        <td><?= htmlspecialchars($employee["name"]) ?></td>
                        <td><?= htmlspecialchars($employee["email"]) ?></td>
                        <td><?= htmlspecialchars($employee["phone"]) ?></td>
                        <td><?= htmlspecialchars($employee["department"]) ?></td>
                        <td>
                            <span class="status <?= htmlspecialchars($employee["status"]) ?>">
                                <?= htmlspecialchars(ucfirst($employee["status"])) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($employee["created_at"] ?? "") ?></td>
                        <td>
                            <?php if ($employee["status"] !== "approved"): ?>
                                <form method="POST" class="inline-form">
                                    <input type="hidden" name="employee_id"
                                           value="<?= (int)$employee["id"] ?>">
                                    <input type="hidden" name="action" value="approved">
                                    <button class="small-btn approve" type="submit">
                                        Approve
                                    </button>
                                </form>
                            <?php endif; ?>

                            <?php if ($employee["status"] !== "rejected"): ?>
                                <form method="POST" class="inline-form"
                                      onsubmit="return confirm('Reject this employee?');">
                                    <input type="hidden" name="employee_id"
                                           value="<?= (int)$employee["id"] ?>">
                                    <input type="hidden" name="action" value="rejected">
                                    <button class="small-btn reject" type="submit">
                                        Reject
                                    </button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No employee registrations found.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

</body>
</html>