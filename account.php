<?php
include 'includes/header.php';
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $fullname = trim($_POST['fullname'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        if ($fullname && $phone) {
            $sql = "UPDATE members SET fullname=?, phone=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssi", $fullname, $phone, $user_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $_SESSION['fullname'] = $fullname;
            echo "<p style='color:green;'>Profile updated.</p>";
        } else {
            echo "<p style='color:red;'>Please fill required fields.</p>";
        }
    }

    if (isset($_POST['change_password'])) {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_new_password'] ?? '';

        $sql = "SELECT password FROM members WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);

        if ($row && password_verify($current, $row['password'])) {
            if (strlen($new) >= 6 && $new === $confirm) {
                $hash = password_hash($new, PASSWORD_DEFAULT);
                $sql2 = "UPDATE members SET password=? WHERE id=?";
                $stmt2 = mysqli_prepare($conn, $sql2);
                mysqli_stmt_bind_param($stmt2, "si", $hash, $user_id);
                mysqli_stmt_execute($stmt2);
                mysqli_stmt_close($stmt2);
                echo "<p style='color:green;'>Password changed successfully.</p>";
            } else {
                echo "<p style='color:red;'>New password must match and be at least 6 characters.</p>";
            }
        } else {
            echo "<p style='color:red;'>Current password is incorrect.</p>";
        }
    }
}

$sql = "SELECT id, fullname, email, phone, gender, role FROM members WHERE id=?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);
?>

<div class="container">
  <h1>My Account</h1>

  <form method="POST">
    <h3>Profile</h3>
    <label>Full Name</label>
    <input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>

    <label>Email (read-only)</label>
    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>

    <label>Phone</label>
    <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>

    <button type="submit" name="update_profile">Update Profile</button>
  </form>

  <hr>

  <form method="POST">
    <h3>Change Password</h3>
    <label>Current Password</label>
    <input type="password" name="current_password" required>

    <label>New Password</label>
    <input type="password" name="new_password" required>

    <label>Confirm New Password</label>
    <input type="password" name="confirm_new_password" required>

    <button type="submit" name="change_password">Change Password</button>
  </form>
</div>

<?php include 'includes/footer.php'; ?>
