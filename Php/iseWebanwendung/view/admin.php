<?php
if(session_status()!=PHP_SESSION_ACTIVE) session_start();
include "../controller/utility.php";
include "../databaseFetcher/databaseController.php";
if(!utility::isLoggedIn()){ //if userID in session is NOT set
    header("Location: ../index.php");
    exit();
}
if(!isset($_SESSION["adminID"])){
    header("Location: ../index.php");
    exit();
}

    $search="";
    $users =databaseController::searchUser($search, $_SESSION["userID"]);
 
if(isset($_GET["makeAdmin"])){
    databaseController::makeAdmin($_GET["otherID"]);
 }

if(isset($_GET["blockUser"])){
    $user =databaseController::blockUser($_GET["otherID"]);
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
                    <li class="active"><a href="adminErzeugen.php">Admin anlegen</a></li>
                </ul>
                <ul class="nav navbar-nav">
                    <li><a href="userSperren.php">User sperren</a></li>
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

<div class="col-md-4"></div>
<div class="col-md-4">
<?php
        if(isset($users) && count($users)>0){
            for($i = 0; $i < count($users); ++$i) {
               echo "<div class=\"list-group \"><li class=\"list-group-item\"><h4>".$users[$i]['vorname']." ".$users[$i]['nachname']."</h4>".$users[$i]['email'];
                
                if(!databaseController::isAdmin($users[$i]["nutzerID"])){
                    echo "<form  action=\"\" method=\"get\">
                    <button type=\"submit\" class=\"btn btn-default btn-xs pull-right\" name=\"makeAdmin\" id=\"makeAdmin\">Make Admin</button>

                    <input type=\"hidden\" id=\"otherID\" name=\"otherID\" class=\"form-control\" value=". $users[$i]["nutzerID"]."></form>";
                }
                                
                if(databaseController::isBanned($users[$i]["nutzerID"])!='1'){
                    echo "<form  action=\"\" method=\"get\">
                    <button type=\"submit\" class=\"btn btn-default btn-xs pull-right\" name=\"blockUser\" id=\"blockUser\">Sperren</button>

                    <input type=\"hidden\" id=\"otherID\" name=\"otherID\" class=\"form-control\" value=". $users[$i]["nutzerID"]."></form>";
                }
                            
                echo "</form></li></div>";

            }
                         
        }else{
            echo "Keine User gefunden";
        }
    ?>

</div>
<div class="col-md-4"></div>




<!-- End Body of the Page -->

</body>

</html>

