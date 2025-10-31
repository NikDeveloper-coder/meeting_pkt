<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin • Add New User</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
  :root{
    --bg:#f7f9fb;
    --surface:#ffffff;
    --ink:#0f172a;
    --muted:#6b7280;
    --border:#e5e7eb;
    --primary:#06b6d4;
    --primary-700:#0e7490;
    --danger:#ef4444;
    --warning:#f59e0b;
  }

  body{ background:var(--bg); color:var(--ink); font-family:'Segoe UI', system-ui, sans-serif; }

  .navbar{ box-shadow:0 6px 18px rgba(15,23,42,.06); }
  .navbar-brand{ font-weight:700; color:var(--primary) !important; }

  main{ padding-top:80px; }
  .layout{
    max-width:1400px; margin:0 auto; padding:0 16px 28px;
    display:grid; grid-template-columns:280px 1fr; gap:20px;
  }
  @media (max-width:991.98px){ .layout{ grid-template-columns:1fr; } }

  .sidebar-card{ display:none; }
  @media (min-width:992px){
    .sidebar-card{
      display:block; position:sticky; top:100px; height:calc(100dvh - 120px);
      background:var(--surface); border:1px solid var(--border); border-radius:16px;
      box-shadow:0 10px 26px rgba(15,23,42,.06); padding:16px;
    }
  }

  .side-link{
    display:flex; align-items:center; gap:.6rem;
    padding:10px 12px; border-radius:12px; color:#1f2937; text-decoration:none;
    margin-bottom:4px; transition:background .2s, color .2s;
  }
  .side-link:hover{ background:#f1f5f9; color:#0e7490; }
  .side-link.active{ background:#dff7fa; color:#0e7490; font-weight:600; }
  .side-link.text-danger:hover{ background:#fee2e2; color:#b91c1c; }

  .offcanvas-body .side-link{ margin-bottom:6px; }

  .page-head{
    background:var(--surface); border:1px solid var(--border); border-radius:16px;
    padding:16px 18px; box-shadow:0 8px 22px rgba(15,23,42,.06);
  }

  .form-card{
    background:var(--surface); border:1px solid var(--border); border-radius:16px;
    padding:24px; box-shadow:0 10px 26px rgba(15,23,42,.06);
  }

  .form-label{ font-weight:600; color:var(--ink); }
  .form-control, .form-select{ 
    border:1px solid var(--border); border-radius:12px; padding:10px 14px;
    transition:border-color .2s, box-shadow .2s;
  }
  .form-control:focus, .form-select:focus{
    border-color:var(--primary); box-shadow:0 0 0 3px rgba(6,182,212,.1);
  }

  .btn-primary{ background:var(--primary); border:none; border-radius:12px; padding:10px 20px; }
  .btn-primary:hover{ background:var(--primary-700); }
  .btn-outline-secondary{ border-radius:12px; padding:10px 20px; }
</style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg bg-white fixed-top">
  <div class="container-fluid">
    <button class="btn btn-outline-secondary d-lg-none me-2" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
      <i class="bi bi-list"></i>
    </button>

    <a class="navbar-brand" href="<?= site_url('admin/dashboard') ?>">
      <i class="bi bi-buildings me-2"></i>Meeting Room Admin
    </a>

    <div class="ms-auto d-flex align-items-center gap-2">
      <div class="dropdown">
        <button class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
          <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars(session()->get('fullname')) ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item" href="<?= site_url('admin/dashboard') ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
          <li><a class="dropdown-item" href="<?= site_url('admin/users') ?>"><i class="bi bi-people me-2"></i>Manage Users</a></li>
          <li><a class="dropdown-item" href="<?= site_url('admin/mailbox') ?>"><i class="bi bi-envelope me-2"></i>Mailbox</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-danger" href="<?= site_url('logout') ?>" onclick="return confirm('Logout now?');"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<!-- Mobile Sidebar -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title"><i class="bi bi-grid me-2"></i>Admin Menu</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <a href="<?= site_url('admin/dashboard') ?>" class="side-link <?= $section=='dashboard'?'active':'' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="<?= site_url('admin/users') ?>" class="side-link <?= $section=='users'?'active':'' ?>"><i class="bi bi-people"></i> Manage Users</a>
    <a href="<?= site_url('admin/mailbox') ?>" class="side-link <?= $section=='mailbox'?'active':'' ?>"><i class="bi bi-envelope"></i> Mailbox</a>
    <a href="<?= site_url('logout') ?>" class="side-link text-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
  </div>
</div>

<main>
  <div class="layout">

    <!-- Sidebar -->
    <aside class="sidebar-card">
      <h5 class="mb-3 fw-bold">System Admin</h5>
      <nav class="d-flex flex-column">
        <a href="<?= site_url('admin/dashboard') ?>" class="side-link <?= $section=='dashboard'?'active':'' ?>"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
        <a href="<?= site_url('admin/users') ?>" class="side-link <?= $section=='users'?'active':'' ?>"><i class="bi bi-people me-2"></i> Manage Users</a>
        <a href="<?= site_url('admin/mailbox') ?>" class="side-link <?= $section=='mailbox'?'active':'' ?>"><i class="bi bi-envelope me-2"></i> Mailbox</a>
        <a href="<?= site_url('logout') ?>" class="side-link text-danger" onclick="return confirm('Are you sure you want to logout?');"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
      </nav>
    </aside>

    <!-- Main Content -->
    <section class="d-flex flex-column gap-3">
      <div class="page-head">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
          <div>
            <h3 class="mb-0"><i class="bi bi-person-plus text-info me-2"></i>Add New User</h3>
            <div class="text-muted">Create a new user account</div>
          </div>
          <a href="<?= site_url('admin/users') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Back to Users
          </a>
        </div>
      </div>

      <div class="form-card">
        <?php if (session()->getFlashdata('errors')): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= $error ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
          </div>
        <?php endif; ?>

        <form method="POST" action="<?= site_url('admin/users/store') ?>">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="full_name" class="form-label">Full Name *</label>
              <input type="text" class="form-control" id="full_name" name="full_name" 
                     value="<?= old('full_name') ?>" required>
            </div>

            <div class="col-md-6">
              <label for="email" class="form-label">Email Address *</label>
              <input type="email" class="form-control" id="email" name="email" 
                     value="<?= old('email') ?>" required>
            </div>

            <div class="col-md-6">
              <label for="username" class="form-label">Username *</label>
              <input type="text" class="form-control" id="username" name="username" 
                     value="<?= old('username') ?>" required>
            </div>

            <div class="col-md-6">
              <label for="password" class="form-label">Password *</label>
              <input type="password" class="form-control" id="password" name="password" required>
              <div class="form-text">Minimum 6 characters</div>
            </div>

            <div class="col-md-6">
              <label for="password_confirm" class="form-label">Confirm Password *</label>
              <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
            </div>

            <div class="col-md-6">
              <label for="jabatan_id" class="form-label">Department *</label>
              <select class="form-select" id="jabatan_id" name="jabatan_id" required>
                <option value="">Select Department</option>
                <?php foreach($jabatan_list as $jabatan): ?>
                  <option value="<?= $jabatan['jabatan_name'] ?>" 
                    <?= (old('jabatan_id') == $jabatan['jabatan_name']) ? 'selected' : '' ?>>
                    <?= $jabatan['jabatan_name'] ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-6">
              <label for="category" class="form-label">Role *</label>
              <select class="form-select" id="category" name="category" required>
                <option value="user" <?= (old('category') == 'user') ? 'selected' : '' ?>>User</option>
                <option value="admin" <?= (old('category') == 'admin') ? 'selected' : '' ?>>Admin</option>
              </select>
            </div>
          </div>

          <div class="mt-4 pt-3 border-top">
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-person-plus me-1"></i> Create User
            </button>
            <a href="<?= site_url('admin/users') ?>" class="btn btn-outline-secondary ms-2">
              <i class="bi bi-x-circle me-1"></i> Cancel
            </a>
          </div>
        </form>
      </div>
    </section>
  </div>
</main>

<script>
// Client-side validation for password confirmation
document.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('form');
  const password = document.getElementById('password');
  const passwordConfirm = document.getElementById('password_confirm');
  
  form.addEventListener('submit', function(e) {
    if (password.value !== passwordConfirm.value) {
      e.preventDefault();
      alert('Passwords do not match!');
      passwordConfirm.focus();
    }
  });
});
</script>

</body>
</html>