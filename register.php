<?php 
include 'includes/header.php';
session_start();

$errors = $_SESSION['register_errors'] ?? [];
$old_data = $_SESSION['register_old_data'] ?? [];
unset($_SESSION['register_errors'], $_SESSION['register_old_data']);
?>

<div class="container">
  <h1>Register</h1>
  
  <?php if (!empty($errors)): ?>
    <div style="background-color: #fee; border: 1px solid #f66; padding: 10px; margin-bottom: 15px; border-radius: 4px;">
      <?php foreach ($errors as $error): ?>
        <p style="color: red; margin: 5px 0;"><?php echo htmlspecialchars($error); ?></p>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form action="process_register.php" method="POST">
    <label>Full Name:</label>
    <input type="text" name="fullname" value="<?php echo htmlspecialchars($old_data['fullname'] ?? ''); ?>" required>

    <label>Email:</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($old_data['email'] ?? ''); ?>" required pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" title="Please enter a valid email address">

    <label>Phone:</label>
    <input type="tel" name="phone" value="<?php echo htmlspecialchars($old_data['phone'] ?? ''); ?>" required pattern="09\d{9}" maxlength="11" title="Phone number must start with 09 and be exactly 11 digits" inputmode="numeric">

    <label>Password:</label>
    <input type="password" name="password" required minlength="6">

    <label>Confirm Password:</label>
    <input type="password" name="confirm_password" required minlength="6">

    <label>Gender:</label>
    <select name="gender" required>
      <option value="">--Select--</option>
      <option value="Male" <?php echo ($old_data['gender'] ?? '') === 'Male' ? 'selected' : ''; ?>>Male</option>
      <option value="Female" <?php echo ($old_data['gender'] ?? '') === 'Female' ? 'selected' : ''; ?>>Female</option>
      <option value="Other" <?php echo ($old_data['gender'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
      <option value="Prefer Not to Say" <?php echo ($old_data['gender'] ?? '') === 'Prefer Not to Say' ? 'selected' : ''; ?>>Prefer Not to Say</option>
    </select>

    <button type="submit">Register</button>
  </form>

  <p>Already have an account? <a href="signin.php">Sign in here</a></p>
</div>

<script>
document.querySelector('input[name="phone"]').addEventListener('input', function(e) {
  this.value = this.value.replace(/[^0-9]/g, '');
  if (this.value.length > 11) {
    this.value = this.value.slice(0, 11);
  }
});
</script>

<?php include 'includes/footer.php'; ?>