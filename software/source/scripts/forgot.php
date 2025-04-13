<?php
    function overwritepass($formData) {
        require_once("dbconnect.php");
        $user_name = htmlspecialchars($formData["user"]);
        $password = htmlspecialchars($formData["pass"]);

        if (isset($password)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

            $sql = "UPDATE users SET password = ? WHERE user_name = ?";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ss", $hashed_password, $user_name);

                if ($stmt->execute()) {
                        echo "<br>Password updated successfully!";
                        $sql = "DELETE FROM requests WHERE user_name = $user_name";
                        $conn->query($sql);
                        header("location ../");
                    } else {
                        echo "Execute failed: " . $stmt->error;
                    }

                    $stmt->close();

            } else {
                echo "Prepare failed: " . $conn->error;
            }
        }
    }

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    function sendforgotreq($formData) {
        $email = htmlspecialchars($formData["email"]);
        $user_name = htmlspecialchars($formData["user"]);

        if (isset($email)) {
            require_once("dbconnect.php");
            $sql = "SELECT * FROM users WHERE email = '$email' AND user_name = '$user_name'";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();

            if (isset($row)) {
                $password = generateRandomString();
                $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
                $role = $row['role'];
                $type = 'forgot';

                require_once('check.php');
                if (dupe_email($conn, $email, "requests")) {
                    // Insert new user data
                    $sql = "INSERT INTO requests (type, email, password, role, user_name) VALUES (?, ?, ?, ?, ?)";
                    $result = $conn->prepare($sql);

                    if ($result) {
                        $result->bind_param("sssss", $type, $email, $hashed_password, $role, $user_name);

                        if ($result->execute()) {
                            // Success Email
                            require_once('mailer.php');
                            usrforgot($email, $user_name, $password);
                            header("location: ./");
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
        }
    }
?>