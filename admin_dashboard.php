<?php
include 'includes/header.php';
include 'db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: signin.php");
    exit;
}

$count_items = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM lost_items"))['total'];
$count_lost = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM lost_items WHERE status='lost'"))['total'];
$count_claimed = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM lost_items WHERE status IN ('claimed','returned')"))['total'];
$count_users = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM members"))['total'];
?>

<div class="container">
  <h1>Admin Dashboard</h1>
  <p>Total items: <?php echo $count_items; ?></p>
  <p>Currently lost: <?php echo $count_lost; ?></p>
  <p>Claimed/Returned: <?php echo $count_claimed; ?></p>
  <p>Registered users: <?php echo $count_users; ?></p>

  <p><a href="manage_items.php">Manage Items</a> | <a href="manage_users.php">Manage Users</a></p>
</div>

<?php include 'includes/footer.php'; ?>
