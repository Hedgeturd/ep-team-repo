<?php
    session_start();
    if (empty($_SESSION['username'])):
        header('location: ../');
    endif;

    $username = $_SESSION['username'];
    $email = $_SESSION['email'];
    $role = $_SESSION['role'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $db = new mysqli('localhost', 'root', '', 'rakusens_database');

        if ($db->connect_error) {
            die('<div class="error">Connection failed: ' . htmlspecialchars($db->connect_error) . '</div>');
        }

        // Input validation & sanitization
        $user_name = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $role = isset($_POST['role']) ? $_POST['role'] : 'user'; // Default role is 'user'
        $type = 'update';

        // Validate email
        require_once('../../scripts/check.php');
        verify_email($email);
        dupe_email($db, $email, "requests");
        dupe_email($db, $email, "users");

        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

        // Insert new user data
        $sql = "INSERT INTO requests (type, user_name, email, password, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssss", $type, $user_name, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                echo '<div class="success">User Updated Successful!</div>';
                //email thing here
            } else {
                echo '<div class="error">Error executing query: ' . htmlspecialchars($stmt->error) . '</div>';
            }

            $stmt->close();
        } else {
            echo '<div class="error">Error preparing statement: ' . htmlspecialchars($db->error) . '</div>';
        }

        $db->close();
    } else {
        echo '<div class="invalid">Invalid request. Please submit the form.</div>';
    }
?>