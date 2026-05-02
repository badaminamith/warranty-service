<?php
include 'session.php';
include 'db.php';

if (isset($_POST['add'])) {
    $c = (int)$_POST['customer_id'];
    $p = (int)$_POST['product_id'];
    $d = $conn->real_escape_string($_POST['purchase_date']);
    $conn->query("INSERT INTO purchase(customer_id,product_id,purchase_date) VALUES($c,$p,'$d')");
}
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM purchase WHERE id=".(int)$_GET['delete']);
}

$rows      = $conn->query("SELECT p.*, c.name AS customer_name, pr.product_name FROM purchase p LEFT JOIN customer c ON p.customer_id=c.id LEFT JOIN product pr ON p.product_id=pr.id ORDER BY p.id DESC");
$customers = $conn->query("SELECT id,name FROM customer ORDER BY name");
$products  = $conn->query("SELECT id,product_name FROM product ORDER BY product_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Purchases — NBCareDesk</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main">
  <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:12px">
    <div class="page-header" style="margin:0"><h1>Purchases</h1><p>Track customer product purchases</p></div>
    <button class="btn btn-primary" onclick="openModal('addModal')">+ Add Purchase</button>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table>
        <thead><tr><th>#</th><th>Customer</th><th>Product</th><th>Purchase Date</th><th style="text-align:right">Actions</th></tr></thead>
        <tbody>
          <?php if ($rows->num_rows === 0): ?>
            <tr class="empty-row"><td colspan="5">No purchases found.</td></tr>
          <?php else: while ($r = $rows->fetch_assoc()): ?>
          <tr>
            <td><?= $r['id'] ?></td>
            <td>👤 <?= htmlspecialchars($r['customer_name'] ?? 'N/A') ?></td>
            <td>📦 <?= htmlspecialchars($r['product_name'] ?? 'N/A') ?></td>
            <td><?= date('M d, Y', strtotime($r['purchase_date'])) ?></td>
            <td style="text-align:right">
              <a href="?delete=<?= $r['id'] ?>" class="btn btn-sm btn-delete" onclick="return confirm('Delete this purchase?')">🗑️ Delete</a>
            </td>
          </tr>
          <?php endwhile; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="modal-overlay" id="addModal">
  <div class="modal">
    <div class="modal-header"><div class="modal-title">Add New Purchase</div>
      <button class="modal-close" onclick="closeModal('addModal')">✕</button></div>
    <form method="post">
      <div class="form-group"><label>Customer *</label>
        <select name="customer_id" class="form-control" required>
          <option value="">-- Select Customer --</option>
          <?php $customers->data_seek(0); while ($c = $customers->fetch_assoc()): ?>
          <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="form-group"><label>Product *</label>
        <select name="product_id" class="form-control" required>
          <option value="">-- Select Product --</option>
          <?php $products->data_seek(0); while ($p = $products->fetch_assoc()): ?>
          <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['product_name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="form-group"><label>Purchase Date *</label>
        <input name="purchase_date" type="date" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
      <div class="form-footer">
        <button type="button" class="btn-cancel" onclick="closeModal('addModal')">Cancel</button>
        <button type="submit" name="add" class="btn btn-primary">Save Purchase</button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
window.onclick = e => { if (e.target.classList.contains('modal-overlay')) e.target.classList.remove('open'); }
</script>
</body>
</html>