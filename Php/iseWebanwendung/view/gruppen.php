<?php
    if(session_status()!=PHP_SESSION_ACTIVE) session_start();
    include "../controller/utility.php";
    include "../databaseFetcher/databaseController.php";
    if(!utility::isLoggedIn()){ //if userID in session is NOT set
        header("Location: ../index.php");
        exit();
    }

    $groups = databaseController::getGroupList($_SESSION["userID"]);

    if(isset($_GET["group"])){
         $beitraege = databaseController::getBeitraege($_SESSION["userID"],$_GET["group"]);
    }

    if(isset($_GET["createBeitrag"]) && isset($_GET["group"])){

        if(databaseController::createBeitrag($_SESSION["userID"], $_GET["bTitel"], $_GET["bInhalt"],$_GET["group"])){
            
             header("Location: gruppen.php?referal=false&group=".$_GET["group"]);
        }else{
            header("Location: gruppen.php?referal=false&group=".$_GET["group"]);
        }
        exit();
    }else if(isset($_GET["group"]) && !isset($_GET["referal"])){
        echo "working";
        if(databaseController::getBeitraege($_SESSION["userID"], $_GET["group"])){
            echo "working2";
            header("Location: gruppen.php?referal=false&group=".$_GET["group"]);
        } else{
            header("Location: gruppen.php?referal=false&group=".$_GET["group"]);
        }
       
        exit();
    }


    if(isset($_GET["createGroup"])){
        
        if(databaseController::createGroup($_SESSION["userID"], $_GET["gTitel"], $_GET["gThema"])){
            header("Location: gruppen.php");
        }else{
             header("Location: gruppen.php?error=fehlerhappened1");
        }
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
                <li><a href="main.php">Mein Profil</a></li>
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
                <li class="active"><a href="gruppen.php">Gruppen</a></li>
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

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <label >Gruppen erstellen</label>
                 <br><br>
                            <form  action="" method="get">
                                <div class="form-group">
                                    <label for="gTitel">Gruppen Titel</label>
                                    <input style="text-align:right;"  type="text" class="form-control" id="gTitel" name="gTitel" placeholder="Titel">
                                </div>
                                <div class="form-group">
                                    <label for="gThema">Gruppen Thema</label>
                                    <input style="text-align:right;"  type="text" class="form-control" id="gThema" name="gThema" placeholder="Thema">
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-default" type="submit" name="createGroup" id="creatGroup">Erstellen</button>
                                </div>
                            </form>
                <hr>
                <label >Gruppen anzeigen</label>
                 <br><br>
                <div class="list-group">
                    <?php
                    for($i = 0; $i < count($groups); ++$i) {
                        echo "<a href=\"?group=".$groups[$i]['gruppenID']."\" class=\"list-group-item\"><h4>".$groups[$i]['gTitel']."</h4> ".$groups[$i]['gThema']."</a>";
                    }
                    ?>

                </div>
                
            </div>
            <div class="col-md-8">
                <div class="row">
                    Benutzer zu Gruppe hinzufügen
                    <div class="form-group">
                      <label for="gThema">Email</label>
                      <input style="text-align:right;"  type="text" class="form-control" id="uEmail" name="uEmail" placeholder="Email">
                    </div>
                    <input type="hidden" id=group name="group" value="<?php echo $_GET["group"] ?>">
                    <div class="form-group">
                    <button class="btn btn-default" type="submit" name="addUser" id="addUser">Hinzufügen</button>
                    </div>
            	</div>
                <hr>
                
                  <div class="list-group">
                    <?php
                      if(isset($beitraege) && count($beitraege)>0){
                          //echo "<div class=\"row\">";
                            for($i = 0; $i < count($beitraege); $i++) {
                                echo "<li class=\"list-group-item\"><h4>".$beitraege[$i]['bTitel']."</h4> ".$beitraege[$i]['bInhalt']."</li>";
                            }
                          //echo "</div>";
                        }else{
                          echo "Keine Beitraege in dieser Gruppe";
                      }
                    ?>
            	   </div>
                
                <hr>
                <div class="row">
                   <label>Beitrag erstellen</label>
                 <br><br>
                            <form  action="" method="get">
                                <div class="form-group">
                                    <label for="gTitel">Beitrags Titel</label>
                                    <input style="text-align:right;"  type="text" class="form-control" id="bTitel" name="bTitel" placeholder="Titel">
                                </div>
                                <div class="form-group">
                                    <label for="gThema">Inhalt</label>
                                    <input style="text-align:right;"  type="text" class="form-control" id="bInhalt" name="bInhalt" placeholder="Inhalt">
                                </div>
                                 <input type="hidden" id=group name="group" value="<?php echo $_GET["group"] ?>">
                                <div class="form-group">
                                    <button class="btn btn-default" type="submit" name="createBeitrag" id="createBeitrag">Erstellen</button>
                                </div>
                            </form>
            	</div>
            </div>
        </div>
    </div>
    
    
    









<!-- End Body of the Page -->

</body>

</html>

