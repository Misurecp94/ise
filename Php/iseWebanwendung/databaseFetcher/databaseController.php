<?php

class databaseController
{
            
    public $con = null;

    
    public function createDatabaseConnection(){
       
            $servername = 'localhost';
            $username = 'root';
            $password = '';
            $dbname= 'kis';
        global $con;
        $con = mysqli_connect($servername, $username, $password, $dbname);

    }

    public function closeDatabaseConnection(){
        global $con;
        mysqli_close($con);
    }
    
    public static function createGroup($userID, $GTitel, $GThema){
        global $con;
        
        databaseController::createDatabaseConnection();
        
        mysqli_autocommit($con,false);
        $sql = "INSERT INTO gruppe (gTitel,gThema,nutzerID) VALUES ".
        "('$GTitel','$GThema','$userID')";
        
         if(mysqli_query( $con, $sql )){
             // insert in ist mitglied mit letzter id von gruppe
            $last_id = mysqli_insert_id($con);
             
            $sql = "INSERT INTO istmitglied (nutzerID,gruppenID) VALUES ('$userID','$last_id')";
             if(mysqli_query( $con, $sql )){
                 mysqli_commit($con);
                 databaseController::closeDatabaseConnection();
                 return true;
             }else{
                 mysqli_rollback($con);
                 databaseController::closeDatabaseConnection();
                 return false;
             }
         }else{
            mysqli_rollback($con);
             databaseController::closeDatabaseConnection();
             return false;
         }       
    }

    public static function addUserToDatabase($inputEmail, $inputPassword)
    {
        global $con;
        databaseController::createDatabaseConnection();
        mysqli_autocommit($con,false);

        $sql = "INSERT INTO benutzer(email, passwort) VALUES ('$inputEmail', '$inputPassword')";

        if(mysqli_query($con,$sql)){
            $lastID = mysqli_insert_id($con);
            mysqli_commit($con);
            databaseController::closeDatabaseConnection();
            return $lastID;
        } else {
            mysqli_rollback($con);
            databaseController::closeDatabaseConnection();
            return null;
        }
    }
    
    public static function addUserToGroup($Email,$group){
        global $con;
        databaseController::createDatabaseConnection();

        $sql = "SELECT nutzerID FROM benutzer WHERE benutzer.email ='$Email'";
       
        $db_erg = mysqli_query( $con, $sql );
        $row = mysqli_fetch_row($db_erg);

        if(isset($row)){   
        
            
            $sql = "INSERT INTO istmitglied (nutzerID,gruppenID) VALUES ".
            "('$row[0]','$group')";

            if(mysqli_query( $con, $sql )){
                mysqli_free_result($db_erg); 
                databaseController::closeDatabaseConnection();
                return true;
            }else{
                mysqli_free_result($db_erg); 
                databaseController::closeDatabaseConnection();
                return false;
            } 
        }
         mysqli_free_result($db_erg); 
        return false;
        
    }
    
     public static function createBeitrag($userID, $BTitel, $BInhalt,$group){
        global $con;
        
        databaseController::createDatabaseConnection();
        
        $sql = "INSERT INTO beitrag (nutzerID,bTitel,bInhalt,gruppenID) VALUES ('$userID','$BTitel','$BInhalt','$group')";
        
         if(mysqli_query( $con, $sql )){

            databaseController::closeDatabaseConnection();
            return true;
         }else{

             databaseController::closeDatabaseConnection();
             return false;
         }       
    }
    
    public static function getBeitraege($userID,$Gruppe){
        
       global $con;
        databaseController::createDatabaseConnection();
        
        $sql = "SELECT beitrag.bTitel,beitrag.bInhalt FROM beitrag WHERE beitrag.gruppenID='$Gruppe'";
        $db_erg = mysqli_query( $con, $sql );
        $result=[];
        while($row = mysqli_fetch_assoc($db_erg)){
            $result[] = $row;
        }
        return $result;
    }
    
    public static function getGroupList($userID){
        
        global $con;
        databaseController::createDatabaseConnection();
        
        $sql = "SELECT gruppe.gTitel,gruppe.gThema,gruppe.gruppenID FROM gruppe,istmitglied WHERE istmitglied.nutzerID='$userID' AND istmitglied.gruppenID=gruppe.gruppenID";
        $db_erg = mysqli_query( $con, $sql );
        $result=[];
        while($row = mysqli_fetch_assoc($db_erg)){
            $result[] = $row;
        }
        return $result;
    }
    

