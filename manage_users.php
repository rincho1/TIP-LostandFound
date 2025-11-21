<?php
include 'includes/header.php';
include 'db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: signin.php");
    exit;
}

if (isset($_GET['action']) && isset($_GET['id'])) {
    $act = $_GET['action'];
    $id = (int)$_GET['id'];

    if ($act === 'delete') {
        if ($id === (int)$_SESSION['user_id']) {
            header("Location: manage_users.php?msg=Cannot delete self");
            exit;
        }
        $stmt = mysqli_prepare($conn, "DELETE FROM members WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: manage_users.php?msg=User deleted");
        exit;
    }

    if ($act === 'promote') {
        $stmt = mysqli_prepare($conn, "UPDATE members SET role='admin' WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: manage_users.php?msg=User promoted");
        exit;
    }

    if ($act === 'demote') {
        $stmt = mysqli_prepare($conn, "UPDATE members SET role='student' WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: manage_users.php?msg=User demoted");
        exit;
    }
}

$result = mysqli_query($conn, "SELECT id, fullname, email, phone, role, created_at FROM members ORDER BY created_at DESC");
$msg = $_GET['msg'] ?? '';
?>

<div class="container">
  <h1>Manage Users</h1>
  <?php if ($msg): ?><p style="color:green;"><?php echo htmlspecialchars($msg); ?></p><?php endif; ?>

  <?php if ($result && mysqli_num_rows($result) > 0): ?>
    <table border="1" cellpadding="6" cellspacing="0" width="100%">
      <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th><th>Actions</th></tr>
      <?php while ($u = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?php echo (int)$u['id']; ?></td>
          <td><?php echo htmlspecialchars($u['fullname']); ?></td>
          <td><?php echo htmlspecialchars($u['email']); ?></td>
          <td><?php echo htmlspecialchars($u['phone']); ?></td>
          <td><?php echo htmlspecialchars($u['role']); ?></td>
          <td>
            <?php if ($u['role'] === 'student'): ?>
              <a href="manage_users.php?action=promote&id=<?php echo (int)$u['id']; ?>">Promote to admin</a>
            <?php else: ?>
              <a href="manage_users.php?action=demote&id=<?php echo (int)$u['id']; ?>">Demote to student</a>
            <?php endif; ?>
            <?php if ($u['id'] !== $_SESSION['user_id']): ?>
              | <a href="manage_users.php?action=delete&id=<?php echo (int)$u['id']; ?>" onclick="return confirm('Delete this user?');">Delete</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <p>No users found.</p>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

