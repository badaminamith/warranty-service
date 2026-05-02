<?php
include 'session.php';
include 'db.php';

if (isset($_POST['add'])) {
    $n = $conn->real_escape_string($_POST['product_name']);
    $b = $conn->real_escape_string($_POST['brand']);
    $p = (int)$_POST['price'];
    $conn->query("INSERT INTO product(product_name,brand,price) VALUES('$n','$b',$p)");
}
if (isset($_POST['edit'])) {
    $id = (int)$_POST['id'];
    $n  = $conn->real_escape_string($_POST['product_name']);
    $b  = $conn->real_escape_string($_POST['brand']);
    $p  = (int)$_POST['price'];
    $conn->query("UPDATE product SET product_name='$n',brand='$b',price=$p WHERE id=$id");
}
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM product WHERE id=".(int)$_GET['delete']);
}

$search = $conn->real_escape_string($_GET['search'] ?? '');
$where  = $search ? "WHERE product_name LIKE '%$search%' OR brand LIKE '%$search%'" : '';
$rows   = $conn->query("SELECT * FROM product $where ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Products — NBCareDesk</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main">
  <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:12px">
    <div class="page-header" style="margin:0">
      <h1>Inventory</h1><p>Manage products and pricing</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('addModal')">+ Add Product</button>
  </div>

  <div class="card">
    <div class="card-toolbar">
      <form method="get">
        <div class="search-box">
          <input name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
        </div>
      </form>
    </div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Product</th><th>Brand</th><th>Price</th><th style="text-align:right">Actions</th></tr></thead>
        <tbody>
          <?php if ($rows->num_rows === 0): ?>
            <tr class="empty-row"><td colspan="4">No products found.</td></tr>
          <?php else: while ($r = $rows->fetch_assoc()): ?>
          <tr>
            <td>📦 <strong><?= htmlspecialchars($r['product_name']) ?></strong><br><small style="color:#94a3b8">ID #<?= $r['id'] ?></small></td>
            <td><span class="badge"><?= htmlspecialchars($r['brand']) ?></span></td>
            <td><strong style="color:#059669">₹<?= number_format($r['price']) ?></strong></td>
            <td style="text-align:right">
              <button class="btn btn-sm btn-edit" onclick="openEdit(<?= $r['id'] ?>, '<?= addslashes($r['product_name']) ?>', '<?= addslashes($r['brand']) ?>', <?= $r['price'] ?>)">✏️ Edit</button>
              <a href="?delete=<?= $r['id'] ?>" class="btn btn-sm btn-delete" onclick="return confirm('Delete this product?')">🗑️ Delete</a>
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
    <div class="modal-header"><div class="modal-title">Add New Product</div>
      <button class="modal-close" onclick="closeModal('addModal')">✕</button></div>
    <form method="post">
      <div class="form-group"><label>Product Name *</label>
        <input name="product_name" class="form-control" placeholder="e.g. ThinkPad X1 Carbon" required></div>
      <div class="form-group"><label>Brand *</label>
        <input name="brand" class="form-control" placeholder="e.g. Lenovo" required></div>
      <div class="form-group"><label>Price (₹) *</label>
        <input name="price" type="number" min="0" class="form-control" placeholder="1500" required></div>
      <div class="form-footer">
        <button type="button" class="btn-cancel" onclick="closeModal('addModal')">Cancel</button>
        <button type="submit" name="add" class="btn btn-primary">Save Product</button>
      </div>
    </form>
  </div>
</div>

<div class="modal-overlay" id="editModal">
  <div class="modal">
    <div class="modal-header"><div class="modal-title">Edit Product</div>
      <button class="modal-close" onclick="closeModal('editModal')">✕</button></div>
    <form method="post">
      <input type="hidden" name="id" id="edit_id">
      <div class="form-group"><label>Product Name *</label>
        <input name="product_name" id="edit_name" class="form-control" required></div>
      <div class="form-group"><label>Brand *</label>
        <input name="brand" id="edit_brand" class="form-control" required></div>
      <div class="form-group"><label>Price (USD) *</label>
        <input name="price" id="edit_price" type="number" min="0" class="form-control" required></div>
      <div class="form-footer">
        <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Cancel</button>
        <button type="submit" name="edit" class="btn btn-primary">Update Product</button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
function openEdit(id, name, brand, price) {
  document.getElementById('edit_id').value = id;
  document.getElementById('edit_name').value = name;
  document.getElementById('edit_brand').value = brand;
  document.getElementById('edit_price').value = price;
  openModal('editModal');
}
window.onclick = e => { if (e.target.classList.contains('modal-overlay')) e.target.classList.remove('open'); }
</script>
</body>
</html>