    // Thanks to: http://www.w3schools.com/php/php_file_upload.asp
    public static function changePic($userID, $pic){
        $target_dir = "../pic/";
        $target_file = $target_dir . basename($pic["name"]);
        $uploadOk = 1;
        $imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
        // Check if image file is a actual image or fake image
        if(isset($_POST["submit"])) {
            $check = getimagesize($pic["tmp_name"]);
            if($check !== false) {
                // Image ok
                $uploadOk = 1;
            } else {
                // Not an Image
                $uploadOk = 0;
            }
        }
        if ($pic["size"] > 5000000) {
            // to large
            $uploadOk = 0;
        }
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif" ) {
            // nur Bilder erlauben
            $uploadOk = 0;
        }
        if ($uploadOk == 0) {
            return false;
        } else {
            if (move_uploaded_file($pic["tmp_name"], $target_file)) {
                // Datei hochgeladen, Nutzer ImagePfad geben

                databaseController::createDatabaseConnection();

                $sqlhelper = $target_dir . basename($pic[name]);

                $sql ="UPDATE benutzer SET benutzer.benutzerbild='$sqlhelper'WHERE benutzer.nutzerID = '$userID'";
                global $con;
                $db_erg=mysqli_query($con, $sql);
                databaseController::closeDatabaseConnection();

                if(!$db_erg){
                    return false;
                } else {
                    return true;

                }

            } else {
                // Some error ocurred
                return false;
            }
        }
        // Falls irgendwas schief geht:
    }

    public static function getPic($userID){
        global $con;
        databaseController::createDatabaseConnection();

        $sql = "SELECT benutzerbild FROM benutzer WHERE benutzer.nutzerID = '$userID'";

        $db_erg = mysqli_query($con, $sql);
        $row = mysqli_fetch_row($db_erg);
        mysqli_free_result($db_erg);

        databaseController::closeDatabaseConnection();

        if($row[0] != null){
            return $row[0];
        } else {
            return "../pic/error.jpg";
        }
    }

    public static function getInteresse($userID){
       
        global $con;
        databaseController::createDatabaseConnection();
        
        $sql = "SELECT interesse FROM benutzer WHERE benutzer.nutzerID ='$userID'";
       
        $db_erg = mysqli_query( $con, $sql );
        $row = mysqli_fetch_row($db_erg);
        mysqli_free_result($db_erg); 

        databaseController::closeDatabaseConnection();

        return $row[0];

    }

    public static function getPersInfo($userID){
        
        
        //Nachname, Vorname, Alter, Groesse, Geschlecht, Beruf
        
        global $con;
        databaseController::createDatabaseConnection();
        
        $sql = "SELECT nachname,vorname,age,groesse,geschlecht,beruf FROM benutzer  WHERE benutzer.nutzerID='$userID'";
       
        $db_erg = mysqli_query( $con, $sql );
        $row = mysqli_fetch_row($db_erg);
        mysqli_free_result($db_erg); 

        databaseController::closeDatabaseConnection();
        /* Wenn Alter ausgegeben werden soll
        $from = new DateTime($row[2]);
        $to   = new DateTime('today');
        echo $from->diff($to)->y;
        */
       
        return ["nName" =>$row[0],
                "vName"=>$row[1],
                "age"=> $row[2],
                "groesse"=>$row[3],
                "gender"=>$row[4],
                "work"=>$row[5]];

    }

    public static function getKontaktdaten($userID){
        

        // Email, Land, Stadt, PLZ, TelNr

        global $con;
        databaseController::createDatabaseConnection();
        
        $sql = "SELECT email,land,ort,ort,telNr FROM benutzer WHERE ". "benutzer.nutzerID='$userID'";
       
        $db_erg = mysqli_query( $con, $sql );
        $row = mysqli_fetch_row($db_erg);
        mysqli_free_result($db_erg); 

        databaseController::closeDatabaseConnection();
       
        
        return ["email" =>$row[0],
                "land"=>$row[1],
                "ort"=>$row[2],
                "plz"=>$row[3],
                "telNr"=>$row[4]];
        
    }

    public static function loginUser($email, $password){
        
        
        databaseController::createDatabaseConnection();
        
        $sql = "SELECT benutzer.nutzerID FROM benutzer WHERE ". "benutzer.passwort='$password' AND benutzer.email='$email'";
        global $con;
        $db_erg = mysqli_query( $con, $sql );

        $row = mysqli_fetch_row($db_erg);
        mysqli_free_result($db_erg); 

        databaseController::closeDatabaseConnection();
        return $row[0];
    }

    public static function changeInteresse($userID, $interests)
    {
        databaseController::createDatabaseConnection();

        $sql = "UPDATE benutzer SET benutzer.Interesse='$interests' WHERE benutzer.nutzerID='$userID'";
        global $con;
        $db_erg = mysqli_query( $con, $sql );

        databaseController::closeDatabaseConnection();
        
        if (!$db_erg) {
            return false;;
        } else {
            return true;
        }
        
    }

    public static function changeKontaktdaten($userID, $email, $land, $ort, $plz, $telNr)
    {
        databaseController::createDatabaseConnection();

        $sql = "UPDATE benutzer SET ".
            "benutzer.email='$email', ".
            "benutzer.land='$land', ".
            "benutzer.ort='$ort', ".
            "benutzer.plz='$plz', ".
            "benutzer.telNr='$telNr' ".
            "WHERE benutzer.nutzerID='$userID'";
        
        global $con;
        $db_erg = mysqli_query( $con, $sql );

        databaseController::closeDatabaseConnection();
        
        if (!$db_erg) {
            return false;
        } else {
            return true;
        }
    }

    public static function changePassword($userID, $newPW1)
    {
               databaseController::createDatabaseConnection();

        $sql = "UPDATE benutzer SET ".
            "benutzer.passwort='$newPW1' ".
            "WHERE benutzer.nutzerID='$userID'";
        
        global $con;
        $db_erg = mysqli_query( $con, $sql );

        databaseController::closeDatabaseConnection();
        
        if (!$db_erg) {
            return false;;
        } else {
            return true;
        }
    }

    public static function changePersInfo($userID, $nName, $vName, $age, $groesse, $gender, $work)
    {
        databaseController::createDatabaseConnection();

        $sql = "UPDATE benutzer SET ".
            "benutzer.vorname='$vName',".
            "benutzer.nachname='$nName', ".
            "benutzer.age='$age', ".
            "benutzer.groesse='$groesse', ".
            "benutzer.beruf='$work', ".
            "benutzer.geschlecht='$gender' ".
            "WHERE benutzer.nutzerID='$userID'";
        
        global $con;
        $db_erg = mysqli_query( $con, $sql );

        databaseController::closeDatabaseConnection();
        
        if (!$db_erg) {
            return false;
        } else {
            return true;
        }
    }


}
?>