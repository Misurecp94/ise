<?php
    if(session_status()!=PHP_SESSION_ACTIVE) session_start();
    include "../controller/utility.php";
    include "../databaseFetcher/databaseController.php";
    if(!utility::isLoggedIn()){ //if userID in session is NOT set
        header("Location: ../index.php");
        exit();
    }

    if(isset($_GET["addGuest"]) && isset($_GET["event"])){
        
        if(databaseController::addGuest($_GET["uEmail"], $_GET["event"])){
            header("Location: veranstaltung.php?referal=false&event=".$_GET["event"]);
        } else{
            header("Location: veranstaltung.php?referal=false&error=true&event=".$_GET["event"]);
        }
        exit();
        
    }

    if(isset($_GET["Absage"])){
        $status="Absage";
        databaseController::updateEventStatus($_SESSION["userID"],$_GET["eventID"], $status);
    }else if(isset($_GET["Zusagen"])){
        $status="Teilnahme";
        databaseController::updateEventStatus($_SESSION["userID"],$_GET["eventID"],$status);
    }


    if(isset($_GET["event"])){
         $guests = databaseController::getGuestList($_GET["event"]);
         
    }

    if(isset($_GET["event"]) && !isset($_GET["referal"])){
       
        header("Location: veranstaltung.php?referal=false&event=".$_GET["event"]);
        exit();
        
    }


    if(isset($_GET["createEvent"])){
        
        if(databaseController::createEvent($_SESSION["userID"], $_GET["vName"], $_GET["vDate"], $_GET["vBesch"], $_GET["vTitel"])){
            header("Location: veranstaltung.php");
        }else{
             header("Location: veranstaltung.php?error=fehlerhappened1");
        }
        exit();
    }


$events = databaseController::getEventList($_SESSION["userID"]);

