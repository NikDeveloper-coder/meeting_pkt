<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin • Reports</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap / Icons / Chart.js -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
  main{ padding-top:88px }
  .layout{
    max-width:1400px; margin:0 auto; padding:0 16px 32px;
    display:grid; grid-template-columns:280px 1fr; gap:20px;
  }
  @media (max-width: 991.98px){ .layout{ grid-template-columns:1fr } }

  /* SIDEBAR (desktop) */
  .sidebar-card{ display:none; }
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

  /* STATS */
  .stat-card{
    border:1px solid var(--border); border-radius:16px; background:var(--surface);
    box-shadow:0 10px 26px rgba(15,23,42,.06); padding:16px;
  }
  .stat-label{ color:var(--muted); font-weight:600; font-size:.9rem }
  .stat-value{ font-size:28px; font-weight:800 }
  .bg-soft-teal   { background:linear-gradient(180deg,#e6fbff,#ffffff) }
  .bg-soft-green  { background:linear-gradient(180deg,#e9fdf3,#ffffff) }
  .bg-soft-amber  { background:linear-gradient(180deg,#fff7e6,#ffffff) }
  .bg-soft-cyan   { background:linear-gradient(180deg,#ecfeff,#ffffff) }
  .bg-soft-purple { background:linear-gradient(180deg,#f3e8ff,#ffffff) }
  .bg-soft-pink   { background:linear-gradient(180deg,#fce7f3,#ffffff) }

  /* CHART CARDS */
  .chart-card{
    background:#fff; border:1px solid var(--border); border-radius:16px;
    box-shadow:0 10px 26px rgba(15,23,42,.06); padding:18px;
    margin-bottom:20px;
  }

  /* TABLE */
  .table-card{
    border:1px solid var(--border); border-radius:16px; overflow:hidden; background:#fff;
    box-shadow:0 10px 26px rgba(15,23,42,.06);
  }
  .table thead th{
    background:#0f172a; color:#fff; border-color:transparent; font-weight:600;
  }
  .table tbody tr:hover{ background:#f9fbff }

  /* STATUS BADGES */
  .badge-status{ padding:.4rem .6rem; border-radius:999px; font-weight:600; font-size:.8rem }
  .badge-pending  { background:rgba(245,158,11,.15); color:#7c4603 }
  .badge-approved { background:rgba(22,163,74,.15); color:#065f46 }
  .badge-cancel   { background:rgba(239,68,68,.15); color:#991b1b }

  /* PRINT STYLES */
  @media print {
    .navbar, .sidebar-card, .offcanvas, .btn, .no-print { display:none !important; }
    main { padding-top:0; }
    .layout { grid-template-columns:1fr; gap:0; }
    .page-head { box-shadow:none; border:1px solid #000; }
    .table-card { box-shadow:none; border:1px solid #000; }
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
            <h3 class="mb-0"><i class="bi bi-graph-up-arrow text-info me-2"></i>Reports & Analytics</h3>
            <div class="text-muted">Comprehensive booking and user statistics</div>
          </div>
          <div class="d-flex gap-2 no-print">
            <a href="<?= site_url('admin/printBooking') ?>" target="_blank" class="btn btn-primary">
              <i class="bi bi-printer me-1"></i> Print All Bookings
            </a>
            <a href="<?= site_url('admin/exportBookings') ?>" class="btn btn-success">
              <i class="bi bi-download me-1"></i> Export CSV
            </a>
          </div>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="stat-card bg-soft-teal">
            <div class="stat-label">Total Bookings</div>
            <div class="stat-value"><?= $stats['total_bookings'] ?? 0 ?></div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="stat-card bg-soft-green">
            <div class="stat-label">Total Users</div>
            <div class="stat-value"><?= $stats['total_users'] ?? 0 ?></div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="stat-card bg-soft-amber">
            <div class="stat-label">Pending Bookings</div>
            <div class="stat-value"><?= $stats['pending_bookings'] ?? 0 ?></div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="stat-card bg-soft-purple">
            <div class="stat-label">Approved Bookings</div>
            <div class="stat-value"><?= $stats['approved_bookings'] ?? 0 ?></div>
          </div>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="row">
        <!-- Monthly Bookings Chart -->
        <div class="col-lg-6">
          <div class="chart-card">
            <h5 class="mb-3"><i class="bi bi-calendar-range me-2 text-primary"></i>Monthly Bookings</h5>
            <canvas id="monthlyChart" height="250"></canvas>
          </div>
        </div>

        <!-- Status Distribution Chart -->
        <div class="col-lg-6">
          <div class="chart-card">
            <h5 class="mb-3"><i class="bi bi-pie-chart me-2 text-success"></i>Booking Status Distribution</h5>
            <canvas id="statusChart" height="250"></canvas>
          </div>
        </div>
      </div>

      <!-- Department Distribution -->
      <div class="chart-card">
        <h5 class="mb-3"><i class="bi bi-building me-2 text-warning"></i>Users Per Department</h5>
        <canvas id="departmentChart" height="150"></canvas>
      </div>

      <!-- Recent Bookings Table -->
      <div class="table-card">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead>
              <tr>
                <th>Booking Ref</th>
                <th>User Name</th>
                <th>Department</th>
                <th>Booking Date</th>
                <th>Time Slot</th>
                <th>Status</th>
                <th class="text-center no-print" style="width:120px">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($recent_bookings)): ?>
                <?php foreach($recent_bookings as $booking): ?>
                  <?php
                    $status = $booking['extra_info'] ?? 'Pending';
                    $badgeClass = $status==='Approved' ? 'badge-approved'
                               : ($status==='Cancelled' ? 'badge-cancel' : 'badge-pending');
                  ?>
                  <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($booking['booking_ref'] ?? '') ?></td>
                    <td><?= htmlspecialchars($booking['full_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($booking['jabatan_id'] ?? '') ?></td>
                    <td><?= htmlspecialchars($booking['booking_date'] ?? '') ?></td>
                    <td><?= htmlspecialchars($booking['start_time'] ?? '') ?> - <?= htmlspecialchars($booking['end_time'] ?? '') ?></td>
                    <td><span class="badge-status <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span></td>
                    <td class="text-center no-print">
                      <a href="<?= site_url('admin/printBooking/'.($booking['id'] ?? '')) ?>" target="_blank" class="btn btn-primary btn-sm" title="Print">
                        <i class="bi bi-printer"></i>
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">
                    <i class="bi bi-inbox display-4 d-block mb-2"></i>
                    No bookings found.
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

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Monthly Bookings Chart
  const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
  const monthlyLabels = <?= json_encode(array_column($monthly_bookings, 'month')) ?>;
  const monthlyData = <?= json_encode(array_column($monthly_bookings, 'count')) ?>;

  new Chart(monthlyCtx, {
    type: 'line',
    data: {
      labels: monthlyLabels,
      datasets: [{
        label: 'Bookings',
        data: monthlyData,
        borderColor: 'rgba(6,182,212,0.9)',
        backgroundColor: 'rgba(6,182,212,0.1)',
        borderWidth: 2,
        tension: 0.4,
        fill: true
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: { beginAtZero: true, title: { display: true, text: 'Number of Bookings' } },
        x: { title: { display: true, text: 'Month' } }
      }
    }
  });

  // Status Distribution Chart
  const statusCtx = document.getElementById('statusChart').getContext('2d');
  const statusLabels = <?= json_encode(array_column($status_distribution, 'status')) ?>;
  const statusData = <?= json_encode(array_column($status_distribution, 'count')) ?>;
  const statusColors = ['#f59e0b', '#16a34a', '#ef4444', '#6b7280'];

  new Chart(statusCtx, {
    type: 'doughnut',
    data: {
      labels: statusLabels,
      datasets: [{
        data: statusData,
        backgroundColor: statusColors,
        borderWidth: 2,
        borderColor: '#fff'
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' }
      }
    }
  });

  // Department Distribution Chart
  const deptCtx = document.getElementById('departmentChart').getContext('2d');
  const deptLabels = <?= json_encode(array_column($dept_distribution, 'jabatan_name')) ?>;
  const deptData = <?= json_encode(array_column($dept_distribution, 'user_count')) ?>;

  new Chart(deptCtx, {
    type: 'bar',
    data: {
      labels: deptLabels,
      datasets: [{
        label: 'Number of Users',
        data: deptData,
        backgroundColor: 'rgba(6,182,212,0.35)',
        borderColor: 'rgba(14,116,144,0.9)',
        borderWidth: 1,
        borderRadius: 8,
        hoverBackgroundColor: 'rgba(6,182,212,0.5)'
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      },
      scales: {
        y: { beginAtZero: true, ticks: { stepSize: 1 }, title: { display: true, text: 'Users' } },
        x: { title: { display: true, text: 'Departments' } }
      }
    }
  });
});
</script>

</body>
</html>