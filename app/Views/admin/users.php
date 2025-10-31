<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin • Manage Users</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap / Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

  /* BUTTONS */
  .btn-edit{ 
    background:#fbbf24; border:none; color:#111827; 
    border-radius:8px; padding:6px 12px;
  }
  .btn-edit:hover{ background:#f59e0b; color:#111827; }
  .btn-delete{ 
    background:var(--danger); border:none; 
    border-radius:8px; padding:6px 12px;
  }
  .btn-delete:hover{ background:#dc2626; }

  /* ROLE BADGE */
  .badge-role-admin{ 
    background:rgba(6,182,212,.16); color:#075985;
    padding:.4rem .6rem; border-radius:999px; font-weight:600; font-size:.8rem;
  }
  .badge-role-user { 
    background:#e5e7eb; color:#374151;
    padding:.4rem .6rem; border-radius:999px; font-weight:600; font-size:.8rem;
  }

  /* ALERTS */
  .alert {
    border-radius:12px;
    border:1px solid var(--border);
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
            <h3 class="mb-0"><i class="bi bi-people text-info me-2"></i>Manage Users</h3>
            <div class="text-muted">Add, edit, or remove user accounts</div>
          </div>
          <a href="<?= site_url('admin/users/new') ?>" class="btn btn-primary">
            <i class="bi bi-person-plus me-1"></i> Add User
          </a>
        </div>
      </div>

      <!-- Success/Error Messages -->
      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
          <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
          <i class="bi bi-exclamation-triangle me-2"></i><?= session()->getFlashdata('error') ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>

      <!-- Table -->
      <div class="table-card">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead>
              <tr>
                <th style="width:60px">#</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Username</th>
                <th>Role</th>
                <th>Department</th>
                <th class="text-center" style="width:140px">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if(!empty($users)): ?>
                <?php $no=1; foreach($users as $usr): ?>
                  <?php
                    $role = strtolower($usr['Category'] ?? 'user');
                    $badgeClass = $role === 'admin' ? 'badge-role-admin' : 'badge-role-user';
                    $userId = $usr['user_Id'] ?? '';
                    $userName = $usr['full_name'] ?? '';
                    $userEmail = $usr['Email'] ?? '';
                    $username = $usr['user_Category'] ?? '';
                    $department = $usr['jabatan_id'] ?? '';
                  ?>
                  <tr>
                    <td class="fw-semibold"><?= $no++; ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($userName); ?></td>
                    <td><?= htmlspecialchars($userEmail); ?></td>
                    <td><?= htmlspecialchars($username); ?></td>
                    <td><span class="badge <?= $badgeClass ?>"><?= ucfirst($usr['Category'] ?? 'User'); ?></span></td>
                    <td><?= htmlspecialchars($department); ?></td>
                    <td class="text-center">
                      <?php if (!empty($userId)): ?>
                        <a href="<?= site_url('admin/users/edit/'.$userId) ?>" class="btn btn-edit btn-sm" title="Edit">
                          <i class="bi bi-pencil-square"></i>
                        </a>
                        <a href="<?= site_url('admin/users/delete/'.$userId) ?>" class="btn btn-delete btn-sm" title="Delete" 
                           onclick="return confirm('Are you sure you want to delete user <?= htmlspecialchars($userName) ?>?');">
                          <i class="bi bi-trash"></i>
                        </a>
                      <?php else: ?>
                        <span class="text-muted">No actions</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">
                    <i class="bi bi-people display-4 d-block mb-2"></i>
                    No users found.
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