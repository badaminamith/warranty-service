<?php
include 'session.php';
include 'db.php';

// Add
if (isset($_POST['add'])) {
    $n = $conn->real_escape_string($_POST['name']);
    $p = $conn->real_escape_string($_POST['phone']);
    $e = $conn->real_escape_string($_POST['email']);
    $a = $conn->real_escape_string($_POST['address']);
    $conn->query("INSERT INTO customer(name,phone,email,address) VALUES('$n','$p','$e','$a')");
}
// Edit
if (isset($_POST['edit'])) {
    $id = (int)$_POST['id'];
    $n  = $conn->real_escape_string($_POST['name']);
    $p  = $conn->real_escape_string($_POST['phone']);
    $e  = $conn->real_escape_string($_POST['email']);
    $a  = $conn->real_escape_string($_POST['address']);
    $conn->query("UPDATE customer SET name='$n',phone='$p',email='$e',address='$a' WHERE id=$id");
}
// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM customer WHERE id=$id");
}

$search = $conn->real_escape_string($_GET['search'] ?? '');
$where  = $search ? "WHERE name LIKE '%$search%' OR phone LIKE '%$search%'" : '';
$rows   = $conn->query("SELECT * FROM customer $where ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customers — NBCareDesk</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main">
  <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:12px">
    <div class="page-header" style="margin:0">
      <h1>Customers</h1>
      <p>Manage your customer database</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('addModal')">+ Add Customer</button>
  </div>

  <div class="card">
    <div class="card-toolbar">
      <form method="get" style="display:inline">
        <div class="search-box">
          <input name="search" type="text" placeholder="Search customers..." value="<?= htmlspecialchars($search) ?>">
        </div>
      </form>
    </div>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Name</th><th>Phone</th><th>Email</th><th>Address</th><th style="text-align:right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($rows->num_rows === 0): ?>
            <tr class="empty-row"><td colspan="5">No customers found.</td></tr>
          <?php else: while ($r = $rows->fetch_assoc()): ?>
          <tr>
            <td><strong><?= htmlspecialchars($r['name']) ?></strong><br><small style="color:#94a3b8">ID #<?= $r['id'] ?></small></td>
            <td><?= htmlspecialchars($r['phone']) ?></td>
            <td><?= htmlspecialchars($r['email'] ?? '—') ?></td>
            <td><?= htmlspecialchars($r['address'] ?? '—') ?></td>
            <td style="text-align:right">
              <button class="btn btn-sm btn-edit" onclick="openEdit(<?= $r['id'] ?>, '<?= addslashes($r['name']) ?>', '<?= addslashes($r['phone']) ?>', '<?= addslashes($r['email']) ?>', '<?= addslashes($r['address']) ?>')">✏️ Edit</button>
              <a href="?delete=<?= $r['id'] ?><?= $search ? '&search='.urlencode($search) : '' ?>"
                 class="btn btn-sm btn-delete" onclick="return confirm('Delete this customer?')">🗑️ Delete</a>
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
      <div class="modal-title">Add New Customer</div>
      <button class="modal-close" onclick="closeModal('addModal')">✕</button>
    </div>
    <form method="post">
      <div class="form-group"><label>Full Name *</label>
        <input name="name" class="form-control" placeholder="John Doe" required></div>
      <div class="form-group"><label>Phone Number *</label>
        <input name="phone" class="form-control" placeholder="+1 (555) 000-0000" required></div>
      <div class="form-group"><label>Email Address</label>
        <input name="email" type="email" class="form-control" placeholder="john@example.com"></div>
      <div class="form-group"><label>Physical Address</label>
        <textarea name="address" class="form-control" placeholder="123 Main St, City"></textarea></div>
      <div class="form-footer">
        <button type="button" class="btn-cancel" onclick="closeModal('addModal')">Cancel</button>
        <button type="submit" name="add" class="btn btn-primary">Save Customer</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Edit Customer</div>
      <button class="modal-close" onclick="closeModal('editModal')">✕</button>
    </div>
    <form method="post">
      <input type="hidden" name="id" id="edit_id">
      <div class="form-group"><label>Full Name *</label>
        <input name="name" id="edit_name" class="form-control" required></div>
      <div class="form-group"><label>Phone Number *</label>
        <input name="phone" id="edit_phone" class="form-control" required></div>
      <div class="form-group"><label>Email Address</label>
        <input name="email" id="edit_email" type="email" class="form-control"></div>
      <div class="form-group"><label>Physical Address</label>
        <textarea name="address" id="edit_address" class="form-control"></textarea></div>
      <div class="form-footer">
        <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Cancel</button>
        <button type="submit" name="edit" class="btn btn-primary">Update Customer</button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
function openEdit(id, name, phone, email, address) {
  document.getElementById('edit_id').value = id;
  document.getElementById('edit_name').value = name;
  document.getElementById('edit_phone').value = phone;
  document.getElementById('edit_email').value = email;
  document.getElementById('edit_address').value = address;
  openModal('editModal');
}
window.onclick = function(e) {
  if (e.target.classList.contains('modal-overlay')) e.target.classList.remove('open');
}
</script>
</body>
</html>