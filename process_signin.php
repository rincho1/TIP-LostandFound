<?php
session_start();
include 'db.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    $_SESSION['signin_error'] = "Please enter both email and password.";
    $_SESSION['signin_old_email'] = $email;
    header("Location: signin.php");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['signin_error'] = "Invalid email format.";
    $_SESSION['signin_old_email'] = $email;
    header("Location: signin.php");
    exit;
}

$sql = "SELECT id, fullname, password, role FROM members WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if ($user && password_verify($password, $user['password'])) {
    unset($_SESSION['signin_error'], $_SESSION['signin_old_email']);
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['fullname'] = $user['fullname'];
    $_SESSION['role'] = $user['role'];

    if ($user['role'] === 'admin') {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: index.php");
    }
    exit;
} else {
    $_SESSION['signin_error'] = "Invalid email or password.";
    $_SESSION['signin_old_email'] = $email;
    header("Location: signin.php");
    exit;
}
?>