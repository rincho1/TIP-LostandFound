<?php
include 'includes/header.php';
include 'db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: signin.php");
    exit;
}

$msg = $_GET['msg'] ?? '';

$edit_item = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM lost_items WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $edit_item = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);
}

$sql = "SELECT lost_items.*, m1.fullname AS posted_by_name, m2.fullname AS claimer_name
        FROM lost_items
        LEFT JOIN members m1 ON lost_items.user_id = m1.id
        LEFT JOIN members m2 ON lost_items.claimed_by = m2.id
        ORDER BY lost_items.created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="container">
  <h1>Manage Lost Items</h1>
  <?php if ($msg): ?><p style="color:green;"><?php echo htmlspecialchars($msg); ?></p><?php endif; ?>

  <h3><?php echo $edit_item ? 'Edit Item' : 'Add New Item'; ?></h3>
  <form action="process_item.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="action" value="<?php echo $edit_item ? 'update' : 'add'; ?>">
    <?php if ($edit_item): ?><input type="hidden" name="id" value="<?php echo (int)$edit_item['id']; ?>"><?php endif; ?>

    <label>Description</label>
    <textarea name="description" required><?php echo htmlspecialchars($edit_item['description'] ?? ''); ?></textarea>

    <label>Date Lost</label>
    <input type="date" name="date_lost" value="<?php echo htmlspecialchars($edit_item['date_lost'] ?? ''); ?>">

    <label>Item Type</label>
    <input type="text" name="item_type" value="<?php echo htmlspecialchars($edit_item['item_type'] ?? ''); ?>" required>

    <label>Status</label>
    <select name="status">
      <?php
      $statuses = ['Lost','Claimed'];
      $current = $edit_item['status'] ?? 'lost';
      foreach ($statuses as $s) {
          $sel = ($s === $current) ? 'selected' : '';
          echo "<option value=\"$s\" $sel>$s</option>";
      }
      ?>
    </select>

    <label>Image (jpg/png/gif) - leave blank to keep existing</label>
    <input type="file" name="image" accept="image/*">

    <button type="submit"><?php echo $edit_item ? 'Update Item' : 'Add Item'; ?></button>
    <?php if ($edit_item): ?><a href="manage_items.php"><button type="button">Cancel</button></a><?php endif; ?>
  </form>

  <hr>

  <h3>All Items</h3>
  <?php if ($result && mysqli_num_rows($result) > 0): ?>
    <table border="1" cellpadding="6" cellspacing="0" width="100%">
      <tr><th>ID</th><th>Image</th><th>Description</th><th>Type</th><th>Status</th><th>Posted by</th><th>Claimed by</th><th>Actions</th></tr>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?php echo (int)$row['id']; ?></td>
          <td><?php if ($row['image']) echo '<img src="uploads/' . htmlspecialchars($row['image']) . '" width="80">'; ?></td>
          <td><?php echo htmlspecialchars($row['description']); ?></td>
          <td><?php echo htmlspecialchars($row['item_type']); ?></td>
          <td><?php echo htmlspecialchars($row['status']); ?></td>
          <td><?php echo htmlspecialchars($row['posted_by_name'] ?? '—'); ?></td>
          <td><?php echo htmlspecialchars($row['claimer_name'] ?? '—'); ?></td>
          <td>
            <a href="manage_items.php?edit=<?php echo (int)$row['id']; ?>">Edit</a> |
            <a href="delete_item.php?id=<?php echo (int)$row['id']; ?>" onclick="return confirm('Delete this item?');">Delete</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <p>No items yet.</p>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
