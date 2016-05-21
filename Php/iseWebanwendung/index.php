<?php
if(session_status()!=PHP_SESSION_ACTIVE) session_start();
session_unset();
// DELETE ME! Just to get to mainPage without register/login
//$_SESSION['userID'] = 1;
// If no session is started --> start it
include "controller/utility.php";
//if userID in session is set
if(utility::isLoggedIn()){
    header("Location: view/main.php");

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
<div class="container"">
<br/>
<br/>
    <div class="row">
        <div class="col-md-2">
            <!-- nothing -->
        </div>
        <div class="col-md-8"  align="center">
            <div class="row">
                <div class="col-md-12">
                    <h1>Herzlich Willkommen zu "Keep It Simple"</h1>
                    <br />
                </div>
            </div>
            <div class="row">
                <div class="row-md-12">
                    <h2>Sie müssen sich einloggen. Alternativ bitte registrieren.</h2>
                    <br />
                    <br />
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4">
                            <!-- nothing -->
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- login Button -->
                                    <form action="view/login.php" method="POST">
                                        <input type="submit" class="btn btn-default" style="height:50px;width:200px" id="login" value="Login">
                                    </form>
                                    <br/>
                                    <br/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- register Button -->
                                    <form action="view/register.php" method="POST">
                                        <input type="submit" class="btn btn-default" style="height:50px;width:200px" id="register" value="Register">
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- nothing -->
                        </div>
                    </div>
            </div>
        </div>
        <div class="col-md-2">
            <!-- nothing -->
        </div>
    </div>
</div>
</body>

</html>
