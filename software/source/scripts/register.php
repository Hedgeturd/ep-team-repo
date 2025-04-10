<?php
    session_start();
    require_once('check.php');
    require_once('dbconnect.php');

    $email = trim($_POST['email']);
    $user = trim($_POST['user']);
    $password = trim($_POST['pass']);
    $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
    $role = isset($_POST['role']) ? $_POST['role'] : 'user'; // Default role is 'user'
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <script src="scripts/login.js"></script>
    <link rel="stylesheet" href="../styles/cmn.css">
    <link rel="stylesheet" href="../styles/login.css">
    <title>Rakusen's Register</title>
  </head>

  <body>
      <!-- Navigation Bar -->
      <nav>
        <div class="logo">
          <img src="../images/Logo.png" alt="Logo" class="logo-img">
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
              <?php
                // Validate email
                  verify_email($email);
                  dupe_email($conn, $email, "requests");
                  dupe_email($conn, $email, "users");

                  // If no errors, add credentials to requests db
                  $sql = "INSERT INTO requests (email, password, role, user_name) VALUES (?, ?, ?, ?)";
                  $result = $conn->prepare($sql);

                if ($result) {
                    $result->bind_param("ssss", $email, $hashed_password, $role, $user);

                    if ($result->execute()) {
                        echo '<div class="success">User registered successfully!</div>';
                    } else {
                        echo '<div class="error">Error executing query: ' . htmlspecialchars($result->error) . '</div>';
                    }

                    $result->close();
                } else {
                    echo '<div class="error">Error preparing statement: ' . htmlspecialchars($conn->error) . '</div>';
                }

                $conn->close();
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