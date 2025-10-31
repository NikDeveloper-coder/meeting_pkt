<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Meeting Room Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<style>
  body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
  .sidebar { height: 100vh; background: #343a40; color: #fff; padding-top: 30px; position: fixed; top:0; left:0; width:240px; }
  .sidebar h4 { text-align:center; margin-bottom:30px; }
  .sidebar a { display:block; padding:12px 20px; color:#ddd; text-decoration:none; margin:5px 15px; border-radius:8px; }
  .sidebar a:hover, .sidebar a.active { background:#495057; color:#fff; }
  .content { margin-left:260px; padding:30px; }
  .table thead { background:#343a40; color:#fff; }
  .position-relative .bi { position:absolute; top:38px; right:10px; cursor:pointer; }
</style>
</head>
<body>
<div class="sidebar">
  <h4><?= htmlspecialchars(session()->get('fullname')); ?></h4>
  <a href="<?= site_url('dashboard') ?>" class="<?= $section=='dashboard'?'active':'' ?>">📊 Home</a>
  <a href="<?= site_url('dashboard/mailbox') ?>" class="<?= $section=='mailbox'?'active':'' ?>">📩 Mailbox</a>
  <div class="px-3">
    <a href="<?= site_url('booking') ?>" class="btn btn-success w-100 mt-2">
      <i class="bi bi-calendar-plus me-1"></i> Book Reservation
    </a>
  </div>
  <a href="<?= site_url('dashboard/profile') ?>" class="<?= $section=='profile'?'active':'' ?>">👤 Profile</a>
  <a href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">🚪 Logout</a>
</div>

<div class="content">
  <h2 class="mb-4">👤 My Profile</h2>
  <?php if (session()->getFlashdata('profile_msg')): ?>
    <div class='alert alert-success'><?= session()->getFlashdata('profile_msg') ?></div>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
    <div class='alert alert-danger'><?= session()->getFlashdata('error') ?></div>
  <?php endif; ?>
  <form method="POST" action="<?= site_url('profile/update') ?>" class="mb-3">
    <div class="mb-3">
      <label class="form-label">Full Name</label>
      <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user_data['full_name'] ?? ''); ?>" required>
    </div>
    <div class="mb-3 position-relative">
      <label class="form-label">New Password</label>
      <input type="password" name="password" id="password" class="form-control" required>
      <span class="bi bi-eye-fill" id="togglePassword" title="Show/Hide password"></span>
    </div>
    <button class="btn btn-success" name="update_profile">Update Profile</button>
  </form>

  <script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.classList.toggle('bi-eye');
        this.classList.toggle('bi-eye-fill');
    });
  </script>
</div>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content p-4 text-center">
      <h4 class="mb-3">⚠️ Log Keluar</h4>
      <p>Adakah anda pasti mahu log keluar dari sistem ini?</p>
      <div class="mt-3">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <a href="<?= site_url('logout') ?>" class="btn btn-danger">Ya, Log Keluar</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>