<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Meeting Room · Mailbox</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
  body { background:#f6f8fb; font-family:'Segoe UI',sans-serif; }
  .sidebar { height:100vh; background:#343a40; color:#fff; padding-top:30px; position:fixed; top:0; left:0; width:240px; }
  .sidebar h4 { text-align:center; margin-bottom:30px; }
  .sidebar a { display:block; padding:12px 20px; color:#ddd; text-decoration:none; margin:5px 15px; border-radius:8px; }
  .sidebar a:hover, .sidebar a.active { background:#495057; color:#fff; }

  .content { margin-left:260px; padding:30px; }

  /* Header card */
  .page-head { border:0; border-radius:18px; background:#fff; box-shadow:0 8px 28px rgba(15,23,42,.07); }
  .stat-pill { display:flex; gap:10px; align-items:center; background:#f8fafc; border-radius:14px; padding:10px 14px; }

  /* Table card */
  .table-wrap { border:0; border-radius:18px; background:#fff; box-shadow:0 8px 28px rgba(15,23,42,.07); }
  .table thead th { background:#0f172a; color:#fff; border-color:transparent; }
  .table>tbody>tr:hover { background:#f9fbff; }

  /* Status badges */
  .badge-pending  { background:rgba(245,158,11,.18); color:#b45309; }
  .badge-approved { background:rgba(16,185,129,.18); color:#047857; }
  .badge-cancel   { background:rgba(239,68,68,.18); color:#b91c1c; }

  /* Inputs */
  .input-neo { border-radius:10px; }
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
  <h4><?= htmlspecialchars(session()->get('fullname')); ?></h4>
  <a href="<?= site_url('dashboard') ?>" class="<?= ($section??'')=='dashboard'?'active':'' ?>">📊 Home</a>
  <a href="<?= site_url('dashboard/mailbox') ?>" class="<?= ($section??'')=='mailbox'?'active':'' ?>">📩 Mailbox</a>
  <div class="px-3">
    <a href="<?= site_url('booking') ?>" class="btn btn-success w-100 mt-2">
      <i class="bi bi-calendar-plus me-1"></i> Book Reservation
    </a>
  </div>
  <a href="<?= site_url('dashboard/profile') ?>" class="<?= ($section??'')=='profile'?'active':'' ?>">👤 Profile</a>
  <a href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">🚪 Logout</a>
</div>

<!-- CONTENT -->
<div class="content">

  <!-- Page header -->
  <div class="page-head p-4 mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <h3 class="mb-1"><i class="bi bi-inbox me-2"></i>Mailbox</h3>
        <div class="text-muted">See booking notifications and take action quickly.</div>
      </div>

      <?php
        $total = isset($bookings)? count($bookings):0;
        $approved = isset($bookings)? count(array_filter($bookings, fn($b)=>($b['extra_info']??'')==='Approved')):0;
        $pending  = isset($bookings)? count(array_filter($bookings, fn($b)=>($b['extra_info']??'Pending')==='Pending')):0;
        $cancelled= isset($bookings)? count(array_filter($bookings, fn($b)=>($b['extra_info']??'')==='Cancelled')):0;
      ?>
      <div class="d-flex flex-wrap gap-2">
        <div class="stat-pill"><i class="bi bi-collection"></i><span class="text-muted me-1">Total</span><strong><?= $total ?></strong></div>
        <div class="stat-pill"><i class="bi bi-check2-circle"></i><span class="text-muted me-1">Approved</span><strong><?= $approved ?></strong></div>
        <div class="stat-pill"><i class="bi bi-hourglass-split"></i><span class="text-muted me-1">Pending</span><strong><?= $pending ?></strong></div>
        <div class="stat-pill"><i class="bi bi-x-circle"></i><span class="text-muted me-1">Cancelled</span><strong><?= $cancelled ?></strong></div>
      </div>
    </div>
  </div>

  <!-- Filters -->
  <form method="GET" class="d-flex gap-2 flex-wrap mb-3">
    <div class="input-group input-neo" style="max-width:280px;">
      <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
      <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($filter_date ?? ''); ?>">
    </div>
    <div class="input-group input-neo" style="max-width:220px;">
      <span class="input-group-text"><i class="bi bi-funnel"></i></span>
      <select class="form-select" name="status">
        <?php $qStatus = $statusFilter ?? ''; ?>
        <option value="" <?= $qStatus===''?'selected':''; ?>>All Status</option>
        <option value="Approved"  <?= $qStatus==='Approved'?'selected':''; ?>>Approved</option>
        <option value="Pending"   <?= $qStatus==='Pending'?'selected':''; ?>>Pending</option>
        <option value="Cancelled" <?= $qStatus==='Cancelled'?'selected':''; ?>>Cancelled</option>
      </select>
    </div>
    <button class="btn btn-primary"><i class="bi bi-search me-1"></i> Apply</button>
    <?php if(!empty($filter_date) || !empty($qStatus)): ?>
      <a class="btn btn-outline-secondary" href="<?= site_url('dashboard/mailbox') ?>">Clear</a>
    <?php endif; ?>
  </form>

  <!-- Table -->
  <div class="table-wrap">
    <div class="table-responsive">
      <table class="table align-middle mb-0 table-hover">
        <thead>
          <tr>
            <th>Booking Date</th>
            <th>Time</th>
            <th>Status</th>
            <th style="width:220px">Action</th>
          </tr>
        </thead>
        <tbody class="table-group-divider">
        <?php if(!empty($bookings)): ?>
          <?php foreach($bookings as $mail): ?>
            <?php
              $bid    = (int)($mail['id'] ?? $mail['booking_id'] ?? 0);
              $status = $mail['extra_info'] ?: 'Pending';
              $bClass = $status==='Approved' ? 'badge-approved' : ($status==='Cancelled' ? 'badge-cancel' : 'badge-pending');
              $isOwner = (int)($mail['user_id'] ?? $mail['user_Id'] ?? 0) === (int)session()->get('user_id');
            ?>
            <tr>
              <td class="fw-medium"><?= htmlspecialchars($mail['booking_date'] ?? ''); ?></td>
              <td><?= htmlspecialchars(($mail['start_time'] ?? '').' - '.($mail['end_time'] ?? '')); ?></td>
              <td><span class="badge <?= $bClass ?> rounded-pill px-3 py-2"><?= htmlspecialchars($status) ?></span></td>
              <td class="d-flex flex-wrap gap-2">
                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#mailView<?= $bid ?>">
                  <i class="bi bi-eye"></i> View
                </button>
                <?php if($isOwner): ?>
                  <a href="<?= site_url('booking/update/' . $bid); ?>" class="btn btn-warning btn-sm">
                    <i class="bi bi-pencil-square"></i> Change
                  </a>
                  <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#mailDelete<?= $bid ?>">
                    <i class="bi bi-trash"></i> Delete
                  </button>
                <?php endif; ?>
              </td>
            </tr>

            <!-- View Modal -->
            <div class="modal fade" id="mailView<?= $bid ?>" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content p-4">
                  <h4 class="mb-3">📌 Booking Details</h4>
                  <div class="row g-3">
                    <div class="col-md-4">
                      <div class="text-muted small">Booking ID</div>
                      <div class="fw-semibold"><?= $bid ?></div>
                    </div>
                    <div class="col-md-8">
                      <div class="text-muted small">User</div>
                      <div><?= htmlspecialchars($mail['full_name'] ?? (session()->get('fullname') ?? '')); ?>
                        <?= !empty($mail['email']) ? '(' . htmlspecialchars($mail['email']) . ')' : '' ?>
                      </div>
                    </div>

                    <div class="col-md-4"><div class="text-muted small">Date</div><div><?= htmlspecialchars($mail['booking_date'] ?? ''); ?></div></div>
                    <div class="col-md-4"><div class="text-muted small">Start</div><div><?= htmlspecialchars($mail['start_time'] ?? ''); ?></div></div>
                    <div class="col-md-4"><div class="text-muted small">End</div><div><?= htmlspecialchars($mail['end_time'] ?? ''); ?></div></div>

                    <div class="col-12"><div class="text-muted small">Reason</div><div><?= htmlspecialchars($mail['reason'] ?? '-'); ?></div></div>
                    <div class="col-12"><div class="text-muted small">Extra Request</div><div><?= !empty($mail['extra_request']) ? htmlspecialchars($mail['extra_request']) : '-' ?></div></div>

                    <div class="col-md-6">
                      <div class="text-muted small">Status</div>
                      <div><span class="badge <?= $bClass ?> rounded-pill px-3 py-2"><?= htmlspecialchars($status) ?></span></div>
                    </div>
                    <div class="col-md-6">
                      <div class="text-muted small">Attachment</div>
                      <div>
                        <?php if(!empty($mail['doc_Attachment'])): ?>
                          <a href="<?= htmlspecialchars($mail['doc_Attachment']); ?>" target="_blank" rel="noopener">View File</a>
                        <?php else: ?>None<?php endif; ?>
                      </div>
                    </div>
                  </div>

                  <div class="d-flex justify-content-end gap-2 mt-4">
                    <?php if($isOwner): ?>
                      <a href="<?= site_url('booking/update/' . $bid); ?>" class="btn btn-warning">
                        <i class="bi bi-pencil-square me-1"></i> Change
                      </a>
                      <button class="btn btn-outline-danger" data-bs-target="#mailDelete<?= $bid ?>" data-bs-toggle="modal">
                        <i class="bi bi-trash me-1"></i> Delete
                      </button>
                    <?php endif; ?>
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="mailDelete<?= $bid ?>" tabindex="-1" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-4 text-center">
                  <h4 class="mb-2">Delete Booking</h4>
                  <p class="text-muted mb-3">Are you sure you want to delete booking <strong>#<?= $bid ?></strong>? This action cannot be undone.</p>
                  <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-secondary" data-bs-target="#mailView<?= $bid ?>" data-bs-toggle="modal">Back</button>
                    <a href="<?= site_url('booking/delete/' . $bid); ?>" class="btn btn-danger">Yes, Delete</a>
                  </div>
                </div>
              </div>
            </div>

          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="4" class="text-center text-muted py-5"><i class="bi bi-clipboard2-data fs-2 d-block mb-2"></i>No booking notifications.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content p-4 text-center">
      <h4 class="mb-2">Logout</h4>
      <p>Are you sure you want to log out?</p>
      <div class="mt-2">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <a href="<?= site_url('logout') ?>" class="btn btn-danger">Yes, Logout</a>
      </div>
    </div>
  </div>
</div>

</body>
</html>
