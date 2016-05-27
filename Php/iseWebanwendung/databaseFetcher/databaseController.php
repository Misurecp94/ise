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
        $sql = "INSERT INTO gruppe (G_Titel,G_Thema,Ersteller_ID) VALUES ".       
        "('$GTitel','$GThema','$userID')";
        
         if(mysqli_query( $con, $sql )){
             // insert in ist mitglied mit letzter id von gruppe
            $last_id = mysqli_insert_id($con);
             
            $sql = "INSERT INTO istmitglied (Benutzer_ID,Gruppen_ID) VALUES ('$userID','$last_id')";
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
    
    public static function getBeitraege($userID,$Gruppe){
        
       global $con;
        databaseController::createDatabaseConnection();
        
        $sql = "SELECT gruppe.G_Titel,gruppe.G_Thema FROM gruppe,istmitglied WHERE istmitglied.Benutzer_ID='$userID' AND istmitglied.Gruppen_ID=gruppe.Gruppen_ID";
        $db_erg = mysqli_query( $con, $sql );
        
        while($row = mysqli_fetch_assoc($db_erg)){
            $result[] = $row;
        }
        return $result;
    }
    
    
    
    public static function getGroupList($userID){
        
        global $con;
        databaseController::createDatabaseConnection();
        
        $sql = "SELECT gruppe.G_Titel,gruppe.G_Thema FROM gruppe,istmitglied WHERE istmitglied.Benutzer_ID='$userID' AND istmitglied.Gruppen_ID=gruppe.Gruppen_ID";
        $db_erg = mysqli_query( $con, $sql );
        
        while($row = mysqli_fetch_assoc($db_erg)){
            $result[] = $row;
        }
        return $result;
    }
    

    public static function changePic($userID, $pic){
        // ToDo: change pic, if success return true, else return false
        return false;
    }

    public static function getPic($userID){
        // ToDo Interessen zurueckgeben. If Kein Bild, Pfad -> "../pic/error.jpg" return

        return "../pic/error.jpg";
    }

    public static function getInteresse($userID){
       
        global $con;
        databaseController::createDatabaseConnection();
        
        $sql = "SELECT Interesse FROM benutzer,profil WHERE benutzer.Benutzer_ID ='$userID' AND ".
            "Profil.Benutzer_ID='$userID'";
       
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
        
        $sql = "SELECT Nachname,Vorname,Geburtstag,Groesse,Geschlecht,Beruf FROM profil,persoenlicheinformation  WHERE profil.Benutzer_ID='$userID'";
       
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
        
        $sql = "SELECT Email,Land,Ort,PLZ,TelNr FROM benutzer,profil,kontaktdaten WHERE ". "kontaktdaten.Benutzer_ID='$userID'";
       
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
        
        $sql = "SELECT benutzer.Benutzer_ID FROM benutzer,kontaktdaten WHERE ". "benutzer.passwort='$password' AND benutzer.Benutzer_ID=kontaktdaten.Kontaktdaten_ID AND ". 
        "kontaktdaten.Email='$email'";
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

        $sql = "UPDATE profil SET profil.Interesse='$interests' WHERE profil.Benutzer_ID='$userID'";
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

        $sql = "UPDATE kontaktdaten SET ".
            "kontaktdaten.Email='$email', ". 
            "kontaktdaten.Land='$land', ".
            "kontaktdaten.Ort='$ort', ".
            "kontaktdaten.PLZ='$plz', ".
            "kontaktdaten.TelNr='$telNr' ".
            "WHERE kontaktdaten.Benutzer_ID='$userID'";
        
        global $con;
        $db_erg = mysqli_query( $con, $sql );

        databaseController::closeDatabaseConnection();
        
        if (!$db_erg) {
            printf("Errormessage: %s\n", mysqli_error($link));
            return false;
        } else {
            return true;
        }
    }

    public static function changePassword($userID, $newPW1)
    {
               databaseController::createDatabaseConnection();

        $sql = "UPDATE benutzer SET ".
            "benutzer.Passwort='$newPW1' ". 
            "WHERE benutzer.benutzer_ID='$userID'";
        
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

        $sql = "UPDATE persoenlicheinformation SET ".
            "persoenlicheinformation.Vorname='$vName',". 
            "persoenlicheinformation.Nachname='$nName', ".
            "persoenlicheinformation.Geburtstag='$age', ".
            "persoenlicheinformation.Groesse='$groesse', ".
            "persoenlicheinformation.Beruf='$work', ".
            "persoenlicheinformation.Geschlecht='$gender' ".
            "WHERE persoenlicheinformation.Benutzer_ID='$userID'";
        
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