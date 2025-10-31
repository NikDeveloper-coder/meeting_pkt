<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Login • Meeting Room</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
<style>
  :root{
    --bg: #f6f8fb;
    --surface: #fff;
    --ink: #0f172a;
    --muted: #667085;
    --border: #e7eaf0;
    --ring: rgba(6,182,212,.18);
    --brand: #06b6d4;     /* aqua */
    --brand-700:#0e7490;  /* teal */
  }
  *{box-sizing:border-box}
  html,body{height:100%}
  body{
    margin:0; background:var(--bg); color:var(--ink);
    font-family: "Inter", system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
    display:grid; place-items:center;
  }

  /* Shell */
  .shell{
    width:min(1160px,94vw);
    background:var(--surface);
    border:1px solid var(--border);
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 28px 72px rgba(2,8,23,.10);
    display:grid; grid-template-columns: 1.02fr .98fr;
  }

  /* Left (form) */
  .left{padding:28px 34px}
  .top{
    display:flex; align-items:center; justify-content:space-between; gap:12px;
    margin-bottom:16px;
  }
  .back{
    display:inline-flex; align-items:center; gap:.5rem;
    padding:8px 12px; border-radius:10px; text-decoration:none;
    color:#364152; background:#e8eef6; font-weight:600; font-size:13px;
  }
  .back:hover{background:#dbe4ef}

  .brand{
    display:flex; align-items:center; gap:.7rem;
  }
  .badge{
    width:40px;height:40px;border-radius:12px;
    display:grid;place-items:center;color:#fff;font-weight:800;
    background:linear-gradient(135deg,var(--brand),#43dbe9);
    box-shadow:0 10px 24px rgba(6,182,212,.35);
  }
  .bname{margin:0;font-weight:700;font-size:18px;line-height:1}
  .bsub{margin:0;color:var(--muted);font-size:13px;line-height:1.3}

  .card{
    margin-top:12px;
    border:1px solid var(--border); border-radius:14px;
    box-shadow:0 14px 36px rgba(15,23,42,.08);
    padding:22px 22px 20px; max-width:520px;
    background:#fff;
  }

  .head{
    display:flex; align-items:center; gap:14px; margin-bottom:8px;
  }
  .avatar{
    width:56px;height:56px;border-radius:50%;
    display:grid;place-items:center;color:#fff;font-size:26px;
    background:linear-gradient(135deg,var(--brand-700),var(--brand));
    box-shadow:0 10px 24px rgba(14,116,144,.35);
  }
  .title{margin:0;font-size:22px;font-weight:700}
  .hint{margin:2px 0 0;color:var(--muted);font-size:13px}

  label{display:block;font-size:13px;font-weight:600;margin:12px 0 6px}
  .field{
    display:flex; align-items:center; gap:.6rem;
    border:1px solid #d7dbe4; border-radius:10px; background:#fff;
    padding:10px 12px; transition:border .15s, box-shadow .15s;
  }
  .field:focus-within{border-color:var(--brand); box-shadow:0 0 0 4px var(--ring)}
  .field i{color:#9aa3af}
  .field input{
    width:100%; border:0; outline:0; font-size:15px; padding:0; background:transparent;
  }

  .util{
    display:flex; align-items:center; justify-content:space-between;
    margin:6px 0 14px;
  }
  .show{display:flex; align-items:center; gap:.5rem; color:#475569; font-size:13px}
  .link{color:var(--brand-700); text-decoration:none; font-weight:700; font-size:13px}
  .link:hover{text-decoration:underline}

  .btn{
    width:100%; border:0; border-radius:10px; cursor:pointer;
    padding:12px; color:#fff; font-weight:800; letter-spacing:.2px;
    background:linear-gradient(135deg,var(--brand),#22d3ee);
    box-shadow:0 16px 34px rgba(6,182,212,.35);
    display:inline-flex; align-items:center; justify-content:center; gap:.6rem;
  }
  .btn:hover{filter:brightness(.97)}
  .btn:active{transform:translateY(1px)}

  .below{text-align:center; margin:12px 0 4px; font-size:14px}
  .below a{color:var(--brand-700); text-decoration:none; font-weight:700}
  .below a:hover{text-decoration:underline}
  .legal{text-align:center; color:var(--muted); font-size:12px}
  .legal a{color:var(--brand-700); text-decoration:none}
  .legal a:hover{text-decoration:underline}

  .alert{
    background:#fee2e2; border:1px solid #fecaca; color:#b91c1c;
    padding:8px 10px; border-radius:10px; font-size:13px; margin:6px 0 10px;
  }

  /* Right (photo) */
  .right{
    position:relative; min-height:560px;
    background:url('<?= base_url("assets/images/meetingroom.jpg") ?>') center/cover no-repeat;
  }
  .overlay{position:absolute; inset:0; background:linear-gradient(180deg, rgba(2,132,199,.18), rgba(14,116,144,.28))}
  .cap{
    position:absolute; left:16px; right:16px; bottom:16px;
    display:flex; gap:8px; align-items:center; justify-content:space-between;
    padding:10px 12px; border-radius:12px; color:#ecfeff; font-weight:700; font-size:14px;
    background:rgba(15,23,42,.4); border:1px solid rgba(255,255,255,.25); backdrop-filter: blur(4px);
  }

  @media (max-width: 980px){
    .shell{grid-template-columns:1fr}
    .right{height:240px}
    .left{padding:22px}
    .card{max-width:none}
  }
</style>
</head>
<body>

<div class="shell">
  <!-- LEFT -->
  <div class="left">
    <div class="top">
      <a href="<?= site_url('/') ?>" class="back"><i class="bi bi-arrow-left"></i> Back to Calendar</a>
      <div class="brand">
        <div class="badge">MR</div>
        <div>
          <p class="bname">Meeting Room</p>
          <p class="bsub">Booking Reservation System</p>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="head">
        <div class="avatar"><i class="bi bi-person"></i></div>
        <div>
          <h2 class="title">Welcome back</h2>
          <p class="hint">Sign in to manage your bookings</p>
        </div>
      </div>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert"><?= session()->getFlashdata('error') ?></div>
      <?php endif; ?>

      <form method="post" action="<?= site_url('login') ?>">
        <label for="email">Email</label>
        <div class="field">
          <i class="bi bi-envelope"></i>
          <input id="email" type="text" name="username" placeholder="you@company.com" autocomplete="username" required />
        </div>

        <label for="password">Password</label>
        <div class="field">
          <i class="bi bi-shield-lock"></i>
          <input id="password" type="password" name="password" placeholder="••••••••" autocomplete="current-password" required />
        </div>

        <div class="util">
          <label class="show">
            <input type="checkbox" id="showPwd" /> Show password
          </label>
          <a class="link" href="#">Forgot?</a>
        </div>

        <button class="btn" type="submit">
          <i class="bi bi-box-arrow-in-right"></i> Login
        </button>
      </form>

      <div class="below">
        Don’t have an account?
        <a href="<?= site_url('register') ?>">Register here</a>
      </div>
      <div class="legal">
        By signing in you agree to our <a href="#">Terms</a> and <a href="#">Privacy</a>.
      </div>
    </div>
  </div>

  <!-- RIGHT -->
  <div class="right">
    <div class="overlay"></div>
    <div class="cap">
      <span><i class="bi bi-buildings me-1"></i>Meeting Room</span>
      <span><i class="bi bi-shield-check me-1"></i>Secure Access</span>
    </div>
  </div>
</div>

<script>
  document.getElementById('showPwd').addEventListener('change', function(){
    const p = document.getElementById('password');
    p.type = this.checked ? 'text' : 'password';
  });
</script>
</body>
</html>
