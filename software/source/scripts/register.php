<?php
    /* require_once('scripts/check.php');
    require_once('scripts/dbconnect.php');

    // Validate email
    function valid_email($email) {
        if (verify_email($email)) {
            // Check duplicate emails in database
            if (dupe_email($conn, $email, "requests") && dupe_email($conn, $email, "users")) {
                return true;
            }
            else  {
                $emailerr = "Email Address Already in Use.";
                return false;
            }
        }
        else {
            $emailerr = "Invalid Email Address.";
            return false;
        }
    }

    // Username Validate
    function valid_user($user) {
        if (preg_match("/^\w+$/",$user)) {
            if (dupe_name($conn, $user, "requests") && dupe_name($conn, $user, "users")) {
                return true;
            }
            else  {
                $emailerr = "Username Already in Use.";
                return false;
            }
        }
        else {
            $emailerr = "Invalid Username.<br><br>Please use Lowercase and Uppercase Letters,<br>Numbers and Underscores.";
            return false;
        }
    }

    // If no errors, add credentials to requests db
    function send_query($bname, $bemail) {
        if ($bname && $bemail) {
            $sql = "INSERT INTO requests (email, password, role, user_name) VALUES (?, ?, ?, ?)";
            $result = $conn->prepare($sql);

            if ($result) {
            $result->bind_param("ssss", $email, $hashed_password, $role, $user);

            if ($result->execute()) {
                require_once('scripts/mailer.php');
                regmail($email, $user, $role);
                //$emailerr = "User Registered Successfully!<br><br>Please wait for a Confirmation Email<br>from our Admins!";
                return true;
            } else {
                echo '<div class="error">Error executing query: ' . htmlspecialchars($result->error) . '</div>';
                return false;
            }

            $result->close();
            } else {
                echo '<div class="error">Error preparing statement: ' . htmlspecialchars($conn->error) . '</div>';
                return false;
            }

            $conn->close();
        }
    } */
?>