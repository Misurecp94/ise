<?php
if(session_status()!=PHP_SESSION_ACTIVE) session_start();
include "../controller/utility.php";
if(utility::isLoggedIn()){ //if userID in session is set
    header("Location: main.php");
    exit();
}
if(isset($_GET['inputEmail'])){
    include"../databaseFetcher/databaseController.php";
    if(($userID = databaseController::loginUser($_GET['inputEmail'], $_GET['inputPassword']))!=null){
        $_SESSION['userID'] = $userID;
        header("Location:main.php");
    } else {
        header("Location:login.php");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>K.I.S.</title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
</head>

<body>
<br/>
<br/>
<div class="row">
    <div class="col col-md-4">
        <!-- nothing -->
    </div>
    <div class="col col-md-4">
        <div class="col col-md-12">
            <h1 align="center">Login</h1>
            <br/>
            <h3 align="center">Bitte geben Sie Ihre Email und ihr Passwort ein!</h3>
            <br/>
            <br/>
            <form class="form-horizontal">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="inputEmail" name="inputEmail" placeholder="Email">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="inputPassword" name="inputPassword" placeholder="Passwort">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-default">Login</button>
                        <label>Alternativ versuchen Sie: </label>
                        <a href="register.php" class="btn btn-default">Register</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="col col-md-4">
        <!-- nothing -->
    </div>
</div>



</body>

</html>