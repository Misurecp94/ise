<?php
if(session_status()!=PHP_SESSION_ACTIVE) session_start();
include "../controller/utility.php";
if(!utility::isLoggedIn()){ //if userID in session is NOT set
    header("Location: ../index.php");
    exit();
}
include "../databaseFetcher/databaseController.php";
$data = null;
if(isset($_GET["input"])){
    $data = databaseController::getPersons($_SESSION["userID"], $_GET["input"]);
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
                <li><a href="main.php">Mein Profil</a></li>
            </ul>
            <ul class="nav navbar-nav">
                <li><a href="freunde.php">Freunde</a></li>
            </ul>
            <form class="navbar-form navbar-left" method="get" action="nutzerSuche.php">
                <div class="form-group">
                    <input type="text" class="form-control" name="input" placeholder="Name eingeben">
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

<!-- gefundene Nutzer anzeigen, Möglichkeit bieten freundschaften hinzuzufügen --> 
<br/>
<br/>
<div class="row">
    <div class="col-md-3">

    </div>
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-12">
                <?php
                if($data == null){
                    echo "<h1 align=\"center\">Kein Benutzer gefunden! Bitte versuchen sie es erneut.";
                } else {
                    // gib alles aus ... inkl button freundeadd wenn boolean istfreund = true;
                    $size = sizeof($data);
                    for($i=0; $i<$size; $i++){
                        echo "<div class=\"well\">";
                            echo "<div class=\"row\">";
                                echo "<div class=\"col-md-4\">";
                                    echo "<img src=\"" . $data[$i]["pic"] . "\" class=\"img-responsive img-rounded\" style=\"max-width:30%\" />";
                                echo "</div>";
                                echo "<div class=\"col-md-4\">";
                                    echo "<h1>" . $data[$i]["vName"] . "  " . $data[$i]["nName"];
                                echo "</div>";
                                echo "<div class=\"col-md-4\" align=\"right\">";
                                    if($data[$i]["isFriend"]==false){
                                        echo "<form action=\"freunde.php\" method=\"get\">";
                                            echo "<button class=\"btn btn-default\" name=\"friendAdd\" . value = \"". $data[$i]["userID"] . "\">Freund hinzufügen</button>";
                                        echo "</form>";
                                    } else {
                                        echo "<span class=\"glyphicon glyphicon-ok\" aria-hidden=\"true\"></span>";
                                    }
                                echo "</div>";
                            echo "</div>";
                        echo "</div>";
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <div class="col-md-3">

    </div>
</div>

<!-- End Body of the Page -->

</body>

</html>

