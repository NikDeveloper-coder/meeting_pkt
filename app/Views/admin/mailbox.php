<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Admin • Mailbox</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />

<!-- Bootstrap / Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
  :root{
    --bg:#f7f9fb;
    --surface:#ffffff;
    --ink:#0f172a;
    --muted:#6b7280;
    --border:#e5e7eb;
    --primary:#06b6d4;       /* teal aqua */
    --primary-700:#0e7490;
    --success:#16a34a;
    --warning:#f59e0b;
    --danger:#ef4444;
  }

  body{ background:var(--bg); color:var(--ink); font-family:'Segoe UI',system-ui,sans-serif; }

  /* TOP NAVBAR */
  .navbar-brand{ font-weight:700; letter-spacing:.3px }
  .navbar{ box-shadow:0 4px 18px rgba(15,23,42,.06) }

  /* LAYOUT */
  main{ padding-top:88px } /* room for fixed navbar */
  .layout{
    max-width:1400px; margin:0 auto; padding:0 16px 32px;
    display:grid; grid-template-columns:280px 1fr; gap:20px;
  }
  @media (max-width: 991.98px){
    .layout{ grid-template-columns:1fr } /* sidebar becomes offcanvas */
  }

  /* SIDEBAR (desktop) */
  .sidebar-card{
    display:none;
  }
  @media (min-width: 992px){
    .sidebar-card{
      display:block; position:sticky; top:104px; height:calc(100dvh - 120px);
      background:var(--surface); border:1px solid var(--border); border-radius:16px;
      box-shadow:0 10px 26px rgba(15,23,42,.06); padding:16px;
    }
  }
  .side-link{
    display:flex; align-items:center; gap:.6rem;
    padding:10px 12px; border-radius:12px; color:#1f2937; text-decoration:none;
  }
  .side-link:hover{ background:#f1f5f9 }
  .side-link.active{ background:rgba(6,182,212,.14); color:#075985 }

  /* OFFCANVAS (mobile sidebar) */
  .offcanvas-body .side-link{ margin-bottom:6px }

  /* SECTION HEADER */
  .page-head{
    background:var(--surface); border:1px solid var(--border); border-radius:16px;
    padding:18px 20px; box-shadow:0 8px 22px rgba(15,23,42,.06);
  }

  /* TABLE CARD */
  .table-card{
    border:1px solid var(--border); border-radius:16px; overflow:hidden; background:#fff;
    box-shadow:0 10px 26px rgba(15,23,42,.06);
  }
  .table thead th{
    background:#0f172a; color:#fff; border-color:transparent; font-weight:600;
  }
  .table tbody tr:hover{ background:#f9fbff }

  /* STATUS BADGES */
  .badge-status{ 
    padding:.4rem .6rem; border-radius:999px; font-weight:600; font-size:.8rem 
  }
  .badge-pending  { background:rgba(245,158,11,.15); color:#7c4603 }
  .badge-approved { background:rgba(22,163,74,.15); color:#065f46 }
  .badge-cancel   { background:rgba(239,68,68,.15); color:#991b1b }

  /* BUTTONS */
  .btn-primary { 
    border-radius:8px; padding:6px 12px;
  }
</style>
</head>
<body>

<!-- NAVBAR (top) -->
<nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
  <div class="container-fluid">
    <button class="btn btn-outline-secondary d-lg-none me-2" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
      <i class="bi bi-list"></i>
    </button>

    <a class="navbar-brand text-primary" href="<?= site_url('admin/dashboard') ?>">
      <i class="bi bi-buildings me-2"></i>Meeting Room Admin
    </a>

    <div class="ms-auto d-flex align-items-center gap-2">
      <form class="d-none d-md-flex" role="search">
        <div class="input-group">
          <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
          <input class="form-control" type="search" placeholder="Quick search…" aria-label="Search">
        </div>
      </form>

      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
          <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars(session()->get('fullname') ?? 'Admin') ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="<?= site_url('admin/dashboard') ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
          <li><a class="dropdown-item" href="<?= site_url('admin/users') ?>"><i class="bi bi-people me-2"></i>Manage Users</a></li>
          <li><a class="dropdown-item" href="<?= site_url('admin/mailbox') ?>"><i class="bi bi-inbox me-2"></i>Mailbox</a></li>
          <li><a class="dropdown-item" href="<?= site_url('admin/reports') ?>"><i class="bi bi-graph-up me-2"></i>Reports</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<!-- OFFCANVAS (mobile sidebar) -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title"><i class="bi bi-grid me-2"></i>Admin Menu</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <a href="<?= site_url('admin/dashboard') ?>" class="side-link <?= $section=='dashboard'?'active':'' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="<?= site_url('admin/users') ?>" class="side-link <?= $section=='users'?'active':'' ?>"><i class="bi bi-people"></i> Users</a>
    <a href="<?= site_url('admin/mailbox') ?>" class="side-link <?= $section=='mailbox'?'active':'' ?>"><i class="bi bi-inbox"></i> Mailbox</a>
    <a href="<?= site_url('admin/reports') ?>" class="side-link <?= $section=='reports'?'active':'' ?>"><i class="bi bi-graph-up"></i> Reports</a>
    <a href="#" class="side-link" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>
</div>

<main>
  <div class="layout">

    <!-- DESKTOP SIDEBAR -->
    <aside class="sidebar-card">
      <h5 class="mb-3">System Admin</h5>
      <nav class="d-flex flex-column">
        <a href="<?= site_url('admin/dashboard') ?>" class="side-link <?= $section=='dashboard'?'active':'' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="<?= site_url('admin/users') ?>" class="side-link <?= $section=='users'?'active':'' ?>"><i class="bi bi-people"></i> Users</a>
        <a href="<?= site_url('admin/mailbox') ?>" class="side-link <?= $section=='mailbox'?'active':'' ?>"><i class="bi bi-inbox"></i> Mailbox</a>
        <a href="<?= site_url('admin/reports') ?>" class="side-link <?= $section=='reports'?'active':'' ?>"><i class="bi bi-graph-up"></i> Reports</a>
        <a href="#" class="side-link" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="bi bi-box-arrow-right"></i> Logout</a>
      </nav>
    </aside>

    <!-- MAIN CONTENT -->
    <section>

      <!-- Page head -->
      <div class="page-head mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
          <div>
            <h3 class="mb-0"><i class="bi bi-inbox text-info me-2"></i>Mailbox</h3>
            <div class="text-muted">All booking notifications and updates</div>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="table-card">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead>
              <tr>
                <th>Booking Ref</th>
                <th>User Name</th>
                <th>Email</th>
                <th>Booking Date</th>
                <th>Time Slot</th>
                <th>Status</th>
                <th style="width:110px;">Action</th>
              </tr>
            </thead>
            <tbody>
            <?php if(!empty($bookings)): ?>
              <?php foreach($bookings as $mail): ?>
                <?php
                  $status = $mail['extra_info'] ?: 'Pending';
                  $badgeClass = $status==='Approved' ? 'badge-approved'
                             : ($status==='Cancelled' ? 'badge-cancel' : 'badge-pending');
                  $id = (int)($mail['id'] ?? 0);
                ?>
                <tr>
                  <td class="fw-semibold"><?= htmlspecialchars($mail['booking_ref'] ?? '') ?></td>
                  <td><?= htmlspecialchars($mail['full_name'] ?? '') ?></td>
                  <td><?= htmlspecialchars($mail['email'] ?? '') ?></td>
                  <td><?= htmlspecialchars($mail['booking_date'] ?? '') ?></td>
                  <td><?= htmlspecialchars($mail['start_time'] ?? '') ?> - <?= htmlspecialchars($mail['end_time'] ?? '') ?></td>
                  <td><span class="badge-status <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span></td>
                  <td>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#mailView<?= $id ?>">
                      View
                    </button>
                  </td>
                </tr>

                <!-- Modal: Booking Details -->
                <div class="modal fade" id="mailView<?= $id ?>" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Booking Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <div class="col-md-6">
                            <p><strong>Booking ID:</strong><br><?= htmlspecialchars($mail['id'] ?? '') ?></p>
                            <p><strong>Booking Ref:</strong><br><?= htmlspecialchars($mail['booking_ref'] ?? '') ?></p>
                            <p><strong>User:</strong><br><?= htmlspecialchars($mail['full_name'] ?? '') ?></p>
                            <p><strong>Email:</strong><br><?= htmlspecialchars($mail['email'] ?? '') ?></p>
                            <p><strong>Department:</strong><br><?= htmlspecialchars($mail['jabatan_id'] ?? '') ?></p>
                          </div>
                          <div class="col-md-6">
                            <p><strong>Date:</strong><br><?= htmlspecialchars($mail['booking_date'] ?? '') ?></p>
                            <p><strong>Time Slot:</strong><br><?= htmlspecialchars($mail['start_time'] ?? '') ?> - <?= htmlspecialchars($mail['end_time'] ?? '') ?></p>
                            <p><strong>Status:</strong><br><span class="badge-status <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span></p>
                            <p><strong>Attachment:</strong><br>
                              <?php if(!empty($mail['doc_Attachment'])): ?>
                                <a href="<?= htmlspecialchars($mail['doc_Attachment']) ?>" target="_blank" rel="noopener" class="btn btn-outline-primary btn-sm">View File</a>
                              <?php else: ?>
                                <span class="text-muted">None</span>
                              <?php endif; ?>
                            </p>
                          </div>
                        </div>
                        <div class="row mt-3">
                          <div class="col-12">
                            <p><strong>Reason:</strong></p>
                            <div class="border rounded p-3 bg-light">
                              <?= htmlspecialchars($mail['reason'] ?? '') ?>
                            </div>
                          </div>
                        </div>
                        <?php if(!empty($mail['extra_request'])): ?>
                        <div class="row mt-3">
                          <div class="col-12">
                            <p><strong>Extra Request:</strong></p>
                            <div class="border rounded p-3 bg-light">
                              <?= htmlspecialchars($mail['extra_request']) ?>
                            </div>
                          </div>
                        </div>
                        <?php endif; ?>
                      </div>
                      <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" class="text-center text-muted py-4">
                  <i class="bi bi-inbox display-4 d-block mb-2"></i>
                  No booking notifications found.
                </td>
              </tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </section>
  </div>
</main>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Confirm Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">Are you sure you want to logout?</div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <a href="<?= site_url('logout') ?>" class="btn btn-danger">Logout</a>
      </div>
    </div>
  </div>
</div>

</body>
</html>