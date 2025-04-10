<?php
    session_start();
    if (empty($_SESSION['username'])):
        header('location: ../');
    endif;

    unset( $_SESSION['username']);
    unset( $_SESSION['email']);
    unset( $_SESSION['role']);

    session_destroy();
    header('location: ../');
?>