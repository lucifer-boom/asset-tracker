<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password</title>
  <link rel="stylesheet" href="<?= base_url('assets/css/login.css') ?>">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>

<body>
  <div class="login-container">

    <!-- Left side image -->
    <div class="login-left">
      <img src="<?= base_url('assets/img/login-bg-left.jpg') ?>" alt="Login Illustration">

      <!-- Overlay Cards -->
      <div class="overlay-cards">
        <div class="card active">
          <h2>Welcome to CA Sri Lanka</h2>
          <p>Manage your assets efficiently and securely.</p>
        </div>
        <div class="card">
          <h2>Smart Asset Tracking</h2>
          <p>Stay on top of your organization's IT resources with ease.</p>
        </div>

        <!-- Navigation Dots -->
        <div class="dots">
          <span class="dot active"></span>
          <span class="dot"></span>
        </div>
      </div>
    </div>

    <!-- Right side form -->
    <div class="login-right">



      <div class="login-box">
        <h2>Welcome To CA Sri Lanka Asset Management</h2>
        <p>Please change your password and log in</p>

        <?php if (session()->getFlashdata('error')): ?>
          <p class="error"><?= session()->getFlashdata('error') ?></p>
        <?php endif; ?>
        <?php if (session()->getFlashdata('success')): ?>
          <p class="success"><?= session()->getFlashdata('success') ?></p>
        <?php endif; ?>

        <form id="loginForm" method="post" action="<?= site_url('auth/updatePassword') ?>" autocomplete="off">
          <div class="form-group input-wrapper">
            <input type="password" id="password" name="new_password" placeholder="New Password" required>
            <span id="passwordToggle" class="toggle-icon">👁️</span>
          </div>
          <div class="form-group input-wrapper">
            <input type="password" id="password" name="confirm_password" placeholder="Confirm Password" required>
            <span id="passwordToggle" class="toggle-icon">👁️</span>
          </div>
          <button type="submit" class="login-btn">Login</button>
        </form>

        <p class="footer-text">Forgot password? Contact Admin</p>
      </div>
    </div>
  </div>
  <script src="<?= base_url('assets/js/login.js') ?>"></script>
</body>

</html>