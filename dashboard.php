<?php
echo __DIR__;
?>
<?php
include 'session.php';
include 'db.php';
$customers    = $conn->query("SELECT COUNT(*) AS c FROM customer")->fetch_assoc()['c'];
$products     = $conn->query("SELECT COUNT(*) AS c FROM product")->fetch_assoc()['c'];
$purchases    = $conn->query("SELECT COUNT(*) AS c FROM purchase")->fetch_assoc()['c'];
$warranties   = $conn->query("SELECT COUNT(*) AS c FROM warranty")->fetch_assoc()['c'];
$centers      = $conn->query("SELECT COUNT(*) AS c FROM service_center")->fetch_assoc()['c'];
$records      = $conn->query("SELECT COUNT(*) AS c FROM service_record")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard — NBCareDesk</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
<?php include 'sidebar.php'; ?>
<div class="main">
  <div class="page-header">
    <h1>Dashboard Overview</h1>
    <p>Welcome back. Here's what's happening today.</p>
  </div>

  <div class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon" style="background:#eff6ff">👥</div>
      <div><div class="stat-label">Total Customers</div><div class="stat-value"><?= $customers ?></div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:#eef2ff">📦</div>
      <div><div class="stat-label">Products Listed</div><div class="stat-value"><?= $products ?></div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:#f0fdf4">🛒</div>
      <div><div class="stat-label">Total Purchases</div><div class="stat-value"><?= $purchases ?></div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:#fffbeb">🛡️</div>
      <div><div class="stat-label">Active Warranties</div><div class="stat-value"><?= $warranties ?></div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:#fff7ed">📍</div>
      <div><div class="stat-label">Service Centers</div><div class="stat-value"><?= $centers ?></div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:#fff1f2">🔧</div>
      <div><div class="stat-label">Service Records</div><div class="stat-value"><?= $records ?></div></div>
    </div>
  </div>

  <div class="dashboard-banner">
    <h2>Ready to optimize your service center?</h2>
    <p>Navigate through the sidebar to manage customers, track active warranties, and log new service requests in real-time.</p>
  </div>
</div>
</body>
</html>