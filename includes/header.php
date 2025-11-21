<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$current_page = basename($_SERVER['PHP_SELF']);
$public_pages = ['signin.php', 'register.php', 'process_register.php', 'process_signin.php'];

$user_name = $_SESSION['fullname'] ?? '';
$user_role = $_SESSION['role'] ?? 'student';

if (!isset($_SESSION['user_id']) && !in_array($current_page, $public_pages)) {
    header("Location: signin.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Lost & Found System</title>
  <link rel="stylesheet" href="assets/style.css?v=11">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<?php if ($current_page === 'signin.php' || $current_page === 'register.php'): ?>
  <nav class="top-navbar">
    <div class="navbar-title">
      <i class="fas fa-thumbtack"></i>
      TIP Lost & Found
    </div>
    <ul>
      <li><a href="signin.php">Sign In</a></li>
      <li><a href="register.php">Register</a></li>
    </ul>
  </nav>
<?php else: ?>
  <div id="sidebar" class="sidebar collapsed">
    <div class="sidebar-header">
      <i class="fas fa-thumbtack"></i>
      <span class="label">TIP Lost & Found</span>
    </div>
    <hr class="sidebar-divider">

    <ul>
      <?php if ($user_role === 'student'): ?>
        <li><a href="index.php"><i class="fas fa-home"></i><span class="label">Home</span></a></li>
        <li><a href="claimed_items.php"><i class="fas fa-box-open"></i><span class="label">Claimed Items</span></a></li>
        <li><a href="account.php"><i class="fas fa-user"></i><span class="label">My Account</span></a></li>
      <?php elseif ($user_role === 'admin'): ?>
        <li><a href="admin_dashboard.php"><i class="fas fa-chart-line"></i><span class="label">Dashboard</span></a></li>
        <li><a href="manage_items.php"><i class="fas fa-clipboard-list"></i><span class="label">Manage Lost Items</span></a></li>
        <li><a href="manage_users.php"><i class="fas fa-users"></i><span class="label">Manage Users</span></a></li>
        <li><a href="reports.php"><i class="fas fa-file-export"></i><span class="label">Reports & Export</span></a></li>
      <?php endif; ?>
      <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span class="label">Logout (<?php echo htmlspecialchars($user_name); ?>)</span></a></li>
    </ul>

    <button id="hamburger" class="hamburger"><i class="fas fa-bars"></i></button>
  </div>

  <div class="content collapsed">
<?php endif; ?>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const hamburger = document.getElementById("hamburger");
  const sidebar = document.getElementById("sidebar");
  const content = document.querySelector(".content");

  if (hamburger && sidebar) {
    hamburger.addEventListener("click", function () {
      sidebar.classList.toggle("collapsed");
      sidebar.classList.toggle("open");
      if (content) content.classList.toggle("collapsed");
    });
  }
});
</script>