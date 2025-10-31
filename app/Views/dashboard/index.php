<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Meeting Room · Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
  :root{ --ink:#0f172a; --muted:#6b7280; --brand:#1f6feb; --brand2:#7c4dff; }
  body{background:#f6f8fb;font-family:'Segoe UI',system-ui,Arial,sans-serif;color:var(--ink)}
  .navbar-dark{background:#0f172a}
  .brand-dot{width:8px;height:8px;border-radius:50%;background:#22c55e;display:inline-block;margin-left:.25rem}
  .hero{border-bottom:1px solid rgba(15,23,42,.06);
    background:
      radial-gradient(1000px 480px at 10% -10%, rgba(31,111,235,.22), transparent 60%),
      radial-gradient(900px 520px at 110% -20%, rgba(124,77,255,.18), transparent 60%),
      linear-gradient(#fff,#f8fafc 70%)
  }
  .card-soft{border:0;background:#fff;border-radius:18px;box-shadow:0 10px 30px rgba(15,23,42,.07)}
  .card-ghost{border:1px dashed rgba(15,23,42,.1);background:#fff;border-radius:16px}
  .stat{display:flex;gap:12px;align-items:center}
  .stat .ico{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;
    background:linear-gradient(135deg, rgba(31,111,235,.12), rgba(124,77,255,.12))}
  .table thead th{background:#0f172a;color:#fff;border-color:transparent}
  .table>tbody>tr:hover{background:#f9fbff}
  .badge-pending{background:rgba(245,158,11,.18);color:#b45309}
  .badge-approved{background:rgba(16,185,129,.18);color:#047857}
  .badge-cancel{background:rgba(239,68,68,.18);color:#b91c1c}
  .sidebar-sticky{position:sticky; top:88px}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center gap-2" href="<?= site_url('dashboard') ?>">
      <i class="bi bi-layout-wtf"></i> Meeting Room <span class="brand-dot"></span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topnav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="topnav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link <?= ($section??'')==='booking'?'active':'' ?>" href="<?= site_url('booking') ?>">
            <i class="bi bi-calendar-plus me-1"></i>Booking
          </a>
        </li>
      </ul>

      <div class="d-flex align-items-center gap-2">
        <span class="text-white-50 small d-none d-md-inline">
          <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars(session()->get('fullname')); ?>
        </span>
        <button class="btn btn-outline-light btn-sm" data-bs-toggle="offcanvas" data-bs-target="#profileCanvas">
          <i class="bi bi-person-gear me-1"></i> Profile
        </button>
        <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#logoutModal">
          <i class="bi bi-box-arrow-right me-1"></i> Logout
        </button>
      </div>
    </div>
  </div>
</nav>

<!-- HEADER -->
<section class="hero">
  <div class="container py-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div>
        <h2 class="mb-1"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h2>
        <div class="text-muted">Monitor room booking status and perform quick actions.</div>
      </div>
      <a href="<?= site_url('booking') ?>" class="btn btn-success btn-lg">
        <i class="bi bi-plus-lg me-1"></i> New Reservation
      </a>
    </div>

    <!-- STAT CARDS -->
    <?php
      $total    = isset($bookings)? count($bookings):0;
      $approved = isset($bookings)? count(array_filter($bookings, fn($b)=>($b['extra_info']??'')==='Approved')):0;
      $pending  = isset($bookings)? count(array_filter($bookings, fn($b)=>($b['extra_info']??'Pending')==='Pending')):0;
    ?>
    <div class="row g-3 mt-3">
      <div class="col-md-4"><div class="card-soft p-3 stat"><div class="ico"><i class="bi bi-collection fs-5"></i></div><div><div class="text-muted small">Total Bookings</div><div class="h4 mb-0"><?= $total ?></div></div></div></div>
      <div class="col-md-4"><div class="card-soft p-3 stat"><div class="ico"><i class="bi bi-check2-circle fs-5"></i></div><div><div class="text-muted small">Approved</div><div class="h4 mb-0"><?= $approved ?></div></div></div></div>
      <div class="col-md-4"><div class="card-soft p-3 stat"><div class="ico"><i class="bi bi-hourglass-split fs-5"></i></div><div><div class="text-muted small">Pending</div><div class="h4 mb-0"><?= $pending ?></div></div></div></div>
    </div>
  </div>
</section>

<!-- MAIN -->
<div class="container my-4">
  <div class="row g-4">
    <!-- LEFT: TABLE -->
    <div class="col-lg-8">
      <div class="d-flex flex-wrap gap-2 mb-3">
        <form method="GET" class="d-flex gap-2 flex-wrap">
          <input type="hidden" name="section" value="dashboard">
          <div class="input-group" style="max-width:300px">
            <span class="input-group-text"><i class="bi bi-calendar-event"></i></span>
            <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($filter_date ?? ''); ?>">
          </div>
          <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i> Filter</button>
          <?php if(!empty($filter_date)): ?><a class="btn btn-outline-secondary" href="<?= site_url('dashboard') ?>">Clear</a><?php endif; ?>
        </form>
      </div>

      <div class="card-soft">
        <div class="table-responsive">
          <table class="table align-middle mb-0 table-hover">
            <thead>
              <tr>
                <th>Full Name</th>
                <th>Booking Date</th>
                <th>Start</th>
                <th>End</th>
                <th>Status</th>
                <th style="width:160px">Action</th>
              </tr>
            </thead>
            <tbody class="table-group-divider">
            <?php if(!empty($bookings)): ?>
              <?php foreach($bookings as $row): ?>
                <?php
                  $status  = $row['extra_info'] ?: 'Pending';
                  $bClass  = $status==='Approved' ? 'badge-approved' : ($status==='Cancelled' ? 'badge-cancel' : 'badge-pending');
                  $isOwner = (int)($row['user_id'] ?? 0) === (int)session()->get('user_id');
                  $rowId   = (int)$row['id'];
                ?>
                <tr>
                  <td class="fw-medium"><?= htmlspecialchars($row['full_name'] ?? ''); ?></td>
                  <td><?= htmlspecialchars($row['booking_date'] ?? ''); ?></td>
                  <td><?= htmlspecialchars($row['start_time'] ?? ''); ?></td>
                  <td><?= htmlspecialchars($row['end_time'] ?? ''); ?></td>
                  <td><span class="badge <?= $bClass ?> rounded-pill px-3 py-2"><?= htmlspecialchars($status) ?></span></td>
                  <td class="d-flex gap-1">
                    <!-- Always show View -->
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#view<?= $rowId; ?>">
                      <i class="bi bi-eye"></i> View
                    </button>
                  </td>
                </tr>

                <!-- VIEW MODAL -->
                <div class="modal fade" id="view<?= $rowId; ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content p-4">
                      <h4 class="mb-3">📌 Reservation Details</h4>

                      <div class="row g-3">
                        <div class="col-md-4"><div class="text-muted small">Booking ID</div><div class="fw-semibold"><?= htmlspecialchars($row['id']); ?></div></div>
                        <div class="col-md-8"><div class="text-muted small">User</div><div><?= htmlspecialchars($row['full_name'] ?? ''); ?> (<?= htmlspecialchars($row['email'] ?? ''); ?>)</div></div>

                        <div class="col-md-4"><div class="text-muted small">Date</div><div><?= htmlspecialchars($row['booking_date'] ?? ''); ?></div></div>
                        <div class="col-md-4"><div class="text-muted small">Start</div><div><?= htmlspecialchars($row['start_time'] ?? ''); ?></div></div>
                        <div class="col-md-4"><div class="text-muted small">End</div><div><?= htmlspecialchars($row['end_time'] ?? ''); ?></div></div>

                        <div class="col-12"><div class="text-muted small">Reason</div><div><?= htmlspecialchars($row['reason'] ?? '-'); ?></div></div>
                        <div class="col-12"><div class="text-muted small">Extra Request</div><div><?= $row['extra_request'] ? htmlspecialchars($row['extra_request']) : '-'; ?></div></div>

                        <div class="col-md-6"><div class="text-muted small">Status</div>
                          <div><span class="badge <?= $bClass ?> rounded-pill px-3 py-2"><?= htmlspecialchars($status) ?></span></div>
                        </div>
                        <div class="col-md-6"><div class="text-muted small">Attachment</div>
                          <div>
                            <?php if(!empty($row['doc_Attachment'])): ?>
                              <a href="<?= htmlspecialchars($row['doc_Attachment']); ?>" target="_blank" rel="noopener">View File</a>
                            <?php else: ?>None<?php endif; ?>
                          </div>
                        </div>
                      </div>

                      <div class="d-flex justify-content-between align-items-center mt-4">
                        <?php if(!$isOwner): ?>
                          <div class="text-muted small">You can only view this reservation.</div>
                        <?php else: ?>
                          <div class="text-muted small">You can update or delete your reservation.</div>
                        <?php endif; ?>

                        <div class="d-flex gap-2">
                          <?php if($isOwner): ?>
                            <a href="<?= site_url('booking/update/' . $rowId); ?>" class="btn btn-warning">
                              <i class="bi bi-pencil-square me-1"></i> Change
                            </a>
                            <!-- Delete launches confirmation modal -->
                            <button class="btn btn-outline-danger" data-bs-target="#deleteModal<?= $rowId; ?>" data-bs-toggle="modal">
                              <i class="bi bi-trash me-1"></i> Delete
                            </button>
                          <?php endif; ?>
                          <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- DELETE CONFIRMATION (separate modal so View can stay open/stack) -->
                <div class="modal fade" id="deleteModal<?= $rowId; ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content p-4 text-center">
                      <h4 class="mb-2">Delete Reservation</h4>
                      <p class="text-muted mb-3">Are you sure you want to delete booking <strong>#<?= $rowId; ?></strong>? This action cannot be undone.</p>
                      <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-secondary" data-bs-target="#view<?= $rowId; ?>" data-bs-toggle="modal">Back</button>
                        <a href="<?= site_url('booking/delete/' . $rowId); ?>" class="btn btn-danger">Yes, Delete</a>
                      </div>
                    </div>
                  </div>
                </div>

              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" class="text-center text-muted py-5"><i class="bi bi-clipboard2-data fs-2 d-block mb-2"></i>No booking records found.</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- RIGHT: SIDEBAR -->
    <div class="col-lg-4">
      <div class="sidebar-sticky">
        <div class="card-soft p-3 mb-3">
          <div class="d-flex align-items-center justify-content-between">
            <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>About System</h5>
            <a href="<?= site_url('booking') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-plus-lg me-1"></i>Book</a>
          </div>
          <p class="text-muted small mt-2 mb-3">
            The Meeting Room Booking System helps staff easily create, review, and manage meeting room reservations online.
          </p>
          <div class="row g-2 small">
            <div class="col-6"><div class="card-ghost p-2"><div class="text-muted">Version</div><div class="fw-semibold">1.0.0</div></div></div>
            <div class="col-6"><div class="card-ghost p-2"><div class="text-muted">Admin</div><div class="fw-semibold">ICT Department</div></div></div>
          </div>
          <div class="mt-2 small">Contact: <a href="mailto:ict@example.com">ict@example.com</a></div>
        </div>

        <div class="card-soft p-3 mb-3">
          <h6 class="mb-3"><i class="bi bi-bolt me-2"></i>Quick Actions</h6>
          <div class="d-grid gap-2">
            <a class="btn btn-outline-secondary btn-sm" href="<?= site_url('booking') ?>"><i class="bi bi-calendar-plus me-1"></i> Make a Booking</a>
            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#profileCanvas"><i class="bi bi-inbox me-1"></i> View Mailbox</button>
          </div>
        </div>

        <div class="card-soft p-3">
          <h6 class="mb-2"><i class="bi bi-megaphone me-2"></i>Announcements</h6>
          <div class="small text-muted">• Room hours: 8:00 AM – 5:00 PM (Mon–Fri).<br>• All bookings require admin approval.</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- OFFCANVAS PROFILE -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="profileCanvas">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title"><i class="bi bi-person-gear me-2"></i>Profile</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <ul class="nav nav-pills mb-3">
      <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#mailbox-pane"><i class="bi bi-inbox me-1"></i>Mailbox</button></li>
      <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#pwd-pane"><i class="bi bi-key me-1"></i>Change Password</button></li>
    </ul>
    <div class="tab-content">
      <?php $mine = isset($bookings)? array_filter($bookings, fn($b)=> (int)($b['user_id']??0) === (int)session()->get('user_id')):[]; ?>
      <div class="tab-pane fade show active" id="mailbox-pane">
        <div class="table-responsive">
          <table class="table table-sm align-middle">
            <thead><tr><th>Date</th><th>Start</th><th>End</th><th>Status</th></tr></thead>
            <tbody>
              <?php if(!empty($mine)): foreach($mine as $b): $s=$b['extra_info']?:'Pending'; $bc=$s==='Approved'?'badge-approved':($s==='Cancelled'?'badge-cancel':'badge-pending'); ?>
                <tr>
                  <td><?= htmlspecialchars($b['booking_date']??''); ?></td>
                  <td><?= htmlspecialchars($b['start_time']??''); ?></td>
                  <td><?= htmlspecialchars($b['end_time']??''); ?></td>
                  <td><span class="badge <?= $bc ?> rounded-pill px-2 py-1"><?= htmlspecialchars($s) ?></span></td>
                </tr>
              <?php endforeach; else: ?>
                <tr><td colspan="4" class="text-muted text-center">No booking found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="tab-pane fade" id="pwd-pane">
        <form method="post" action="<?= site_url('dashboard/change-password'); ?>" class="needs-validation" novalidate>
          <?= csrf_field(); ?>
          <div class="mb-3"><label class="form-label">Current Password</label><input type="password" name="current_password" class="form-control" required></div>
          <div class="mb-3"><label class="form-label">New Password</label><input type="password" name="new_password" class="form-control" minlength="6" required></div>
          <div class="mb-3"><label class="form-label">Confirm New Password</label><input type="password" name="confirm_password" class="form-control" minlength="6" required></div>
          <button class="btn btn-primary w-100"><i class="bi bi-check2-circle me-1"></i> Update Password</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- LOGOUT -->
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// bootstrap form validation (for Change Password form)
(() => {
  const forms=document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(f=>f.addEventListener('submit',e=>{
    if(!f.checkValidity()){e.preventDefault();e.stopPropagation()} f.classList.add('was-validated')
  }));
})();
</script>
</body>
</html>
