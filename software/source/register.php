<?php
    $_SESSION['err'] = $email = $user = "";
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <script src="scripts/login.js"></script>
    <link rel="stylesheet" href="styles/cmn.css">
    <link rel="stylesheet" href="styles/login.css">
    <title>Rakusen's Register</title>
  </head>

  <body>
      <?php $_SESSION['err'] = ""; ?>
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
          <div style="padding: 10px;">
            <input type="radio" id="html" name="fav_language" onclick="location.href='./';">
            <label class="login-type" for="html">Log In</label>
            <input type="radio" id="css" name="fav_language" checked="checked">
            <label class="login-type" for="css">Sign Up</label>
          </div>

          <br>
          <div style="padding: 10px;">
            <form name="loginForm"  method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
              <input type="email" name="email" placeholder="Email" value="<?php echo $email;?>" required><br>
              <input type="text"  name="user" placeholder="Username" value="<?php echo $user;?>" required><br>
              <input type="password" name="pass" placeholder="Password" required><br>
              <br>
              <input class="submit-button" type="submit" value="Submit">
            </form>
          </div>

            <br>
            <?php
                $_SESSION['err'] = "";
                require_once('scripts/register.php');

                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    sendregister_req($_POST);
                }
            ?>

          <?php
            if (!empty($_SESSION['err'])) {
                echo $_SESSION['err'];
            }
          ?>
        </div>
      </div>

    </div>

    <!-- Footer -->
    <footer>
      <p>&copy; 2025 Rakusen's - Real-Time Dashboard</p>
    </footer>
  </body>
</html>