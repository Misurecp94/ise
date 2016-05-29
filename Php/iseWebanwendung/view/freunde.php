<?php
if(session_status()!=PHP_SESSION_ACTIVE) session_start();
include "../controller/utility.php";
include "../databaseFetcher/databaseController.php";
if(!utility::isLoggedIn()){ //if userID in session is NOT set
    header("Location: ../index.php");
    exit();
}

$freunde = databaseController::getFriends($_SESSION["userID"]);
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
                <li class="active"><a href="freunde.php">Freunde</a></li>
            </ul>
             <form class="navbar-form navbar-left" method="get" action="nutzerSuche.php">
                <div class="form-group">
                    <input type="text" id="search" name="search" class="form-control" placeholder="Name eingeben">
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

<!-- Freunde anzeigen, unterhaltungen beginnen -->

<div class="row">
    <div class="col-md-4"></div>
    <div class="col-md-4">
        <?php
        if(isset($freunde) && count($freunde)>0){
            for($i = 0; $i < count($freunde); ++$i) {
                echo "<div class=\"list-group \"><li class=\"list-group-item\"><h4>".$freunde[$i]['vorname']." ".$freunde[$i]['nachname']."</h4>".$freunde[$i]['email'];

                if(databaseController::convStarted($_SESSION["userID"],$freunde[$i]['nutzerID'])==0){

                    echo "<form  action=\"unterhaltung.php\" method=\"get\"><button type=\"submit\" class=\"btn btn-default btn-xs pull-right\" name=\"startConv\" id=\"startConv\"> 
                            Unterhaltung starten</button>
                            <input type=\"hidden\" id=\"otherID\" name=\"otherID\" class=\"form-control\" value=".$freunde[$i]['nutzerID']."></form>";
                }
                echo "</li></div>";
            }

        }else{
            echo "Keine User gefunden";
        }
        ?>
    </div>
    <div class="col-md-4"></div>
</div>


<!-- End Body of the Page -->

</body>

</html>

