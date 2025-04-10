<?php
    function verify_email($email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            /* echo '<div class="success">Valid email address</div>'; */
            return true;
        }
        else {
            /* echo '<div class="error">Invalid email format.</div>'; */
            return false;
        }
    }

    function dupe_email($db, $email, $table) {
        // Check if email already exists
        $check_sql = "SELECT id FROM $table WHERE email = ?";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            /* echo('<div class="error">Email already exists. Please use a different one.</div>'); */
            return false;
        }

        $check_stmt->close();
        return true;
    }

    function dupe_name($db, $name, $table) {
        // Check if email already exists
        $check_sql = "SELECT id FROM $table WHERE user_name = ?";
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bind_param("s", $name);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            /* echo('<div class="error">Email already exists. Please use a different one.</div>'); */
            return false;
        }

        $check_stmt->close();
        return true;
    }
?>