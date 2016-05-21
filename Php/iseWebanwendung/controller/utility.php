<?php

class utility
{
    // Checks if User is logged in, if yes --> return true, else --> return false
    public static function isLoggedIn() {
        if (isset($_SESSION['userID']) && !empty($_SESSION['userID'])) { //if userID in session is set
            return true;
        } else {
            return false;
        }
    }

    public static function logout(){
        if(session_status()==PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
    }
}

/*
 * Help to Session:
 * session_start(); needs to be called ON THE VERY TOP OF EACH PACHE ... Why? I dont fking know, just do it
 * session_start(); starts the Session
 * include a Variable with: $_SESSION["name"] = "value"/value
 * session_unset(); destroys all variables
 * session_destroy(); destroys a session (session needs to be empty)
 */
?>