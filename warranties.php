<?php
include 'session.php';
include 'db.php';

$success = '';
$error   = '';
$showForm = isset($_GET['add']);

// Add warranty
if (isset($_POST['add'])) {
    $pid = (int)$_POST['purchase_id'];
    $m   = (int)$_POST['warranty_months'];
    if ($pid > 0 && $m > 0) {
        if ($conn->query("INSERT INTO warranty(purchase_id,warranty_months) VALUES($pid,$m)")) {
            $success  = 'Warranty added successfully!';
            $showForm = false;
        } else {
            $error = 'DB Error: ' . $conn->error;
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}

// Delete
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM warranty WHERE id=" . (int)$_GET['delete']);
    $success = 'Warranty deleted.';
}

// Fetch warranties
$rows = $conn->query("
    SELECT w.id, w.warranty_months, p.purchase_date,
           c.name AS customer_name,
           pr.product_name
    FROM warranty w
    LEFT JOIN purchase p  ON w.purchase_id = p.id
    LEFT JOIN customer c  ON p.customer_id = c.id
    LEFT JOIN product pr  ON p.product_id  = pr.id
    ORDER BY w.id DESC
");

// Fetch purchases for dropdown
$purchases = $conn->query("
    SELECT p.id, c.name AS cname, pr.product_name
    FROM purchase p
    LEFT JOIN customer c  ON p.customer_id = c.id
    LEFT JOIN product pr  ON p.product_id  = pr.id
    ORDER BY p.id DESC
");
$hasPurchases = ($purchases && $purchases->num_rows > 0);

// Search
$search = $_GET['search'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Warranties — NBCareDesk</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    .alert { padding:12px 16px; border-radius:10px; font-size:14px; font-weight:600; margin-bottom:20px; }
    .alert-success { background:#f0fdf4; border:1px solid #86efac; color:#16a34a; }
    .alert-error   { background:#fff1f2; border:1px solid #fecdd3; color:#ef4444; }
    .alert-warning { background:#fffbeb; border:1px solid #fde68a; color:#d97706; }

    /* Inline form card */
    .inline-form-card {
      background:#fff; border-radius:16px;
      border:1px solid #e2e8f0; padding:28px;
      margin-bottom:24px;
      box-shadow:0 1px 3px rgba(0,0,0,.05);
    }
    .inline-form-card h3 {
      font-size:17px; font-weight:700; color:#0f172a;
      margin-bottom:20px; padding-bottom:14px;
      border-bottom:1px solid #f1f5f9;
      display:flex; align-items:center; gap:8px;
    }
    .form-row { display:flex; gap:16px; flex-wrap:wrap; }
    .form-row .form-group { flex:1; min-width:220px; }
    .btn-cancel-link {
      display:inline-flex; align-items:center; gap:6px;
      padding:9px 18px; border-radius:10px;
      background:#f1f5f9; color:#475569;
      font-size:14px; font-weight:600;
      text-decoration:none; margin-right:8px;
      transition:background 0.2s;
    }
    .btn-cancel-link:hover { background:#e2e8f0; }

    /* Warranty ID cell */
    .warranty-id {
      display:flex; align-items:center; gap:10px;
    }
    .warranty-id-circle {
      width:32px; height:32px; border-radius:50%;
      border:2px solid #3b82f6;
      display:flex; align-items:center; justify-content:center;
      color:#3b82f6; font-size:12px; font-weight:700;
      flex-shrink:0;
    }
    .warranty-id-text { font-weight:600; color:#1e293b; font-size:14px; }

    /* Purchase details cell */
    .purchase-product { font-weight:700; color:#0f172a; font-size:15px; margin-bottom:2px; }
    .purchase-meta    { font-size:12px; color:#64748b; line-height:1.6; }

    /* Duration cell */
    .duration-cell {
      display:flex; align-items:center; gap:7px;
      color:#d97706; font-weight:600; font-size:14px;
    }
    .duration-icon { font-size:16px; }

    /* Status badge */
    .badge-active {
      display:inline-block; padding:4px 12px;
      background:#dcfce7; color:#16a34a;
      border-radius:20px; font-size:12px; font-weight:700;
      letter-spacing:0.05em; text-transform:uppercase;
      margin-bottom:4px;
    }
    .badge-expired {
      display:inline-block; padding:4px 12px;
      background:#fee2e2; color:#ef4444;
      border-radius:20px; font-size:12px; font-weight:700;
      letter-spacing:0.05em; text-transform:uppercase;
      margin-bottom:4px;
    }
    .expiry-date { font-size:12px; color:#64748b; }
    .expiry-date span { margin-right:4px; }
  </style>
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main">

  <!-- Page Header -->
  <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;flex-wrap:wrap;gap:12px">
    <div class="page-header" style="margin:0">
      <h1>Warranties</h1>
      <p>Manage product warranties and coverage periods</p>
    </div>
    <?php if (!$showForm && $hasPurchases): ?>
      <a href="?add=1" class="btn btn-primary">+ Add Warranty</a>
    <?php elseif (!$showForm): ?>
      <a href="purchases.php" class="btn btn-primary">→ Add Purchase First</a>
    <?php endif; ?>
  </div>

  <!-- Alerts -->
  <?php if ($success): ?>
    <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <?php if (!$hasPurchases && !$showForm): ?>
    <div class="alert alert-warning">
      ⚠️ No purchases found. Please add a <a href="purchases.php" style="color:#b45309;font-weight:700">Purchase</a> first.
      <br><small style="font-weight:400">Order: Customers → Products → Purchases → Warranties</small>
    </div>
  <?php endif; ?>

  <!-- Inline Add Form -->
  <?php if ($showForm && $hasPurchases): ?>
  <div class="inline-form-card">
    <h3>🛡️ Add New Warranty</h3>
    <form method="post" action="warranties.php">
      <div class="form-row">
        <div class="form-group">
          <label>Select Purchase *</label>
          <select name="purchase_id" class="form-control" required>
            <option value="">-- Select a Purchase --</option>
            <?php $purchases->data_seek(0); while ($p = $purchases->fetch_assoc()): ?>
            <option value="<?= $p['id'] ?>">
              #<?= $p['id'] ?> —
              <?= htmlspecialchars($p['product_name'] ?? 'Unknown Product') ?> /
              Owner: <?= htmlspecialchars($p['cname'] ?? 'Unknown') ?>
            </option>
            <?php endwhile; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Warranty Duration (months) *</label>
          <input name="warranty_months" type="number" min="1" max="120"
                 class="form-control" placeholder="e.g. 6 or 12 or 24" required>
          <small style="color:#64748b;font-size:12px;margin-top:4px;display:block">
            6 = 6 months &nbsp;|&nbsp; 12 = 1 Year &nbsp;|&nbsp; 24 = 2 Years
          </small>
        </div>
      </div>
      <div style="margin-top:20px">
        <a href="warranties.php" class="btn-cancel-link">✕ Cancel</a>
        <button type="submit" name="add" class="btn btn-primary">🛡️ Save Warranty</button>
      </div>
    </form>
  </div>
  <?php endif; ?>

  <!-- Search + Table Card -->
  <div class="card">
    <!-- Search Bar -->
    <div class="card-toolbar">
      <form method="get" style="width:100%">
        <div class="search-box" style="width:100%;max-width:400px">
          <input name="search" type="text" placeholder="Search by customer or product..."
                 value="<?= htmlspecialchars($search) ?>"
                 style="width:100%">
        </div>
      </form>
    </div>

    <!-- Table -->
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Warranty ID</th>
            <th>Purchase Details</th>
            <th>Duration</th>
            <th>Status / Expiry</th>
            <th style="text-align:right">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php
        if (!$rows) {
            echo '<tr class="empty-row"><td colspan="5">Database error: ' . $conn->error . '</td></tr>';
        } elseif ($rows->num_rows === 0) {
            echo '<tr class="empty-row"><td colspan="5">No warranties found. Click &quot;+ Add Warranty&quot; to get started.</td></tr>';
        } else {
            while ($r = $rows->fetch_assoc()) {
                // Filter by search
                $sname = strtolower($r['customer_name'] ?? '');
                $sprod = strtolower($r['product_name'] ?? '');
                $sq    = strtolower($search);
                if ($search && strpos($sname, $sq) === false && strpos($sprod, $sq) === false) continue;

                $expiry_ts   = strtotime($r['purchase_date'] . ' +' . $r['warranty_months'] . ' months');
                $expiry_date = date('M d, Y', $expiry_ts);
                $isActive    = $expiry_ts >= time();
                $purchase_d  = $r['purchase_date'] ? date('M d, Y', strtotime($r['purchase_date'])) : 'N/A';
                $months      = $r['warranty_months'];
                $dur_label   = $months >= 12
                    ? ($months % 12 === 0 ? ($months/12) . ' Year' . ($months/12 > 1 ? 's' : '') : $months . ' Months')
                    : $months . ' Month' . ($months > 1 ? 's' : '');

                $badge = $isActive
                    ? "<span class='badge-active'>Active</span>"
                    : "<span class='badge-expired'>Expired</span>";

                echo "<tr>
                  <td>
                    <div class='warranty-id'>
                      <div class='warranty-id-circle'>#{$r['id']}</div>
                      <div class='warranty-id-text'>Warranty #{$r['id']}</div>
                    </div>
                  </td>
                  <td>
                    <div class='purchase-product'>" . htmlspecialchars($r['product_name'] ?? 'N/A') . "</div>
                    <div class='purchase-meta'>
                      Owner: " . htmlspecialchars($r['customer_name'] ?? 'N/A') . "<br>
                      Purchased: {$purchase_d}
                    </div>
                  </td>
                  <td>
                    <div class='duration-cell'>
                      <span class='duration-icon'>🕐</span>
                      {$dur_label}
                    </div>
                  </td>
                  <td>
                    {$badge}
                    <div class='expiry-date'><span>📅</span>{$expiry_date}</div>
                  </td>
                  <td style='text-align:right'>
                    <a href='?delete={$r['id']}' class='btn btn-sm btn-delete'
                       onclick='return confirm(\"Delete this warranty?\")'>🗑️ Delete</a>
                  </td>
                </tr>";
            }
        }
        ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
</body>
</html>