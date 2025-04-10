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
    <?php
        session_start();
        require_once('scripts/check.php');
        require_once('scripts/dbconnect.php');

        $email = $user = $password = "";
        $emailerr = "";
        $bname = $bemail = false;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = trim($_POST['email']);
            $username = trim($_POST['user']);
            $password = trim($_POST['pass']);
            $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
            $role = isset($_POST['role']) ? $_POST['role'] : 'user'; // Default role is 'user'

            // Validate email
            if (verify_email($email)) {
                // Check duplicate emails in database
                if (dupe_email($conn, $email, "requests") && dupe_email($conn, $email, "users")) {
                    $bemail = true;
                }
                else  {
                    $emailerr = "Email Address Already in Use.";
                    $bemail = false;
                }
            }
            else {
                $emailerr = "Invalid Email Address.";
                $bemail = false;
            }

            // Username Validate
            if (preg_match("/^\w+$/",$user)) {
                if (dupe_name($conn, $user, "requests") && dupe_name($conn, $user, "users")) {
                    $bname = true;
                }
                else  {
                    $emailerr = "Username Already in Use.";
                    $bname = false;
                }
            }
            else {
                $emailerr = "Invalid Username.<br><br>Please use Lowercase and Uppercase Letters,<br>Numbers and Underscores.";
                $bname = false;
            }

            // If no errors, add credentials to requests db
            if ($bname && $bemail) {
                $sql = "INSERT INTO requests (email, password, role, user_name) VALUES (?, ?, ?, ?)";
                $result = $conn->prepare($sql);

                if ($result) {
                $result->bind_param("ssss", $email, $hashed_password, $role, $user);

                if ($result->execute()) {
                    $emailerr = "User Registered Successfully!<br><br>Please wait for a Confirmation Email<br>from our Admins!";
                } else {
                    echo '<div class="error">Error executing query: ' . htmlspecialchars($result->error) . '</div>';
                }

                $result->close();
                } else {
                    echo '<div class="error">Error preparing statement: ' . htmlspecialchars($conn->error) . '</div>';
                }

                $conn->close();
            }
        }
    ?>

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

          <?php echo "$emailerr";?>
        </div>
      </div>

    </div>

    <!-- Footer -->
    <footer>
      <p>&copy; 2025 Rakusen's - Real-Time Dashboard</p>
    </footer>
  </body>
</html>