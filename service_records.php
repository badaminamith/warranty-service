<?php
include 'session.php';
include 'db.php';

if (isset($_POST['add'])) {
    $pid = (int)$_POST['purchase_id'];
    $cid = (int)$_POST['center_id'];
    $d   = $conn->real_escape_string($_POST['service_date']);
    $pr  = $conn->real_escape_string($_POST['problem']);
    $conn->query("INSERT INTO service_record(purchase_id,center_id,service_date,problem) VALUES($pid,$cid,'$d','$pr')");
}
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM service_record WHERE id=".(int)$_GET['delete']);
}

$rows = $conn->query("
    SELECT sr.*, 
           c.name AS customer_name, 
           pr.product_name, 
           sc.center_name 
    FROM service_record sr 
    LEFT JOIN purchase p  ON sr.purchase_id = p.id 
    LEFT JOIN customer c  ON p.customer_id  = c.id 
    LEFT JOIN product pr  ON p.product_id   = pr.id 
    LEFT JOIN service_center sc ON sr.center_id = sc.id 
    ORDER BY sr.id DESC
");

$purchases = $conn->query("
    SELECT p.id, c.name AS cname, pr.product_name 
    FROM purchase p 
    LEFT JOIN customer c  ON p.customer_id = c.id 
    LEFT JOIN product pr  ON p.product_id  = pr.id 
    ORDER BY p.id DESC
");

$centers = $conn->query("SELECT * FROM service_center ORDER BY center_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Service Records — NBCareDesk</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main">

  <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:12px">
    <div class="page-header" style="margin:0">
      <h1>Service Records</h1>
      <p>Log and track all service requests</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('addModal')">+ Add Record</button>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Customer</th>
            <th>Product</th>
            <th>Service Center</th>
            <th>Service Date</th>
            <th>Problem</th>
            <th style="text-align:right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($rows->num_rows === 0): ?>
            <tr class="empty-row">
              <td colspan="7">No service records found. Add your first record above.</td>
            </tr>
          <?php else: while ($r = $rows->fetch_assoc()): ?>
          <tr>
            <td><?= $r['id'] ?></td>
            <td>👤 <strong><?= htmlspecialchars($r['customer_name'] ?? 'N/A') ?></strong></td>
            <td>📦 <?= htmlspecialchars($r['product_name'] ?? 'N/A') ?></td>
            <td>📍 <?= htmlspecialchars($r['center_name'] ?? 'N/A') ?></td>
            <td><?= date('d M Y', strtotime($r['service_date'])) ?></td>
            <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
                title="<?= htmlspecialchars($r['problem']) ?>">
              <?= htmlspecialchars($r['problem']) ?>
            </td>
            <td style="text-align:right">
              <a href="?delete=<?= $r['id'] ?>"
                 class="btn btn-sm btn-delete"
                 onclick="return confirm('Delete this service record?')">
                🗑️ Delete
              </a>
            </td>
          </tr>
          <?php endwhile; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- Add Modal -->
<div class="modal-overlay" id="addModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Add Service Record</div>
      <button class="modal-close" onclick="closeModal('addModal')">✕</button>
    </div>
    <form method="post">

      <div class="form-group">
        <label>Purchase *</label>
        <select name="purchase_id" class="form-control" required>
          <option value="">-- Select Purchase --</option>
          <?php while ($p = $purchases->fetch_assoc()): ?>
          <option value="<?= $p['id'] ?>">
            #<?= $p['id'] ?> — <?= htmlspecialchars($p['cname'] ?? 'Unknown') ?> /
            <?= htmlspecialchars($p['product_name'] ?? 'Unknown') ?>
          </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="form-group">
        <label>Service Center *</label>
        <select name="center_id" class="form-control" required>
          <option value="">-- Select Service Center --</option>
          <?php while ($c = $centers->fetch_assoc()): ?>
          <option value="<?= $c['id'] ?>">
            <?= htmlspecialchars($c['center_name']) ?> — <?= htmlspecialchars($c['location']) ?>
          </option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="form-group">
        <label>Service Date *</label>
        <input name="service_date" type="date" class="form-control"
               value="<?= date('Y-m-d') ?>" required>
      </div>

      <div class="form-group">
        <label>Problem Description *</label>
        <textarea name="problem" class="form-control"
                  placeholder="Describe the issue in detail..." required></textarea>
      </div>

      <div class="form-footer">
        <button type="button" class="btn-cancel" onclick="closeModal('addModal')">Cancel</button>
        <button type="submit" name="add" class="btn btn-primary">Save Record</button>
      </div>

    </form>
  </div>
</div>

<script>
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
window.onclick = function(e) {
  if (e.target.classList.contains('modal-overlay')) e.target.classList.remove('open');
}
</script>
</body>
</html>