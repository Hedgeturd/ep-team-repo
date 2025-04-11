<?php
    function adduser($id) {
        require_once("scripts/dbconnect.php");
        $sql = "SELECT * FROM requests WHERE id = $id";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();

        // Input validation & sanitization
        $user_name = $row['user_name'];
        $email = $row['email'];
        $password = $row['password'];
        $role = $row['role'];

        // Insert new user data
        $sql = "INSERT INTO users (email, password, role, user_name) VALUES (?, ?, ?, ?)";
        $result = $conn->prepare($sql);

        if ($result) {
            $result->bind_param("ssss", $email, $password, $role, $user_name);

            if ($result->execute()) {
                // Success Email
                require_once('scripts/mailer.php');
                regmailverified($email, $user_name, $role);

                // Delete Record from Requests
                $sql = "DELETE FROM requests WHERE id = $id";
                $conn->query($sql);
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
?>