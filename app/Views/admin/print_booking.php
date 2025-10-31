<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Booking Report - <?= $print_all ? 'All Bookings' : 'Booking #' . ($booking['id'] ?? '') ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  body { font-family: Arial, sans-serif; margin: 20px; color: #333; }
  .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
  .header h1 { margin: 0; color: #06b6d4; }
  .header .subtitle { color: #666; margin: 5px 0; }
  .print-date { text-align: right; font-size: 14px; color: #666; }
  .table { width: 100%; border-collapse: collapse; margin: 20px 0; }
  .table th { background: #0f172a; color: white; padding: 12px; text-align: left; }
  .table td { padding: 10px; border: 1px solid #ddd; }
  .table tr:nth-child(even) { background: #f9f9f9; }
  .status-badge { padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: bold; }
  .status-pending { background: #fef3c7; color: #92400e; }
  .status-approved { background: #d1fae5; color: #065f46; }
  .status-cancelled { background: #fee2e2; color: #991b1b; }
  .booking-details { background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0; }
  .section-title { color: #06b6d4; margin: 25px 0 15px 0; border-bottom: 1px solid #eee; padding-bottom: 8px; }
  @media print {
    body { margin: 0; }
    .no-print { display: none; }
  }
</style>
</head>
<body>

<div class="header">
  <h1>Meeting Room Booking Report</h1>
  <div class="subtitle">Generated on <?= date('F j, Y \a\t g:i A') ?></div>
</div>

<?php if (!$print_all && $booking): ?>
<!-- Single Booking Report -->
<div class="booking-details">
  <h2>Booking Details</h2>
  <div class="row">
    <div class="col-6">
      <p><strong>Booking ID:</strong> <?= $booking['id'] ?? '' ?></p>
      <p><strong>Reference:</strong> <?= $booking['booking_ref'] ?? '' ?></p>
      <p><strong>User:</strong> <?= $booking['full_name'] ?? '' ?></p>
      <p><strong>Email:</strong> <?= $booking['email'] ?? '' ?></p>
    </div>
    <div class="col-6">
      <p><strong>Department:</strong> <?= $booking['jabatan_id'] ?? '' ?></p>
      <p><strong>Date:</strong> <?= $booking['booking_date'] ?? '' ?></p>
      <p><strong>Time:</strong> <?= $booking['start_time'] ?? '' ?> - <?= $booking['end_time'] ?? '' ?></p>
      <p><strong>Status:</strong> 
        <span class="status-badge status-<?= strtolower($booking['extra_info'] ?? 'pending') ?>">
          <?= $booking['extra_info'] ?? 'Pending' ?>
        </span>
      </p>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <p><strong>Reason:</strong></p>
      <p><?= $booking['reason'] ?? '' ?></p>
    </div>
  </div>
  <?php if (!empty($booking['extra_request'])): ?>
  <div class="row">
    <div class="col-12">
      <p><strong>Extra Request:</strong></p>
      <p><?= $booking['extra_request'] ?></p>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php elseif ($print_all && !empty($bookings)): ?>
<!-- All Bookings Report -->
<h2 class="section-title">All Bookings (Total: <?= count($bookings) ?>)</h2>
<table class="table">
  <thead>
    <tr>
      <th>Booking Ref</th>
      <th>User Name</th>
      <th>Department</th>
      <th>Booking Date</th>
      <th>Time Slot</th>
      <th>Status</th>
      <th>Reason</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($bookings as $booking): ?>
      <?php
        $status = $booking['extra_info'] ?? 'Pending';
        $statusClass = 'status-' . strtolower($status);
      ?>
      <tr>
        <td><?= htmlspecialchars($booking['booking_ref'] ?? '') ?></td>
        <td><?= htmlspecialchars($booking['full_name'] ?? '') ?></td>
        <td><?= htmlspecialchars($booking['jabatan_id'] ?? '') ?></td>
        <td><?= htmlspecialchars($booking['booking_date'] ?? '') ?></td>
        <td><?= htmlspecialchars($booking['start_time'] ?? '') ?> - <?= htmlspecialchars($booking['end_time'] ?? '') ?></td>
        <td><span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($status) ?></span></td>
        <td><?= htmlspecialchars(substr($booking['reason'] ?? '', 0, 50)) ?>...</td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php else: ?>
<p>No booking data available.</p>
<?php endif; ?>

<div class="no-print" style="margin-top: 30px; text-align: center;">
  <button onclick="window.print()" class="btn btn-primary">Print Report</button>
  <button onclick="window.close()" class="btn btn-secondary">Close</button>
</div>

<script>
window.onload = function() {
  window.print();
}
</script>

</body>
</html>