<?php
include 'session.php';
include 'db.php';

if (isset($_POST['add'])) {
    $n = $conn->real_escape_string($_POST['center_name']);
    $l = $conn->real_escape_string($_POST['location']);
    $conn->query("INSERT INTO service_center(center_name,location) VALUES('$n','$l')");
}
if (isset($_POST['edit'])) {
    $id = (int)$_POST['id'];
    $n  = $conn->real_escape_string($_POST['center_name']);
    $l  = $conn->real_escape_string($_POST['location']);
    $conn->query("UPDATE service_center SET center_name='$n',location='$l' WHERE id=$id");
}
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM service_center WHERE id=".(int)$_GET['delete']);
}

$rows = $conn->query("SELECT * FROM service_center ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Service Centers — NBCareDesk</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main">

  <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:12px">
    <div class="page-header" style="margin:0">
      <h1>Service Centers</h1>
      <p>Manage repair and service locations</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('addModal')">+ Add Center</button>
  </div>

  <div class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>#</th>
            <th>Center Name</th>
            <th>Location</th>
            <th style="text-align:right">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($rows->num_rows === 0): ?>
            <tr class="empty-row">
              <td colspan="4">No service centers found. Add your first center above.</td>
            </tr>
          <?php else: while ($r = $rows->fetch_assoc()): ?>
          <tr>
            <td><?= $r['id'] ?></td>
            <td>
              <strong>📍 <?= htmlspecialchars($r['center_name']) ?></strong>
              <br><small style="color:#94a3b8">ID #<?= $r['id'] ?></small>
            </td>
            <td><?= htmlspecialchars($r['location']) ?></td>
            <td style="text-align:right">
              <button class="btn btn-sm btn-edit"
                onclick="openEdit(
                  <?= $r['id'] ?>,
                  '<?= addslashes($r['center_name']) ?>',
                  '<?= addslashes($r['location']) ?>'
                )">
                ✏️ Edit
              </button>
              <a href="?delete=<?= $r['id'] ?>"
                 class="btn btn-sm btn-delete"
                 onclick="return confirm('Delete this service center?')">
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
      <div class="modal-title">Add Service Center</div>
      <button class="modal-close" onclick="closeModal('addModal')">✕</button>
    </div>
    <form method="post">
      <div class="form-group">
        <label>Center Name *</label>
        <input name="center_name" class="form-control"
               placeholder="e.g. Downtown Repair Hub" required>
      </div>
      <div class="form-group">
        <label>Location *</label>
        <input name="location" class="form-control"
               placeholder="e.g. Mumbai, Maharashtra" required>
      </div>
      <div class="form-footer">
        <button type="button" class="btn-cancel" onclick="closeModal('addModal')">Cancel</button>
        <button type="submit" name="add" class="btn btn-primary">Save Center</button>
      </div>
    </form>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal-overlay" id="editModal">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Edit Service Center</div>
      <button class="modal-close" onclick="closeModal('editModal')">✕</button>
    </div>
    <form method="post">
      <input type="hidden" name="id" id="edit_id">
      <div class="form-group">
        <label>Center Name *</label>
        <input name="center_name" id="edit_name" class="form-control" required>
      </div>
      <div class="form-group">
        <label>Location *</label>
        <input name="location" id="edit_location" class="form-control" required>
      </div>
      <div class="form-footer">
        <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Cancel</button>
        <button type="submit" name="edit" class="btn btn-primary">Update Center</button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
function openEdit(id, name, location) {
  document.getElementById('edit_id').value       = id;
  document.getElementById('edit_name').value     = name;
  document.getElementById('edit_location').value = location;
  openModal('editModal');
}
window.onclick = function(e) {
  if (e.target.classList.contains('modal-overlay')) e.target.classList.remove('open');
}
</script>
</body>
</html>