<?php
if(session_status()!=PHP_SESSION_ACTIVE) session_start();
include "../controller/utility.php";
if(utility::isLoggedIn()){ //if userID in session is set
    header("Location: main.php");
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>K.I.S.</title>
    <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
</head>

<body>

<h1>TEST Register</h1>



</body>

</html>