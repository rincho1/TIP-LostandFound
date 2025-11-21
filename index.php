<?php
include 'includes/header.php';
include 'db.php';

$type = trim($_GET['item_type'] ?? '');
$date_lost = trim($_GET['date_lost'] ?? '');
$q = trim($_GET['q'] ?? '');

$where = "WHERE lost_items.status = 'lost'";
$params = [];

if ($type !== '') {
    $safe = mysqli_real_escape_string($conn, $type);
    $where .= " AND lost_items.item_type LIKE '%$safe%'";
}
if ($date_lost !== '') {
    $safe = mysqli_real_escape_string($conn, $date_lost);
    $where .= " AND lost_items.date_lost = '$safe'";
}
if ($q !== '') {
    $safe = mysqli_real_escape_string($conn, $q);
    $where .= " AND (lost_items.description LIKE '%$safe%' OR lost_items.item_type LIKE '%$safe%')";
}

$sql = "SELECT lost_items.*, members.fullname AS posted_by
        FROM lost_items
        JOIN members ON lost_items.user_id = members.id
        $where
        ORDER BY lost_items.created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="container">
  <h1>Lost Items</h1>

  <form method="GET" class="filters">
    <label>Search</label>
    <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="keyword">

    <label>Item Type</label>
    <input type="text" name="item_type" value="<?php echo htmlspecialchars($type); ?>" placeholder="e.g. Phone">

    <label>Date Lost</label>
    <input type="date" name="date_lost" value="<?php echo htmlspecialchars($date_lost); ?>">

    <button type="submit">Apply</button>
    <a href="index.php" class="button">Clear</a>
  </form>

  <?php if ($result && mysqli_num_rows($result) > 0): ?>
    <div class="gallery">
      <?php while ($item = mysqli_fetch_assoc($result)): ?>
        <div class="item">
          <?php if ($item['image']): ?>
            <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="Item Image" width="200">
          <?php endif; ?>
          <p><strong>Item ID:</strong> <?php echo (int)$item['id']; ?></p>
          <p><strong>Type:</strong> <?php echo htmlspecialchars($item['item_type']); ?></p>
          <p><strong>Posted by:</strong> <?php echo htmlspecialchars($item['posted_by']); ?></p>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p>No lost items available.</p>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
