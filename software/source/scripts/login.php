<?php session_start();
    require_once('dbconnect.php');

    $user = $_GET['user'];
    $password = $_GET['pass'];
    $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

    $sql = "SELECT * FROM users WHERE user_name = '$user'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['username']=$row['user_name'];
            $_SESSION['email']=$row['email'];
            $_SESSION['role']=$row['role'];

            if ($row['role'] == "admin") {
                header("Location: ../SignedIn/index.php");
            }
            else {
                header("Location: ../SignedIn/");
            }
        } else {
            $error = "Invalid username or password";
            echo 'alert("Invalid username or password")';
            header("Location: ../");
            echo 'alert("Invalid username or password")';
        }
    } else {
        $error = "Invalid username or password";
        echo 'alert("Invalid username or password")';
        header("Location: ../");
        echo 'alert("Invalid username or password")';
    }

    $conn->close();
?>