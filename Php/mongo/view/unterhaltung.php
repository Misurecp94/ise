<?php
if(session_status()!=PHP_SESSION_ACTIVE) session_start();
include "../controller/utility.php";
include "../databaseFetcher/databaseController.php";
if(!utility::isLoggedIn()){ //if userID in session is NOT set
    header("Location: ../index.php");
    exit();
}
if(databaseController::isBanned($_SESSION["userID"])){
    utility::logout();
    header("Location: login.php?error=2");
    exit();
}
if(isset($_POST['insertMessage'])){
    if(databaseController::sendMessage($_SESSION["userID"], $_GET["unterhaltung"], $_POST["insertMessage"]));
}

if(isset($_GET['otherID'])){
    databaseController::addUnterhaltung($_SESSION["userID"], $_GET["otherID"]);
}
if(isset($_GET['unterhaltung'])){
    $nachrichten = databaseController::getNachrichten($_SESSION["userID"], $_GET["unterhaltung"]);
}

$unterhaltungen = databaseController::getUnterhaltungen($_SESSION["userID"]);
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
                <li><a href="main.php">Mein Profil</a></li>
            </ul>
            <ul class="nav navbar-nav">
                <li><a href="freunde.php">Freunde</a></li>
            </ul>
            <form class="navbar-form navbar-left" method="get" action="nutzerSuche.php">
                <div class="form-group">
                    <input type="text" id="search" name="search" class="form-control" placeholder="Name eingeben">
                </div>
                <button type="submit" class="btn btn-default">Nutzer suchen</button>
            </form>
            <ul class="nav navbar-nav">
                <li class="active"><a href="unterhaltung.php">Unterhaltung</a></li>
            </ul>
            <ul class="nav navbar-nav">
                <li><a href="gruppen.php">Gruppen</a></li>
            </ul>
            <ul class="nav navbar-nav">
                <li><a href="veranstaltung.php">Veranstaltungen</a></li>
            </ul>
            <?php
            if(isset($_SESSION["adminID"])) {
                ?>
                <ul class="nav navbar-nav">
                    <li><a href="admin.php">Admin</a></li>
                </ul>
  
                <?php
            }
            ?>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="../controller/logout.php">Logout</a></li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav>
<!-- Navbar end -->

<!-- Body of the Page -->

<!-- Unterhaltungen anzeigen ... Nachrichten versenden -->
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <?php
            if(isset($nachrichten)){
            ?>
                <form class="form" action="unterhaltung.php?unterhaltung=<?php echo $_GET['unterhaltung'] ?>" method="post">
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <textarea class="form-control" rows="3" placeholder="Nachricht eingeben..." name="insertMessage" id="insertMessage" style="resize: none;"></textarea>
                        </div>
                        <div class="col-sm-2" align="left">
                            <button type="submit" class="btn btn-default">Senden</button>
                        </div>
                        <div class="col-sm-2" align="center">
                            <a href="unterhaltung.php?unterhaltung=<?php echo $_GET['unterhaltung'] ?>" class="btn btn-default">Reload</a>
                        </div>
                        <div class="col-sm-2" align="right">
                            <a href="unterhaltung.php" class="btn btn-default">Schliessen</a>
                        </div>
                    </div>
                </form>
                <hr>
                <?php
                    if(count($nachrichten)>0){
                        
                        for($i = count($nachrichten)-1; $i >= 0; --$i) {
                            if($nachrichten[$i]['EMPFAENGERID'] == $_SESSION["userID"]){
                                echo "<div class=\"list-group \" align='right'><li class=\"list-group-item\"><h4>" . $nachrichten[$i]['NINHALT'] . "</h4><br>";
                                echo "<form><button class=\"btn btn-default btn-xs pull-right disabled\">"
                                    .$nachrichten[$i]['VORNAME']. " " . $nachrichten[$i]['NACHNAME'] . " am " . $nachrichten[$i]['ERSTELLZEITPUNKT']
                                    ."</button></form>";
                            } else {
                                echo "<div class=\"list-group \" align='left'><li class=\"list-group-item\"><h4>" . $nachrichten[$i]['NINHALT'] . "</h4><br>";
                                echo "<form><button class=\"btn btn-default btn-xs pull-left disabled\">"
                                    .$nachrichten[$i]['VORNAME']. " " . $nachrichten[$i]['NACHNAME'] . " am " . $nachrichten[$i]['ERSTELLZEITPUNKT']
                                    ."</button></form>";
                            }
                            echo "</li></div>";
                        }
                    } else {
                        echo "<h3>Starte den Chat! Tippe eine Nachricht ein!</h3>";
                    }
                } else {
                    echo "<h3>Bitte waehlen Sie einen Chat!</h3>";
                }
                echo "<hr>";
                ?>
        </div>
        <div class="col-md-4">
            <h3>Chatten Sie mit: </h3>
            <br>
            <hr>
            <div class="list-group">
                <?php
                for($i = 0; $i < count($unterhaltungen); ++$i) {
                    echo "<a href=\"?unterhaltung=".$unterhaltungen[$i]['unterhaltungsID']."\" class=\"list-group-item\"><h4>".$unterhaltungen[$i]['vorname']." " .$unterhaltungen[$i]['nachname']
                        ."</h4> </a>";
                }
                ?>
            </div>
        </div>
    </div>
</div>
<!-- End Body of the Page -->
</body>

</html>

