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
        
        $sql = "SELECT Nachname,Vorname,Geburtstag,Groesse,Geschlecht,Beruf FROM profil,persoenlicheinformation  WHERE profil.Benutzer_ID='$userID' AND persoenlicheinformation.Profil_ID=profil.Profil_ID" ;
       
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
        
        $sql = "SELECT Email,Land,Ort,PLZ,TelNr FROM benutzer,profil,kontaktdaten WHERE ". "benutzer.Benutzer_ID='$userID' AND benutzer.Profil_ID=profil.Profil_ID AND ". "Profil.Kontaktdaten_ID = kontaktdaten.Kontaktdaten_ID";
       
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
        
        $sql = "SELECT benutzer.Benutzer_ID FROM benutzer,profil,kontaktdaten WHERE ". "benutzer.passwort='$password' AND benutzer.Profil_ID=profil.Profil_ID AND ". "Profil.Kontaktdaten_ID = kontaktdaten.Kontaktdaten_ID AND kontaktdaten.Email='$email'";
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
            "WHERE kontaktdaten.Profil_ID='$userID'";
        
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
            "WHERE persoenlicheinformation.Profil_ID='$userID'";
        
        global $con;
        $db_erg = mysqli_query( $con, $sql );

        databaseController::closeDatabaseConnection();
        
        if (!$db_erg) {
            return false;
        } else {
            return true;
        }
    }

    public static function getPersons($userID, $input)
    {
        // ToDo: Search for everyone with name or vorname (or both) like $input, store all people in an array (pic, Vorname, Nachname, userID, Boolean friendsWithUser .. per User) and return it.
        // Nothing found? return null
        return [[
            "pic" => "../pic/error.jpg",
            "vName" => "Tim",
            "nName" => "Muster",
            "userID" => 1,
            "isFriend" => true
        ],[
            "pic" => "../pic/error.jpg",
            "vName" => "Lisa",
            "nName" => "Musterfrau",
            "userID" => 2,
            "isFriend" => false
        ]];
    }

    public static function addFriend($userID, $friendAdd)
    {
        // ToDo freund mit ID $friendAdd und user mit id $userID befreunden!
    }

    public static function getFriends($userID)
    {
        // ToDO: get all Friends of user w/ $userID and return it. No friends => return null;
        // ToDO: return pic, vorname, nachname, userID und Unterhaltung vorhanden boolean (true false)

        return [[
            "pic" => "../pic/error.jpg",
            "vName" => "Tim",
            "nName" => "Muster",
            "userID" => 1,
            "unterhaltung" => true
            ],[
           "pic" => "../pic/error.jpg",
            "vName" => "Tom",
            "nName" => "Turbo",
            "userID" => 3,
            "unterhaltung" => false
        ]];
    }


}
?>