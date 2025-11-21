<?php
include 'includes/header.php';
include 'db.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: signin.php");
    exit;
}

$report_type = $_GET['report_type'] ?? 'all_items';
$status_filter = $_GET['status'] ?? '';
$date_from = $_GET['date_from'] ?? '';
$date_to = $_GET['date_to'] ?? '';
$item_type_filter = $_GET['item_type'] ?? '';

$where = "WHERE 1=1";

if ($report_type === 'lost_items') {
    $where .= " AND lost_items.status = 'lost'";
} elseif ($report_type === 'claimed_items') {
    $where .= " AND lost_items.status IN ('claimed', 'returned')";
}

if ($status_filter) {
    $safe_status = mysqli_real_escape_string($conn, $status_filter);
    $where .= " AND lost_items.status = '$safe_status'";
}

if ($date_from) {
    $safe_from = mysqli_real_escape_string($conn, $date_from);
    $where .= " AND lost_items.date_lost >= '$safe_from'";
}

if ($date_to) {
    $safe_to = mysqli_real_escape_string($conn, $date_to);
    $where .= " AND lost_items.date_lost <= '$safe_to'";
}

if ($item_type_filter) {
    $safe_type = mysqli_real_escape_string($conn, $item_type_filter);
    $where .= " AND lost_items.item_type LIKE '%$safe_type%'";
}

$sql = "SELECT lost_items.*, 
        m1.fullname AS posted_by_name, 
        m2.fullname AS claimer_name
        FROM lost_items
        LEFT JOIN members m1 ON lost_items.user_id = m1.id
        LEFT JOIN members m2 ON lost_items.claimed_by = m2.id
        $where
        ORDER BY lost_items.created_at DESC";

$result = mysqli_query($conn, $sql);
$items = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $items[] = $row;
    }
}

$count_items = count($items);
$count_lost = 0;
$count_claimed = 0;
foreach ($items as $item) {
    if ($item['status'] === 'lost') $count_lost++;
    if (in_array($item['status'], ['claimed', 'returned'])) $count_claimed++;
}
?>

<div class="container">
  <h1>Reports & Export</h1>
  
  <form method="GET" class="filters">
    <label>Report Type</label>
    <select name="report_type">
      <option value="all_items" <?php echo $report_type === 'all_items' ? 'selected' : ''; ?>>All Items</option>
      <option value="lost_items" <?php echo $report_type === 'lost_items' ? 'selected' : ''; ?>>Lost Items Only</option>
      <option value="claimed_items" <?php echo $report_type === 'claimed_items' ? 'selected' : ''; ?>>Claimed Items Only</option>
    </select>

    <label>Status</label>
    <select name="status">
      <option value="">All Statuses</option>
      <option value="lost" <?php echo $status_filter === 'lost' ? 'selected' : ''; ?>>Lost</option>
      <option value="claimed" <?php echo $status_filter === 'claimed' ? 'selected' : ''; ?>>Claimed</option>
      <option value="returned" <?php echo $status_filter === 'returned' ? 'selected' : ''; ?>>Returned</option>
    </select>

    <label>Item Type</label>
    <input type="text" name="item_type" value="<?php echo htmlspecialchars($item_type_filter); ?>" placeholder="e.g. Phone">

    <label>Date From</label>
    <input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">

    <label>Date To</label>
    <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">

    <button type="submit">Apply Filters</button>
    <a href="reports.php" class="button">Clear</a>
  </form>

  <hr>

  <div style="margin: 20px 0;">
    <h3>Summary</h3>
    <p><strong>Total Items:</strong> <?php echo $count_items; ?></p>
    <p><strong>Lost:</strong> <?php echo $count_lost; ?></p>
    <p><strong>Claimed/Returned:</strong> <?php echo $count_claimed; ?></p>
  </div>

  <div style="margin: 20px 0; display: flex; gap: 10px;">
    <form action="export_pdf.php" method="POST" target="_blank">
      <input type="hidden" name="report_type" value="<?php echo htmlspecialchars($report_type); ?>">
      <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
      <input type="hidden" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>">
      <input type="hidden" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>">
      <input type="hidden" name="item_type" value="<?php echo htmlspecialchars($item_type_filter); ?>">
      <button type="submit" style="background-color: #e74c3c; color: white;">
        <i class="fas fa-file-pdf"></i> Generate PDF Report
      </button>
    </form>

    <button onclick="copyToClipboard()" style="background-color: #3498db; color: white;">
      <i class="fas fa-copy"></i> Copy to Clipboard
    </button>

    <button onclick="window.print()" style="background-color: #27ae60; color: white;">
      <i class="fas fa-print"></i> Print Preview
    </button>
  </div>

  <hr>

  <div id="reportData">
    <h3>Report Data</h3>
    <?php if ($count_items > 0): ?>
      <table border="1" cellpadding="8" cellspacing="0" width="100%" id="dataTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Description</th>
            <th>Item Type</th>
            <th>Date Lost</th>
            <th>Status</th>
            <th>Posted By</th>
            <th>Claimed By</th>
            <th>Claim Date</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item): ?>
            <tr>
              <td><?php echo (int)$item['id']; ?></td>
              <td><?php echo htmlspecialchars($item['description']); ?></td>
              <td><?php echo htmlspecialchars($item['item_type']); ?></td>
              <td><?php echo htmlspecialchars($item['date_lost'] ?? '—'); ?></td>
              <td><?php echo htmlspecialchars($item['status']); ?></td>
              <td><?php echo htmlspecialchars($item['posted_by_name'] ?? '—'); ?></td>
              <td><?php echo htmlspecialchars($item['claimer_name'] ?? '—'); ?></td>
              <td><?php echo htmlspecialchars($item['claim_date'] ?? '—'); ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <p>No items found matching the selected criteria.</p>
    <?php endif; ?>
  </div>
</div>

<script>
function copyToClipboard() {
    const table = document.getElementById('dataTable');
    if (!table) {
        alert('No data to copy!');
        return;
    }

    let text = 'TIP LOST & FOUND SYSTEM - REPORT\n';
    text += 'Generated: ' + new Date().toLocaleString() + '\n\n';
    text += 'Total Items: <?php echo $count_items; ?>\n';
    text += 'Lost: <?php echo $count_lost; ?>\n';
    text += 'Claimed/Returned: <?php echo $count_claimed; ?>\n\n';
    text += '---\n\n';

    const rows = table.querySelectorAll('tr');
    rows.forEach(row => {
        const cells = row.querySelectorAll('th, td');
        const rowText = Array.from(cells).map(cell => cell.textContent.trim()).join('\t');
        text += rowText + '\n';
    });

    navigator.clipboard.writeText(text).then(() => {
        alert('Report data copied to clipboard!');
    }).catch(err => {
        alert('Failed to copy: ' + err);
    });
}
</script>

<style>
@media print {
    .sidebar, .hamburger, form, button, .filters, hr {
        display: none !important;
    }
    .content {
        margin-left: 0 !important;
    }
    table {
        width: 100%;
        font-size: 12px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>