<?php
    session_start();
    $email = $user = $password = "";
    $emailerr = "";
    $bname = $bemail = false;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //sendregister_req($_POST, $conn);
    }

    function sendregister_req($_regform) {
        require_once('check.php');
        require_once('dbconnect.php');

        $email = trim($_regform['email']);
        $user = trim($_regform['user']);
        $password = trim($_regform['pass']);
        $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
        $role = isset($_regform['role']) ? $_regform['role'] : 'user'; // Default role is 'user'

        // Validate email
        if (verify_email($email)) {
            // Check duplicate emails in database
            if (dupe_email($conn, $email, "requests") && dupe_email($conn, $email, "users")) {
                $bemail = true;
            }
            else  {
                $_SESSION['err'] = "Email Address Already in Use.";
                $bemail = false;
            }
        }
        else {
            $_SESSION['err'] = "Invalid Email Address.";
            $bemail = false;
        }

        // Username Validate
        if (preg_match("/^\w+$/",$user)) {
            if (dupe_name($conn, $user, "requests") && dupe_name($conn, $user, "users")) {
                $bname = true;
            }
            else  {
                $_SESSION['err'] = "Username Already in Use.";
                $bname = false;
            }
        }
        else {
            $_SESSION['err'] = "Invalid Username.<br><br>Please use Lowercase and Uppercase Letters,<br>Numbers and Underscores.";
            $bname = false;
        }

        // If no errors, add credentials to requests db
        if ($bname && $bemail) {
            $sql = "INSERT INTO requests (email, password, role, user_name) VALUES (?, ?, ?, ?)";
            $result = $conn->prepare($sql);

            if ($result) {
            $result->bind_param("ssss", $email, $hashed_password, $role, $user);

            if ($result->execute()) {
                require_once('scripts/mailer.php');
                regmail($email, $user, $role);
                $_SESSION['err'] = "User Registered Successfully!<br><br>Please wait for a Confirmation Email<br>from our Admins!";
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