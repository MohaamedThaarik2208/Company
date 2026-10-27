
<?php
session_start();
require "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

if (($_SESSION["role"] ?? "") !== "admin") {
    header("Location: employee_dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admin_dashboard.php");
    exit;
}

$id = filter_input(INPUT_POST, "employee_id", FILTER_VALIDATE_INT);
$action = $_POST["action"] ?? "";

if (!$id || !in_array($action, ["approve", "reject"], true)) {
    $_SESSION["flash"] = "Invalid employee action.";
    header("Location: admin_dashboard.php");
    exit;
}

$status = ($action === "approve") ? "approved" : "rejected";

$stmt = $conn->prepare(
    "UPDATE users SET status = ?
     WHERE id = ? AND role = 'employee'"
);

$stmt->bind_param("si", $status, $id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    $_SESSION["flash"] = "Employee registration " . $status . " successfully.";
} else {
    $_SESSION["flash"] = "Employee not found or status was unchanged.";
}

$stmt->close();

header("Location: admin_dashboard.php");
exit;
?>
