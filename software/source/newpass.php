<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <script src="scripts/login.js"></script>
  <link rel="stylesheet" href="styles/cmn.css">
  <link rel="stylesheet" href="styles/login.css">
  <title>Rakusen's New Password</title>
</head>

<body>
<!-- Navigation Bar -->
<nav>
  <div class="logo">
    <img src="./images/Logo.png" alt="Logo" class="logo-img">
  </div>
  <div class="menu-icon">
    <i class="fa-solid fa-bars" onclick="toggleMenu()"></i>
  </div>
</nav>

<!--Login Form-->
<div style="padding: 20px;">
  <h1>Login</h1>

  <div class="stats-container">
    <div class="stat-box">
      <h2>Please enter your<br>Username and new Password</h2><br>
      <div style="padding: 10px;">
        <form name="loginForm" method="POST">
          <input type="text" name="user" placeholder="Username" required><br>
          <input type="password" name="pass" placeholder="Password" required><br>
          <br>
          <input class="submit-button" type="submit" name="submit">
        </form>
        <?php
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST['submit'])) {
                    require_once('scripts/forgot.php');
                    overwritepass($_POST);
                }
            }
        ?>
      </div>
    </div>
  </div>

</div>

<!-- Footer -->
<footer>
  <p>&copy; 2025 Rakusen's - Real-Time Dashboard</p>
</footer>
</body>
</html>