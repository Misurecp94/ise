<?php
if(session_status()!=PHP_SESSION_ACTIVE) session_start();
include "../controller/utility.php";
include "../databaseFetcher/databaseController.php";
if(utility::isLoggedIn()){ //if userID in session is set
    if(databaseController::isBanned($_SESSION["userID"])){
        utility::logout();
        header("Location: login.php?error=2");
        exit();
    } else {
        header("Location: main.php");
        exit();
    }
}
if(isset($_GET['inputEmail'])){
    if(($userID = databaseController::addUserToDatabase($_GET['inputEmail'], $_GET['inputPassword']))!=null){
        $_SESSION['userID'] = $userID;
        if(($adminID = databaseController::isAdmin($userID))!=null){
            $_SESSION['adminID'] = $adminID;
        }
        header("Location:main.php");
    } else {
        header("Location:register.php?error=\"\"");
    }
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
<br/>
<br/>
<div class="row">
    <div class="col col-md-4">
        <!-- nothing -->
    </div>
    <div class="col col-md-4">
        <div class="col col-md-12">
            <h1 align="center">Registrierung</h1>
            <br/>
            <h3 align="center">Bitte geben Sie Ihre Email und ihr Passwort ein!</h3>
            <br/>
            <br/>
            <form class="form-horizontal" method="get" action="">
                <div class="form-group">
                    <label for="inputEmail" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" name="inputEmail" id="inputEmail" placeholder="Email" required="required">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword" class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" name="inputPassword" id="inputPassword" placeholder="Passwort" required="required">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" name="buttonEnter" class="btn btn-default">Register</button>
                        <label> Alternativ versuchen Sie: </label>
                        <a href="login.php" class="btn btn-default">Login</a>
                    </div>
                </div>
            </form>
            <br/>
            <?php
            if(isset($_GET['error'])){
                echo "
                    <div class=\"alert alert-danger\">
                        <strong>Achtung!</strong> Fehlerhafte Eingabe. Bitte versuchen Sie es erneut! Möglicherweise ist die Email bereits vergeben!
                    </div>
                ";
            }
            ?>
        </div>
    </div>
    <div class="col col-md-4">
        <!-- nothing -->
    </div>
</div>


</body>

</html>