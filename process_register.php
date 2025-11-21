<?php
include 'db.php';
session_start();

$fullname = trim($_POST['fullname'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';
$gender = $_POST['gender'] ?? '';

$errors = [];

if (!$fullname || !$email || !$phone || !$password || !$confirm_password || !$gender) {
    $errors[] = "All fields are required.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}

if (!preg_match('/^09\d{9}$/', $phone)) {
    $errors[] = "Phone number must start with 09 and be exactly 11 digits.";
}

if ($password !== $confirm_password) {
    $errors[] = "Passwords do not match.";
}

if (strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters.";
}

$sql_check = "SELECT id FROM members WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql_check);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
if (mysqli_stmt_num_rows($stmt) > 0) {
    $errors[] = "Email is already registered.";
}
mysqli_stmt_close($stmt);

if ($errors) {
    foreach ($errors as $error) {
        echo "<p style='color:red;'>" . htmlspecialchars($error) . "</p>";
    }
    echo "<p><a href='register.php'>Go back</a></p>";
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO members (fullname, email, phone, password, gender, role) VALUES (?, ?, ?, ?, ?, 'student')";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sssss", $fullname, $email, $phone, $hashed_password, $gender);
$success = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($success) {
    header("Location: signin.php?registered=1");
    exit;
} else {
    echo "<p style='color:red;'>Error: " . htmlspecialchars(mysqli_error($conn)) . "</p>";
}
?>