<?php
    function regmail($email, $user, $role) {
        // the message
        $msg = "Dear $user,\n\nThank you for registering as a $role on the Rakusen's Factory Line Management Dashboard!\nYour Request will be reviewed shortly by an Admin of the Interface.\n\nThank you for your patience.\nAdministration";
        $msg = wordwrap($msg,100);
        // send email
        mail($email,"Account Register Request",$msg);
    }

    function regmailverified($email, $user, $role) {
        $msg = "Dear $user,\n\nThank you for registering as a $role on the Rakusen's Factory Line Management Dashboard!\nAn Admin has looked at your Request and Accepted your Registration.\n\nThank you for your patience and welcome.\nAdministration";
        $msg = wordwrap($msg,100);
        mail($email,"Account Register Confirmation",$msg);
    }

    function usrdeleted($email, $user, $role) {
        // the message
        //$msg = "Dear $user,\n\nThank you for registering as a $role on the Rakusen's Factory Line Management Dashboard!\nAn Admin has looked at your Request and Accepted your Registration.\n\nThank you for your patience and welcome.\nAdministration";
        // use wordwrap() if lines are longer than 70 characters
        //$msg = wordwrap($msg,100);
        // send email
        //mail($email,"Account Register Confirmation",$msg);
    }
?>