$openEvents = databaseController::getOpenEventList($_SESSION["userID"]);
$closedEvents = databaseController::getClosedEventList($_SESSION["userID"]);


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
                <li class="active" > <a href="veranstaltung.php">Veranstaltungen</a></li>
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

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
              <label>Veranstaltung erstellen</label>
                <br><br>
                <form  action="" method="get">
                    <div class="form-group">
                        <label for="vTitel">Veranstaltungs Titel </label>
                        <input  type="text" class="form-control" id="vTitel" name="vTitel" placeholder="Titel">
                    </div>
                    <div class="form-group">
                        <label for="vName">Veranstaltungs Name</label>
                        <input   type="text" class="form-control" id="vName" name="vName" placeholder="Name">
                    </div>
                    <div class="form-group">
                        <label for="vBesch">Beschreibung</label>
                        <input   type="text" class="form-control" id="vBesch" name="vBesch" placeholder="Name">
                    </div>
                    <div class="form-group">
                         <label for="vBesch">Datum</label>
                        <div class="input-group date" data-provide="datepicker">
                        <input type="date"  id="vDate" name="vDate" class="form-control" >
                        </div>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-default" type="submit" name="createEvent" id="createEvent">Erstellen</button>
                    </div>
                </form>
                <hr>
                <label >Eigene Veranstaltungen anzeigen</label>
                 <br><br>
                <div class="list-group">
                    <?php
                    for($i = 0; $i < count($events); ++$i) {
                        echo "<a href=\"?event=".$events[$i]['veranstaltungsID']."\" class=\"list-group-item\"><h4>".$events[$i]['vTitel']."</h4> ". $events[$i]['vName'].
                            " <br> " . $events[$i]["vBeschreibung"]  ." - on " .  $events[$i]["vDatum"]."</a>";
                    }
                    ?>
                </div>
            </div>
            <div class="col-md-4">
                <label>Benutzer zu Veranstaltung einladen</label>
                <br><br>
                <form  action="" method="get">
                    <div class="form-group">
                        <label for="uEmail">Email</label>
                        <input  type="text" class="form-control" id="uEmail" name="uEmail" placeholder="Email">
                    </div>
                    <input type="hidden" id="event" name="event" value="<?php echo $_GET["event"] ?>">
                    <div class="form-group">
                        <br>
                        <button class="btn btn-default" type="submit" name="addGuest" id="addGuest">Einladen</button>
                    </div>
                </form>
                <hr>
                <label >Eingeladene Personen anzeigen</label>
                <br><br>
                <div class="list-group">
                 <?php
                  if(isset($guests) && count($guests)>0){
                      //echo "<div class=\"row\">";
                        for($i = 0; $i < count($guests); $i++) {
                            echo "<li class=\"list-group-item\"><h4>".$guests[$i]['vorname']." ".$guests[$i]['nachname'].
                                " </h4>" . $guests[$i]["email"]." - ". $guests[$i]["status"] ."</li>";
                        }
                      //echo "</div>";
                    }else{
                      echo "Keine G&auml;ste f&uuml;r diese Veranstaltung";
                  }
                ?>
                </div>
            </div>
            <div class="col-md-4">

                <label>Eigene Ausstehende Einladungen</label>
                <br><br>
                <!-- chhange to show mitglieder -->
                <div class="list-group">
                     <?php
                    if(isset($openEvents) && count($openEvents)>0){
                        for($i = 0; $i < count($openEvents); ++$i) {
                           echo "<div class=\"list-group \"><li class=\"list-group-item\"><h4>".$openEvents [$i]['vTitel']."</h4> ". $openEvents [$i]['vName'].
                            " <br> " . $openEvents [$i]["vBeschreibung"]  ." - on " .  $openEvents [$i]["vDatum"];

                                echo "<form  action=\"\" method=\"get\">
                                
                                <button type=\"submit\" class=\"btn btn-default btn-xs pull-right\" name=\"Zusagen\" id=\"Zusagen\">Zusagen</button>
                                
                                <input type=\"hidden\" id=\"search\" name=\"eventID\" class=\"form-control\" value=". $openEvents[$i]["veranstaltungsID"].">";
                            
                                if(isset($_GET["event"])){
                                echo "<input type=\"hidden\" id=\"search\" name=\"event\" class=\"form-control\" value=". $_GET["event"].">";
                                }
                                
                                
                                echo"</form><form  action=\"\" method=\"get\">   
                                <button type=\"submit\" class=\"btn btn-default btn-xs pull-right\" name=\"Absage\" id=\"Absage\">Absagen </button>
                                
                                <input type=\"hidden\" id=\"search\" name=\"eventID\" class=\"form-control\" value=". $openEvents [$i]["veranstaltungsID"].">";
                                
                                
                                if(isset($_GET["event"])){
                                echo "<input type=\"hidden\" id=\"search\" name=\"event\" class=\"form-control\" value=". $_GET["event"].">";
                                }
                            
                               echo "</form></li></div>";
                        }

                    }else{
                        echo "Keine Ausstehenden Einladungen";
                    }
                ?>
                </div>
                <hr>
                <label>Beantwortete Einladungen</label>
                <br><br>
                <!-- chhange to show mitglieder -->
                <div class="list-group">
                    <?php
                    if(isset($closedEvents ) && count($closedEvents )>0){
                         for($i = 0; $i < count($closedEvents ); ++$i) {
                        echo "<li class=\"list-group-item\"><h4>".$closedEvents [$i]['vTitel']."</h4> ". $closedEvents [$i]['vName'].
                            " <br> " . $closedEvents [$i]["vBeschreibung"]  ." - on " .  $closedEvents [$i]["vDatum"]." |    ".$closedEvents [$i]["status"]."</li>";
                    }
                    }else{
                        echo "Keine beantworteden Einladungen";   // Sollte niemals bei Gruppenbetrachtung vorkommen! Ersteller muss drin sein!
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    
    









<!-- End Body of the Page -->

</body>

</html>

