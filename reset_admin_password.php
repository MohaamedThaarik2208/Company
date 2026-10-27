
<?php
require_once "db.php";

$email = "john@technova.com";
$newPassword = "Admin@123";

$hashedPassword = password_hash(
    $newPassword,
    PASSWORD_DEFAULT
);

$stmt = $conn->prepare(
    "UPDATE admins SET password = ? WHERE email = ?"
);

$stmt->bind_param("ss", $hashedPassword, $email);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo "Admin password updated successfully!";
} else {
    echo "Password update failed. Check the admin email.";
}

$stmt->close();
?>
