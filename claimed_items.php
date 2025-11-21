<?php
include 'includes/header.php';
include 'db.php';

$sql = "SELECT lost_items.*, m.fullname AS claimer
        FROM lost_items
        LEFT JOIN members m ON lost_items.claimed_by = m.id
        WHERE lost_items.status IN ('claimed','returned')
        ORDER BY lost_items.claim_date DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="container">
  <h1>Claimed / Returned Items</h1>

  <?php if ($result && mysqli_num_rows($result) > 0): ?>
    <div class="gallery">
      <?php while ($item = mysqli_fetch_assoc($result)): ?>
        <div class="item">
          <?php if ($item['image']): ?>
            <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="Claimed item">
          <?php endif; ?>
          <p><strong>Item ID:</strong> <?php echo (int)$item['id']; ?></p>
          <p><strong>Description:</strong> <?php echo htmlspecialchars($item['description']); ?></p>
          <p><strong>Date Lost:</strong> <?php echo htmlspecialchars($item['date_lost']); ?></p>
          <p><strong>Type:</strong> <?php echo htmlspecialchars($item['item_type']); ?></p>
          <p><strong>Status:</strong> <?php echo htmlspecialchars($item['status']); ?></p>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <p>No claimed items found.</p>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
