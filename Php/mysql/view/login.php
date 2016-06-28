<?php
if(session_status()!=PHP_SESSION_ACTIVE) session_start();
include "../controller/utility.php";
include "../databaseFetcher/databaseController.php";

if(utility::isLoggedIn()){ //if userID in session is set
    if(databaseController::isBanned($_SESSION["userID"])) {
        utility::logout();
        header("Location:login.php?error=\"\"");
        exit();
    } else {
        header("Location: main.php");
    }
}

if(isset($_GET['inputEmail'])){
    if(($userID = databaseController::loginUser($_GET['inputEmail'], $_GET['inputPassword']))!=null){
        $_SESSION['userID'] = $userID;
        if(($adminID = databaseController::isAdmin($userID))!=null){
            $_SESSION['adminID'] = $adminID;
        }
        header("Location:main.php");
        exit();
    } else {
        header("Location:login.php?error=\"\"");
        exit();
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
                        <input type="email" class="form-control" id="inputEmail" name="inputEmail" placeholder="Email" required="required">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="inputPassword" name="inputPassword" placeholder="Passwort" required="required">
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
            <br/>
            <?php
            if(isset($_GET['error'])){
                if($_GET["error"] == 2){
                    echo "
                        <div class=\"alert alert-danger\">
                            <strong>Achtung!</strong> Sie wurden gesperrt. Bitte wenden sie sich an einen Admin.
                        </div>
                    ";
                } else {
                    echo "
                        <div class=\"alert alert-danger\">
                            <strong>Achtung!</strong> Fehlerhafte Eingabe. Bitte versuchen Sie es erneut!
                        </div>
                    ";
                }
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