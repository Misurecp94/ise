<?php
if(session_status()!=PHP_SESSION_ACTIVE) session_start();
include "../controller/utility.php";
include "../databaseFetcher/databaseController.php";
if(!utility::isLoggedIn()){ //if userID in session is NOT set
    header("Location: ../index.php");
    exit();
}
$kontakts = databaseController::getKontaktdaten($_SESSION["userID"]);
$persInfo = databaseController::getPersInfo($_SESSION["userID"]);
$interests = databaseController::getInteresse($_SESSION["userID"]);
$pic = databaseController::getPic($_SESSION["userID"]);

if(isset($_GET["submitPersInfo"])){
    databaseController::changePersInfo($_SESSION["userID"], $_GET["nName"], $_GET["vName"], $_GET["age"], $_GET["groesse"], $_GET["gender"], $_GET["work"]);
    header("Location: main.php");
    exit();
} else if(isset($_GET["submitPasswordChange"])){
    if(utility::checkIfPasswordsAreTheSame($_GET["newPW1"], $_GET["newPW2"]) == true){
        if(databaseController::changePassword($_SESSION["userID"], $_GET["newPW1"])==true){
            header("Location: main.php");
            exit();
        } else {
            header("Location: main.php?error=password");
            exit();
        }
    } else {
        header("Location: main.php?error=password");
        exit();
    }

} else if(isset($_POST["submitPic"])){
    if(isset($_FILES["pic"])){
        if(databaseController::changePic($_SESSION["userID"], $_FILES["pic"]) == true){
            header("Location: main.php");
            exit();
        } else {
            header("Location: main.php?error=pic");
            exit();
        }
    }
} else if(isset($_GET["submitKontaktdaten"])){
    databaseController::changeKontaktdaten($_SESSION["userID"], $_GET["email"],$_GET["land"], $_GET["ort"], $_GET["plz"], $_GET["telNr"]);
    header("Location: main.php");
    exit();
} else if(isset($_GET["submitInteresse"])){
    databaseController::changeInteresse($_SESSION["userID"], $_GET["interests"]);
    header("Location: main.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>K.I.S.</title>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
</head>

<body>

<!-- NavBar -->
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">K.I.S.</a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li  class="active"><a href="main.php">Mein Profil</a></li>
            </ul>
            <ul class="nav navbar-nav">
                <li><a href="freunde.php">Freunde</a></li>
            </ul>
            <form class="navbar-form navbar-left" method="get" action="nutzerSuche.php">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Name eingeben">
                </div>
                <button type="submit" class="btn btn-default">Nutzer suchen</button>
            </form>
            <ul class="nav navbar-nav">
                <li><a href="unterhaltung.php">Unterhaltung</a></li>
            </ul>
            <ul class="nav navbar-nav">
                <li><a href="gruppen.php">Gruppen</a></li>
            </ul>
            <ul class="nav navbar-nav">
                <li><a href="veranstaltung.php">Veranstaltungen</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="../controller/logout.php">Logout</a></li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
<!-- Navbar end -->
<!-- Body of the Page -->
<div class="row">
    <div class="col-md-4" align="right">
        <div class="row">
            <div class="col-md-1">
                <!-- nothing -->
            </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-12">
                            <h1>Persönliche Informationen</h1>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-md-12">
                            <form class="form-horizontal" action="" method="get">
                                <div class="form-group">
                                    <label for="nName">Nachname</label>
                                    <input style="text-align:right;"  type="text" class="form-control" id="nName" name="nName" placeholder="Nachname" value=<?php echo $persInfo["nName"]; ?>>
                                </div>
                                <div class="form-group">
                                    <label for="vName">Vorname</label>
                                    <input style="text-align:right;"  type="text" class="form-control" id="vName" name="vName" placeholder="Vorname" value=<?php echo $persInfo["vName"]; ?>>
                                </div>
                                <div class="form-group">
                                    <label for="age">Alter</label>
                                    <input style="text-align:right;"  type="text" class="form-control" id="age" name="age" placeholder="Alter" value=<?php echo $persInfo["age"]; ?>>
                                </div>
                                <div class="form-group">
                                    <label for="groesse">Größe</label>
                                    <input style="text-align:right;"  type="text" class="form-control" id="groesse" name="groesse" placeholder="Groesse" value=<?php echo $persInfo["groesse"]; ?>>
                                </div>
                                <div class="form-group">
                                    <label for="gender">Geschlecht</label>
                                    <input style="text-align:right;" type="text" class="form-control" id="gender" name="gender" placeholder="Geschlecht" value=<?php echo $persInfo["gender"]; ?>>
                                </div>
                                <div class="form-group">
                                    <label for="work">Arbeit</label>
                                    <input style="text-align:right;"  type="text" class="form-control" id="work" name="work" placeholder="Arbeit" value=<?php echo $persInfo["work"]; ?>>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-default" type="submit" name="submitPersInfo" id="submitPersInfo">Speichern</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <div class="col-md-1">
                <!-- nothing -->
            </div>
        </div>
        <br/>
        <div class="row">
            <div class="col-md-1">
                <!-- nothing -->
            </div>
            <div class="col-md-8">
                 <div class="row">
                     <div class="col-md-12">
                         <h1>Passwort ändern</h1>
                     </div>
                 </div>
                <br/>
                <div class="row">
                    <div class="col-md-12">
                        <form action="" method="get">
                            <div class="form-group">
                                <label for="oldPW">Altes Passwort:</label>
                                <input style="text-align:right;" type="password" class="form-control" id="oldPW" name="oldPW" placeholder="Altes Passwort"  required="required">
                            </div>
                            <div class="form-group">
                                <label for="newPW1">Neues Passwort:</label>
                                <input style="text-align:right;" type="password" class="form-control" id="newPW1" name="newPW1" placeholder="Neues Passwort"  required="required">
                            </div>
                            <div class="form-group">
                                <label for="newPW2">Neues Passwort nochmal:</label>
                                <input style="text-align:right;" type="password" class="form-control" id="newPW2" name="newPW2" placeholder="Neues Passwort nochmal"  required="required">
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-default" type="submit" name="submitPasswordChange" id="submitPasswordChange">Passwort ändern</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php
                if(isset($_GET["error"]) && $_GET["error"] == "password"){
                    echo "
                    <br/>
                    <div class=\"alert alert-danger\">
                        <strong>Achtung!</strong> Passworteingabe ungültig! Erneut versuchen!
                    </div>
                ";
                }
                ?>
            </div>
            <div class="col-md-1">
                <!-- nothing -->
            </div>
        </div>
    </div>
    <div class="col-md-4" align="center">
        <div class="row">
            <div class="col-md-1">
                <!-- nothing -->
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-12">
                        <h1>Benutzerbild</h1>
                    </div>
                </div>
                <br/>
                <br/>
                <div class="row">
                    <div class="col-md-12">
                        <img src="<?php echo $pic; ?>" alt="Kein Bild vorhanden" class="img-responsive img-rounded" style="max-width: 60%" />
                    </div>
                </div>
                <br/>
                <br/>
                <div class="row">
                    <div class="col-md-12">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="pic">Neues Bild hochladen</label>
                                <input type="file" id="pic" name="pic">
                            </div>
                            <br/>
                            <button type="submit" class="btn btn-default" id="submitPic" name="submitPic">Hochladen</button>
                        </form>
                    </div>
                </div>
                <br/>
                <?php
                if(isset($_GET["error"]) && $_GET["error"] == "pic"){
                    echo "
                    <br/>
                    <div class=\"alert alert-danger\">
                        <strong>Achtung!</strong> Ungültige Datei! Erneut versuchen!
                    </div>
                ";
                }
                ?>
            </div>
            <div class="col-md-1">
                <!-- nothing -->
            </div>
        </div>
    </div>
    <div class="col-md-4" align="left">
        <div class="row">
            <div class="col-md-1">
                <!-- nothing -->
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-12">
                        <h1>Kontaktdaten</h1>
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-md-12">
                        <form action="" method="get">
                            <div class="form-group">
                                <label for="email">E-Mail</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="E-Mail" value=<?php echo $kontakts["email"]; ?>>
                            </div>
                            <div class="form-group">
                                <label for="land">Land</label>
                                <input type="text" class="form-control" id="land" name="land" placeholder="Land" value=<?php echo $kontakts["land"]; ?>>
                            </div>
                            <div class="form-group">
                                <label for="ort">Ort</label>
                                <input type="text" class="form-control" id="ort" name="ort" placeholder="Ort" value=<?php echo $kontakts["ort"]; ?>>
                            </div>
                            <div class="form-group">
                                <label for="plz">Postleitzahl</label>
                                <input type="text" class="form-control" id="plz" name="plz" placeholder="Postleitzahl" value=<?php echo $kontakts["plz"]; ?>>
                            </div>
                            <div class="form-group">
                                <label for="telNr">Telefonnummer</label>
                                <input type="text" class="form-control" id="telNr" name="telNr" placeholder="Telefonnummer" value=<?php echo $kontakts["telNr"]; ?>>
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-default" name="submitKontaktdaten" id="submitKontaktdaten">Speichern</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-1">
                <!-- nothing -->
            </div>
        </div>
        <br/>
        <div class="row">
            <div class="col-md-1">
                <!-- nothing -->
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-12">
                        <h1>Interesse</h1>
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-md-12">
                        <form action="" method="get">
                            <textarea class="form-control" rows="5" placeholder="Interessen" name="interests" id="interests" style="resize: none;"><?php echo $interests; ?></textarea>
                            <br/>
                            <button class="btn btn-default" name="submitInteresse" id="submitInteresse">Speichern</button>
                        </form>
                    </div>
                </div>
                <br/>
            </div>
            <div class="col-md-1">
                <!-- nothing -->
            </div>
        </div>
    </div>
</div>
<br/>
<br/>
<!-- End Body of the Page -->

</body>

</html>

