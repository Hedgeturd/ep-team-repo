<?php
    function deluser($id) {
        if (isset($id)) {
            require_once("../scripts/dbconnect.php");
            $sql = "SELECT * FROM users WHERE id = $id";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();

            if (isset($row)) {
                // Input validation & sanitization
                $user_name = $row['user_name'];
                $email = $row['email'];
                $role = $row['role'];

                if ($result) {
                    if ($result->num_rows > 0) {
                        $sql = "DELETE FROM users WHERE id = $id";
                        $conn->query($sql);
                        // Success Email
                        //require_once('mailer.php');
                        //regmailverified($email, $user_name, $role);
                        //$emailerr = "User Registered Successfully!<br><br>Please wait for a Confirmation Email<br>from our Admins!";
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
        else {
            //
        }
    }

    function delreq($id) {
            if (isset($id)) {
                require_once("../scripts/dbconnect.php");
                $sql = "SELECT * FROM requests WHERE id = $id";
                $result = $conn->query($sql);
                $row = $result->fetch_assoc();

                if (isset($row)) {
                    // Input validation & sanitization
                    $user_name = $row['user_name'];
                    $email = $row['email'];
                    $role = $row['role'];

                    if ($result) {
                        if ($result->num_rows > 0) {
                            $sql = "DELETE FROM requests WHERE id = $id";
                            $conn->query($sql);
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
            else {
                //
            }
        }
?>