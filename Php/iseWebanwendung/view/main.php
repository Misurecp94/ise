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

if(true){
    // nothing ;(
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
                <li  class="active"><a href="#">Mein Profil</a></li>
            </ul>
            <ul class="nav navbar-nav">
                <li><a href="freunde.php">Freunde</a></li>
            </ul>
            <form class="navbar-form navbar-left" method="post" action="nutzerSuche.php">
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
                        <div class="form-group">
                            <label for="nName">Nachname</label>
                            <input style="text-align:right;"  type="text" class="form-control" id="nName" placeholder="Nachname" value=<?php echo $persInfo["nName"]; ?>>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="vName">Vorname</label>
                            <input style="text-align:right;"  type="text" class="form-control" id="vName" placeholder="Vorname" value=<?php echo $persInfo["vName"]; ?>>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="age">Alter</label>
                            <input style="text-align:right;"  type="text" class="form-control" id="age" placeholder="Alter" value=<?php echo $persInfo["age"]; ?>>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="groesse">Größe</label>
                            <input style="text-align:right;"  type="text" class="form-control" id="groesse" placeholder="Groesse" value=<?php echo $persInfo["groesse"]; ?>>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="gender">Geschlecht</label>
                            <input style="text-align:right;" type="text" class="form-control" id="gender" placeholder="Geschlecht" value=<?php echo $persInfo["gender"]; ?>>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="work">Arbeit</label>
                            <input style="text-align:right;"  type="text" class="form-control" id="work" placeholder="Arbeit" value=<?php echo $persInfo["work"]; ?>>
                        </div>
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default" type="submit" name="submitPersInfo" id="submitPersInfo">Speichern</button>
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
                        <div class="form-group">
                            <label for="oldPW">Altes Passwort:</label>
                            <input style="text-align:right;" type="password" class="form-control" id="oldPW" placeholder="Altes Passwort">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="newPW1">Neues Passwort:</label>
                            <input style="text-align:right;" type="password" class="form-control" id="newPW1" placeholder="Neues Passwort">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="newPW2">Neues Passwort nochmal:</label>
                            <input style="text-align:right;" type="password" class="form-control" id="newPW2" placeholder="Neues Passwort nochmal">
                        </div>
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default" type="submit" name="submitPasswordChange" id="submitPasswordChange">Passwort ändern</button>
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
                        <div class="form-group">
                            <label for="pic">Neues Bild hochladen</label>
                            <input type="file" id="pic">
                        </div>
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-default" id="submitPic" name="submitPic">Hochladen</button>
                    </div>
                </div>
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
                        <div class="form-group">
                            <label for="email">E-Mail</label>
                            <input style="text-align:right;"  type="email" class="form-control" id="email" placeholder="E-Mail" value=<?php echo $persInfo["email"]; ?>>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="land">Land</label>
                            <input style="text-align:right;"  type="text" class="form-control" id="land" placeholder="Land" value=<?php echo $persInfo["land"]; ?>>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="ort">Ort</label>
                            <input style="text-align:right;"  type="text" class="form-control" id="ort" placeholder="Ort" value=<?php echo $persInfo["ort"]; ?>>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="plz">Postleitzahl</label>
                            <input style="text-align:right;"  type="text" class="form-control" id="plz" placeholder="Postleitzahl" value=<?php echo $persInfo["plz"]; ?>>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="telNr">Telefonnummer</label>
                            <input style="text-align:right;"  type="text" class="form-control" id="telNr" placeholder="Telefonnummer" value=<?php echo $persInfo["telNr"]; ?>>
                        </div>
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default" name="submitKontaktdaten" id="submitKontaktdaten">Speichern</button>
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
                        <textarea class="form-control" rows="5" placeholder="Interessen" name="interests" id="interests" style="resize: none;"><?php echo $interests; ?></textarea>
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default" name="submitInteresse" id="submitInteresse">Speichern</button>
                    </div>
                </div>
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

