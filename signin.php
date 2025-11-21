<?php 
session_start();

$success_message = '';
if (isset($_GET['registered'])) {
    $success_message = "Registration successful. Please sign in.";
}

$error = $_SESSION['signin_error'] ?? '';
$old_email = $_SESSION['signin_old_email'] ?? '';
unset($_SESSION['signin_error'], $_SESSION['signin_old_email']);

include 'includes/header.php';
?>

<div class="container">
  <h1>Sign In</h1>
  
  <?php if ($success_message): ?>
    <p style="color:green;"><?php echo htmlspecialchars($success_message); ?></p>
  <?php endif; ?>
  
  <?php if ($error): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
  <?php endif; ?>
  
  <form action="process_signin.php" method="POST">
    <label>Email:</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($old_email); ?>" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="Please enter a valid email address">

    <label>Password:</label>
    <input type="password" name="password" required>

    <button type="submit">Sign In</button>
  </form>

  <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>

<?php include 'includes/footer.php'; ?>