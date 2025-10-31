<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login - Meeting Room</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      height: 100vh;
      justify-content: center;
      align-items: center;
    }

    .admin-login-box {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 15px 35px rgba(0,0,0,0.1);
      width: 400px;
      padding: 40px;
      text-align: center;
    }

    .admin-icon {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      justify-content: center;
      align-items: center;
      color: #fff;
      font-size: 36px;
      margin: 0 auto 20px auto;
    }

    .admin-login-box h2 {
      margin-bottom: 10px;
      font-size: 28px;
      font-weight: 600;
      color: #333;
    }

    .subtitle {
      color: #666;
      margin-bottom: 30px;
      font-size: 14px;
    }

    .error-msg {
      color: #e74c3c;
      font-size: 14px;
      margin-bottom: 15px;
      padding: 10px;
      background: #ffeaea;
      border-radius: 8px;
    }

    .input-group {
      margin-bottom: 20px;
      text-align: left;
    }

    .input-group label {
      font-weight: 500;
      margin-bottom: 8px;
      display: block;
      font-size: 14px;
      color: #555;
    }

    .input-group input {
      width: 100%;
      padding: 14px;
      border-radius: 10px;
      border: 2px solid #e1e5e9;
      font-size: 14px;
      box-sizing: border-box;
      transition: border 0.3s;
    }

    .input-group input:focus {
      border-color: #667eea;
      outline: none;
    }

    .btn-admin {
      width: 100%;
      padding: 14px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      border-radius: 10px;
      color: #fff;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.2s;
      margin-top: 10px;
    }

    .btn-admin:hover {
      transform: translateY(-2px);
    }

    .footer-links {
      margin-top: 25px;
      font-size: 14px;
      color: #666;
    }

    .footer-links a {
      color: #667eea;
      text-decoration: none;
      margin: 0 10px;
    }

    .footer-links a:hover {
      text-decoration: underline;
    }

    .user-login-link {
      margin-top: 20px;
      padding-top: 20px;
      border-top: 1px solid #eee;
    }

    .user-login-link a {
      color: #667eea;
      text-decoration: none;
    }

    .user-login-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="admin-login-box">
    <div class="admin-icon">⚙️</div>
    <h2>Admin Login</h2>
    <div class="subtitle">Meeting Room Booking System</div>
    
    <?php if (session()->getFlashdata('error')): ?>
      <div class="error-msg"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form method="post" action="<?= site_url('admin/login') ?>">
      <div class="input-group">
        <label>Admin Username</label>
        <input type="text" name="username" placeholder="Enter admin username" required>
      </div>
      <div class="input-group">
        <label>Password</label>
        <input type="password" name="password" placeholder="Enter admin password" required>
      </div>
      <button type="submit" class="btn-admin">Admin Login</button>
    </form>

    <div class="footer-links">
      <a href="#">Forgot Password?</a>
    </div>

    <div class="user-login-link">
      <a href="<?= site_url('login') ?>">← Back to User Login</a>
    </div>
  </div>
</body>
</html>