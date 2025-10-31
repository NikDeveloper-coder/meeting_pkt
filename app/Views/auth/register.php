<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Register - Meeting Room</title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
<style>
  :root{
    --bg:#0b1220;
    --panel:#0e1628;
    --surface:#ffffff;
    --ink:#0f172a;
    --muted:#667085;
    --border:#e8ecf3;
    --ring:rgba(6,182,212,.22);
    --brand:#06b6d4;
    --brand-700:#0e7490;
  }
  *{box-sizing:border-box}
  html,body{height:100%}
  body{
    margin:0; background:radial-gradient(1200px 700px at 20% -10%, #11203b 0%, #0b1220 60%);
    color:var(--ink);
    font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;
    display:grid; place-items:center; padding:24px;
  }

  .shell{
    width:min(1100px,96vw);
    min-height:620px;
    border-radius:20px;
    overflow:hidden;
    display:grid;
    grid-template-columns: 480px 1fr;
    background:var(--surface);
    box-shadow:0 35px 100px rgba(2,8,23,.55),0 6px 18px rgba(2,8,23,.18);
  }

  .brandPane{
    position:relative;
    background:linear-gradient(180deg, #0f3041 0%, #0b1c2c 100%);
    color:#e8fbff;
    padding:34px;
    display:flex; flex-direction:column; justify-content:space-between;
  }
  .badge{
    width:46px;height:46px;border-radius:14px;
    background:linear-gradient(135deg,var(--brand),#31e2f1);
    display:grid;place-items:center;font-weight:800;color:#00212a;
    box-shadow:0 18px 40px rgba(6,182,212,.45);
  }
  .brandTop{display:flex; align-items:center; gap:12px}
  .brandTitle{margin:0; font-weight:800}
  .brandSub{margin:4px 0 0; color:#c7e8ee; font-size:13px}

  .hero{
    margin-top:40px;
    border:1px solid rgba(255,255,255,.08);
    border-radius:14px;
    overflow:hidden;
    position:relative;
    background:url('<?= base_url("assets/images/meetingroom.jpg") ?>') center/cover no-repeat;
    min-height:320px;
    box-shadow:0 30px 60px rgba(0,0,0,.35);
  }
  .hero::after{
    content:"";
    position:absolute; inset:0;
    background:linear-gradient(180deg, rgba(2,132,199,.18), rgba(14,116,144,.30));
  }
  .heroCap{
    position:absolute; left:12px; right:12px; bottom:12px;
    backdrop-filter: blur(6px);
    background:rgba(14,23,37,.45);
    border:1px solid rgba(255,255,255,.18);
    border-radius:12px; padding:10px 12px; color:#eaffff; font-weight:700; font-size:14px;
    display:flex; align-items:center; justify-content:space-between;
  }
  .footNote{font-size:12px; color:#b6dfe6; text-align:center; margin-top:18px}

  .formPane{
    background:var(--surface);
    padding:34px;
    display:flex; flex-direction:column; gap:16px;
  }
  h2{
    margin:0; font-size:26px; font-weight:800;
    display:flex; align-items:center; gap:.6rem;
  }
  .dot{
    width:38px; height:38px; border-radius:12px; color:#fff; font-weight:800;
    display:grid;place-items:center;
    background:linear-gradient(135deg,var(--brand),#35e0ef);
    box-shadow:0 10px 22px rgba(6,182,212,.32);
    font-size:13px;
  }

  .error-msg,.success-msg{
    font-size:13px; border-radius:10px; padding:10px 12px; border:1px solid transparent;
  }
  .error-msg{ background:#fee2e2; border-color:#fecaca; color:#b91c1c }
  .success-msg{ background:#dcfce7; border-color:#bbf7d0; color:#166534 }

  .form{display:flex; flex-direction:column; gap:14px;}
  .input-group label{display:block; font-size:13px; font-weight:700; color:#374151; margin-bottom:6px;}
  .input-group input, .input-group select{
    width:100%; height:46px; border-radius:12px;
    border:1px solid #d7dbe4; background:#fff; padding:0 12px; font-size:15px;
    transition:border .15s, box-shadow .15s;
  }
  .input-group input:focus, .input-group select:focus{
    outline:0; border-color:var(--brand); box-shadow:0 0 0 4px var(--ring);
  }

  .btn{
    height:48px; border:0; border-radius:12px; color:#fff; font-weight:800; font-size:15px;
    background:linear-gradient(135deg,var(--brand),#22d3ee);
    box-shadow:0 18px 40px rgba(6,182,212,.28);
    cursor:pointer; transition:filter .15s, transform .05s;
  }
  .btn:hover{ filter:brightness(.97) }
  .btn:active{ transform:translateY(1px) }

  .divider{
    display:flex; align-items:center; gap:12px; color:var(--muted); font-size:13px;
  }
  .divider::before,.divider::after{ content:""; height:1px; background:var(--border); flex:1 }

  .providers{ display:flex; gap:12px }
  .providers a{
    width:48px; height:48px; display:flex; align-items:center; justify-content:center;
    border:1px solid var(--border); border-radius:12px; background:#fff;
    transition:box-shadow .15s, transform .05s;
  }
  .providers a:hover{ box-shadow:0 12px 26px rgba(2,8,23,.10) }
  .providers a:active{ transform:translateY(1px) }
  .providers img{ width:22px; height:22px }

  .footer{ text-align:center; font-size:12px; color:var(--muted); line-height:1.6; margin-top:4px }
  .footer a{ color:var(--brand-700); text-decoration:none; font-weight:700 }
  .footer a:hover{ text-decoration:underline }

  @media (max-width:980px){
    .shell{ grid-template-columns:1fr }
    .brandPane{ display:none }
  }
</style>
</head>
<body>
  <div class="shell">
    <aside class="brandPane">
      <div class="brandTop">
        <div class="badge">MR</div>
        <div>
          <h3 class="brandTitle">Meeting Room</h3>
          <p class="brandSub">Booking Reservation System</p>
        </div>
      </div>

      <div class="hero">
        <div class="heroCap">
          <span>Meeting Room</span>
          <span>Secure Access</span>
        </div>
      </div>
      <div class="footNote">Plan. Book. Meet — without the hassle.</div>
    </aside>

    <section class="formPane">
      <h2><span class="dot">MR</span> Create Account</h2>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="error-msg"><?= session()->getFlashdata('error') ?></div>
      <?php endif; ?>
      <?php if (session()->getFlashdata('success')): ?>
        <div class="success-msg"><?= session()->getFlashdata('success') ?></div>
      <?php endif; ?>

      <form class="form" method="post" action="<?= site_url('register') ?>">
        <div class="input-group">
          <label>Full Name</label>
          <input type="text" name="fullname" placeholder="Enter your full name" required>
        </div>
        <div class="input-group">
          <label>Email</label>
          <input type="email" name="email" placeholder="Enter your email" required>
        </div>
        <div class="input-group">
          <label>Department</label>
          <select name="jabatan" required>
            <option value="" disabled selected>Select Department</option>
            <option value="Jabatan Kejuruteraan Elektronik">Jabatan Kejuruteraan Elektronik</option>
            <option value="Jabatan Teknologi Maklumat dan Komunikasi">Jabatan Teknologi Maklumat dan Komunikasi</option>
            <option value="Jabatan Pengajian Am">Jabatan Pengajian Am</option>
          </select>
        </div>
        <div class="input-group">
          <label>Password</label>
          <input type="password" name="password" placeholder="Enter your password" required>
        </div>
        <button class="btn" type="submit">Register</button>
      </form>

      <div class="divider">or use one of these options</div>

      <div class="providers">
        <a href="<?= site_url('auth/google') ?>" aria-label="Continue with Google">
          <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" />
        </a>
        <a href="<?= site_url('auth/apple') ?>" aria-label="Continue with Apple">
          <img src="https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg" alt="Apple" />
        </a>
        <a href="<?= site_url('auth/facebook') ?>" aria-label="Continue with Facebook">
          <img src="https://cdn-icons-png.flaticon.com/512/5968/5968764.png" alt="Facebook" />
        </a>
      </div>

      <div class="footer">
        <p>Already have an account? <a href="<?= site_url('login') ?>">Login</a></p>
        <p>By signing in or creating an account, you agree with our
          <a href="#">Terms & conditions</a> and
          <a href="#">Privacy statement</a>.
        </p>
      </div>
    </section>
  </div>
</body>
</html>
