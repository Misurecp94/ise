<?php
if(session_status()!=PHP_SESSION_ACTIVE) session_start();
include "utility.php";
utility::logout();
header("Location: ../index.php");
?>