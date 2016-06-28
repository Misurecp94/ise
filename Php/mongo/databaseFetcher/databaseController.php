<?php

class databaseController
{
            
    public $m = null;
    public $db = null;
    public $coll_benutzer = null;
    public $coll_gruppe = null;
    public $coll_nachrichten = null;
    public $coll_veranstaltungen =null;

    
    public static function createDatabaseConnection(){
       
        $m = new MongoClient();
            $db = $m->ise;
        
            global $coll_benutzer;
            $coll_benutzer = $db->benutzer;
        
            global $coll_gruppe;
            $coll_gruppe = $db->gruppe;
        
            global $coll_nachrichten;
            $coll_nachrichten = $db->nachrichten;
        
            global $coll_veranstaltungen;
            $coll_veranstaltungen = $db->veranstaltungen;

    }

    public static function closeDatabaseConnection(){
        global $m;
		if($m){
			$m->close();
		}		
    }

    public static function addFriend($userID,$otherID){

		databaseController::createDatabaseConnection();
        global $coll_benutzer;
        $cursor = $coll_benutzer->findOne(array("_id" => $userID));
		$friend = array('nutzerID'=>$otherID);
        $coll_benutzer->update(array('_id'=>$userID), array('$push'=>array('FRIENDS'=>$friend)));
		
		$cursor = $coll_benutzer->findOne(array("_id" =>$otherID));
		$friend = array('nutzerID'=>$userID);
		$coll_benutzer->update(array('_id'=>$otherID), array('$push' => array('FRIENDS'=>$friend)));
		
        databaseController::closeDatabaseConnection();
    }

    public static function sendMessage($userID, $unterhaltungsID, $message)
    {
          if(empty($message)){
            return;
        }

        databaseController::createDatabaseConnection();
        global $coll_nachrichten;
        try{
            
            $cursor= $coll_nachrichten->findOne(array("_id"=>$unterhaltungsID), array('NUTZERID1','NUTZERID2'));
            $otherID=null;
            if($cursor['NUTZERID1']==$userID){
                $otherID=$cursor['NUTZERID2'];
            }else{
                $otherID=$cursor['NUTZERID1'];
            }

            $id= new MongoID();
            $Nachricht = array("_id" =>(string) $id , "EMPFAENGERID" => $otherID, "ERSTELLZEITPUNKT"=>date("Y-m-d H:i:s.I"),"NINHALT"=>$message,"ERSTELLERID"=>$userID);
            $coll_nachrichten->update( array("_id"=>$unterhaltungsID), array('$push'=>array("NACHRICHTEN"=>$Nachricht)));
            databaseController::closeDatabaseConnection();
                                              
        }catch(MongoCursorException $e) {
				databaseController::closeDatabaseConnection();
                return false;
        }
        return true;

    }

    public static function addUnterhaltung($userID, $otherID)
    {
        databaseController::createDatabaseConnection();
        global $coll_nachrichten;
        
        
        try{
            $id= new MongoID();
            $unterhaltung = array("_id" =>(string) $id , "UBEGINNZEITPUNKT" =>date("Y-m-d H:i:s.I") , "NUTZERID1"=>$userID, "NUTZERID2"=>$otherID, "NACHRICHTEN"=>array());
            $coll_nachrichten->insert($unterhaltung);
        }catch(MongoCursorException $e) {
				databaseController::closeDatabaseConnection();
                return false;
            }
		return true;
    }
    
    public static function isFriend($userID, $otherID){
		databaseController::createDatabaseConnection();
        global $coll_benutzer;	
		$cursor = $coll_benutzer->findOne(array("_id" => $userID));
            foreach((array)$cursor['FRIENDS'] as $friend){
                if($friend['nutzerID'] == $otherID){
					databaseController::closeDatabaseConnection();
					return 1;
				}
           }
		databaseController::closeDatabaseConnection();
		return 0;
    }

    public static function isBanned($otherID){

		databaseController::createDatabaseConnection();
        global $coll_benutzer;
        $cursor = $coll_benutzer->findOne(array("_id" => $otherID), array("SPERRE"=>1));
		databaseController::closeDatabaseConnection();
        if($cursor['SPERRE']==null){
            return false;
        }else{
            return "1";
        }
       

    }
    
    public static function convStarted($userID, $otherID)
    {
		
		databaseController::createDatabaseConnection();
        global $coll_nachrichten;
        
        $cursor = $coll_nachrichten->findOne(array('$or'=>array(array("NUTZERID1" => $userID,"NUTZERID2"=>$otherID),array("NUTZERID1"=>$otherID,"NUTZERID2"=>$userID))));
        
        if(empty($cursor)){
            return 0;
        }else{
            return 1;
        }

    }

    public static function searchUser($search, $userID){
		databaseController::createDatabaseConnection();
        global $coll_benutzer;
        $cursor = $coll_benutzer->findOne(array('_id' => $userID), array('BENUTZERBILD'));
		
		$searchQuery = array(
			'$and' => array(
				array('$or' => array(
					array(
						'VORNAME' => new MongoRegex("/^$search/i"),
						),
					array(
						'NACHNAME' => new MongoRegex("/^$search/i"),
						),
					array(
						'EMAIL' => new MongoRegex("/^$search/i"),
						)
				)),
				array('_id' => array('$ne' => $userID))
			)
            );
		$cursor = $coll_benutzer->find($searchQuery);
		databaseController::closeDatabaseConnection();
		$result=[];
		foreach($cursor as $doc){
            $result[]=$doc;
        }
		return $result;
    }
    
    public static function createGroup($userID, $GTitel, $GThema){
        databaseController::createDatabaseConnection();
        global $coll_gruppe;

        $id= new MongoID();
        $group = array("_id" =>(string) $id , "GTITEL" => $GTitel, "GTHEMA"=>$GThema,"GERSTELLUNGSDATUM"=> date("Y-m-d H:i:s.I"),"NUTZERID"=>$userID,"MITGLIEDER"=>array(array("NUTZERID"=>$userID)),"BEITRAEGE"=>array());
        $coll_gruppe->insert($group);
		databaseController::closeDatabaseConnection();
		return true;
    }

    public static function addUserToDatabase($inputEmail, $inputPassword)
    {
		databaseController::createDatabaseConnection();
        global $coll_benutzer;

        
        $cursor=$coll_benutzer->findOne(array("EMAIL"=>$inputEmail));
        if(!empty($cursor)){
            return false;
		 }
        
        
        try{
            $id= new MongoID();
            $benutzer = array("_id" =>(string) $id , "PASSWORT" => $inputPassword, "INTERESSE"=>"", "BENUTZERBILD"=>"", "LAND"=>"","EMAIL"=>$inputEmail, "TELNR"=>"", "PLZ"=>"","ORT"=>"", "VORNAME"=>"","NACHNAME"=>"","AGE"=>"","GROESSE"=>"", "GESCHLECHT"=>"","BERUF"=>"", "SPERRE"=>null, "FRIENDS"=>array(), "SPERRE"=>null, "ADMIN"=>"false");
            $coll_benutzer->update(array("EMAIL"=>$inputEmail),$benutzer,array("upsert"=>true));
        }catch(MongoCursorException $e) {
				databaseController::closeDatabaseConnection();
                return false;
            }
		return true;
    }
    
    public static function addUserToGroup($Email,$group){
        databaseController::createDatabaseConnection();
        global $coll_benutzer;
        global $coll_gruppe;
        $cursor = $coll_benutzer->findOne(array("EMAIL" => $Email));
        
        if($cursor['_id']!=null){
        $mitglied = array("NUTZERID" =>$cursor['_id']);
            
        $coll_gruppe->update(array("_id"=>$group),array('$push'=>array("MITGLIEDER"=>$mitglied)));
            
        }
        databaseController::closeDatabaseConnection();
    }
    
    public static function createBeitrag($userID, $BTitel, $BInhalt,$group){
        databaseController::createDatabaseConnection();
        global $coll_gruppe;
        
        $id= new MongoID();
        $beitrag = array("_id" =>(string) $id , "BTITEL" => $BTitel, "BINHALT"=>$BInhalt,"NUTZERID"=>$userID);
        $coll_gruppe->update(array("_id"=>$group),array('$push'=>array("BEITRAEGE"=>$beitrag)));
    }
    
    public static function getEventList($userID)
    {
		databaseController::createDatabaseConnection();
        global $coll_veranstaltungen;
        $cursor = $coll_veranstaltungen->find(array("NUTZERID" => $userID),array('VNAME'=>1,'VTITEL'=>1,'_id'=>1,'VBESCHREIBUNG'=>1,'VDATUM'=>1));
        
        $result=[];
        foreach($cursor as $doc){
            $result[]=$doc;
        }
		databaseController::closeDatabaseConnection();
        return $result;  
    }
    
    public static function updateEventStatus($userID,$eventID,$status)
    {
        databaseController::createDatabaseConnection();
        global $coll_veranstaltungen;
        $newdata = array('$set' => array("EINLADUNGEN.$.STATUS" => $status));
        
        $cursor = $coll_veranstaltungen->update(array('_id' =>$eventID, "EINLADUNGEN.NUTZERID"=>$userID),$newdata);
		databaseController::closeDatabaseConnection();
    }
    
    public static function getOpenEventList($userID)
    {
        databaseController::createDatabaseConnection();
        global $coll_veranstaltungen;
        
        $cursor=$coll_veranstaltungen->find(array("EINLADUNGEN"=>array('$elemMatch'=>array("NUTZERID"=>$userID,"STATUS"=>'Ausstehend'))),array('EINLADUNGEN'=>1,'VNAME'=>1,'VTITEL'=>1,'_id'=>1,'VBESCHREIBUNG'=>1,'VDATUM'=>1));
        
        
        
        $result=[];
        foreach($cursor as $doc){
            $result[]=$doc;
        }
		databaseController::closeDatabaseConnection();
        return $result;
    }
    
    public static function getClosedEventList($userID)
    {
        databaseController::createDatabaseConnection();
        global $coll_veranstaltungen;
        
        $cursor=$coll_veranstaltungen->find(array("EINLADUNGEN"=>array('$elemMatch'=>array("NUTZERID"=>$userID,"STATUS"=>array('$ne'=>'Ausstehend')))),array('EINLADUNGEN'=>1,'VNAME'=>1,'VTITEL'=>1,'_id'=>1,'VBESCHREIBUNG'=>1,'VDATUM'=>1));
        
        $result=[];
        $status=[];
        foreach($cursor as $doc){
            // Status einzeln anhängen
            $status['VNAME']=$doc['VNAME'];
            $status['VTITEL']=$doc['VTITEL'];
            $status['VBESCHREIBUNG']=$doc['VBESCHREIBUNG'];
            $status['VDATUM']=$doc['VDATUM'];
            foreach($doc['EINLADUNGEN'] as $invite){
                if($invite['NUTZERID']==$userID){
                    $status['STATUS']=$invite['STATUS'];
                }
            }
            $result[]=$status;
        }
		databaseController::closeDatabaseConnection();
        return $result; 
    }
    
    public static function addGuest($email, $eventID){
        databaseController::createDatabaseConnection();
        global $coll_benutzer;
        global $coll_veranstaltungen;
        $cursor = $coll_benutzer->findOne(array("EMAIL" => $email));
        
        if($cursor['_id']!=null){
            try {
                $invite = array("NUTZERID" =>$cursor['_id'],"STATUS"=>'Ausstehend');
            
                $coll_veranstaltungen->update(array("_id"=>$eventID),
                                    array('$push'=>array("EINLADUNGEN"=>$invite)));
				databaseController::closeDatabaseConnection();
                return true;
            }catch(MongoCursorException $e) {
				databaseController::closeDatabaseConnection();
                return false;
            }
            
        }else{
			databaseController::closeDatabaseConnection();
            return false;
        }    
    }
    
    public static function getGuestList($eventID)
    {
        
        databaseController::createDatabaseConnection();
        global $coll_veranstaltungen;
        global $coll_benutzer;
        $cursor = $coll_veranstaltungen->findOne(array("_id" => $eventID),array('EINLADUNGEN'=>1));
        
        $result = [];
            foreach((array) $cursor['EINLADUNGEN'] as $invites){
                $status = []; 
                $cursor_freund =$coll_benutzer->findOne(array("_id"=>$invites['NUTZERID']),
                                                      array('VORNAME','NACHNAME','EMAIL','_id'));
                $cursor_freund['STATUS']=$invites['STATUS'];
                $result[]=$cursor_freund;
                
        }
		databaseController::closeDatabaseConnection();
        return $result;
    }
    
    public static function createEvent($userID,$Name,$Datum,$Beschreibung,$Titel){
       $date = str_replace('.', '-', $Datum);
        
        databaseController::createDatabaseConnection();
        global $coll_veranstaltungen;
        
        $id= new MongoID();
        $veranstaltung = array("_id" =>(string) $id , "VNAME" => $Name,"VDATUM"=>$date, "VBESCHREIBUNG"=>$Beschreibung,"VTITEL"=>$Titel,"NUTZERID"=>$userID,"EINLADUNGEN"=>array(array("NUTZERID"=>$userID,"STATUS"=>'Teilnahme')));
        $coll_veranstaltungen->insert($veranstaltung);
		databaseController::closeDatabaseConnection();
		return true;
    }
    
    public static function getFriends($userID)
    {
        databaseController::createDatabaseConnection();
        global $coll_benutzer;
        $cursor = $coll_benutzer->find(array("_id" => $userID));
        $result = [];
        foreach ($cursor as $friends){
            foreach($friends['FRIENDS'] as $freund){
                $cursor_freund =$coll_benutzer->findOne(array("_id"=>$freund['nutzerID']),
                                                      array('VORNAME','NACHNAME','EMAIL','_id'));
                 $result[]=$cursor_freund;
           }
        }
		databaseController::closeDatabaseConnection();
        return $result;
    }

    public static function getUnterhaltungen($userID)
    {
		
		databaseController::createDatabaseConnection();
        global $coll_benutzer;
        global $coll_nachrichten;
		$result = [];
		
		$cursor = $coll_nachrichten->find();
		foreach ( $cursor as $id => $value )
		{
			if(isset($value['NUTZERID1'])){
				if($value['NUTZERID1'] == $userID){
					$help = $coll_benutzer->findOne(array("_id"=>$value["NUTZERID2"]));
					array_push($result,[
						"unterhaltungsID"=>$id,
						"vorname"=>$help["VORNAME"],
						"nachname"=>$help["NACHNAME"]
					]);
				}
			}
			if(isset($value['NUTZERID2'])){
				if($value["NUTZERID2"] == $userID){
					$help = $coll_benutzer->findOne(array("_id"=>$value["NUTZERID1"]));
					array_push($result,[
						"unterhaltungsID"=>$id,
						"vorname"=>$help["VORNAME"],
						"nachname"=>$help["NACHNAME"]
					]);
				}
			}
		}

		databaseController::closeDatabaseConnection();
		return $result;

    }

 public static function getNachrichten($userID, $unterhaltung){
        
        databaseController::createDatabaseConnection();
        global $coll_benutzer;
        global $coll_nachrichten;
        $cursor = $coll_nachrichten->findOne(array("_id" => $unterhaltung));
        $result = [];
            foreach((array)$cursor['NACHRICHTEN'] as $nachricht){
                $cursor_ersteller =$coll_benutzer->findOne(array("_id"=>$nachricht['ERSTELLERID']),
                                                      array('VORNAME','NACHNAME'));
                $result[]=array_merge($nachricht,$cursor_ersteller);
           }
		databaseController::closeDatabaseConnection();
        return $result;
        
    }

    public static function getBeitraege($gruppe){
        
       databaseController::createDatabaseConnection();
        global $coll_benutzer;
        global $coll_gruppe;
        $cursor = $coll_gruppe->findOne(array("_id" => $gruppe));
        $result = [];
            foreach((array)$cursor['BEITRAEGE'] as $beitrag){
                $cursor_ersteller =$coll_benutzer->findOne(array("_id"=>$beitrag['NUTZERID']),
                                                      array('VORNAME','NACHNAME'));
                $result[]=array_merge($beitrag,$cursor_ersteller);
           }
		databaseController::closeDatabaseConnection();
        return $result;
    }
    
    public static function getGroupList($userID){
        
       databaseController::createDatabaseConnection();
        global $coll_gruppe;
        $cursor = $coll_gruppe->find(array("MITGLIEDER.NUTZERID" => $userID),array('GTITEL'=>1,'GTHEMA'=>1,'_id'=>1));
        
        $result=[];
        foreach($cursor as $doc){
            $result[]=$doc;
        }
		databaseController::closeDatabaseConnection();
        return $result;
    }
    
    public static function getMitglieder($gruppe){
        databaseController::createDatabaseConnection();
        global $coll_benutzer;
        global $coll_gruppe;
        $cursor = $coll_gruppe->findOne(array("_id" => $gruppe));
        $result = [];
            foreach((array)$cursor['MITGLIEDER'] as $mitglied){
                $cursor_mitglied =$coll_benutzer->findOne(array("_id"=>$mitglied['NUTZERID']),
                                                      array('VORNAME','NACHNAME'));
                 $result[]=$cursor_mitglied;
           }
		databaseController::closeDatabaseConnection();
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
				global $coll_benutzer;
				$nosqlhelper = $target_dir . basename($pic[name]);
				$newdata = array('$set' => array("BENUTZERBILD" => $nosqlhelper));
				$cursor = $coll_benutzer->update(array('_id' => $userID),$newdata);

                databaseController::closeDatabaseConnection();
				return true;
            } else {
                // Some error ocurred
                return false;
            }
        }
        // Falls irgendwas schief geht:
    }

    public static function getPic($userID){
        databaseController::createDatabaseConnection();
        global $coll_benutzer;
        $cursor = $coll_benutzer->findOne(array('_id' => $userID), array('BENUTZERBILD'));
		databaseController::closeDatabaseConnection();
        if($cursor['BENUTZERBILD'] != null){
            return $cursor['BENUTZERBILD'];
        } else {
            return "../pic/error.jpg";
        }
    }

    public static function getInteresse($userID){
       
        databaseController::createDatabaseConnection();
        global $coll_benutzer;
        $cursor = $coll_benutzer->findOne(array('_id' => $userID), array('INTERESSE'));
		databaseController::closeDatabaseConnection();
        return $cursor['INTERESSE'];

    }

    public static function getPersInfo($userID){
        
        
        //Nachname, Vorname, Alter, Groesse, Geschlecht, Beruf
        
         databaseController::createDatabaseConnection();
        global $coll_benutzer;
        $cursor = $coll_benutzer->findOne(array("_id" => $userID), array('NACHNAME','VORNAME','AGE','GROESSE','GESCHLECHT','BERUF'));
		databaseController::closeDatabaseConnection();
        return ["nName" =>$cursor['NACHNAME'],
                "vName"=>$cursor['VORNAME'],
                "age"=> $cursor['AGE'],
                "groesse"=>$cursor['GROESSE'],
                "gender"=>$cursor['GESCHLECHT'],
                "work"=>$cursor['BERUF']];

    }

    public static function getKontaktdaten($userID){
        

        // Email, Land, Stadt, PLZ, TelNr

        databaseController::createDatabaseConnection();
        global $coll_benutzer;
        $cursor = $coll_benutzer->findOne(array('_id' => $userID), array('EMAIL','LAND','ORT','PLZ','TELNR'));
		databaseController::closeDatabaseConnection();
        return ["email" =>$cursor['EMAIL'],
                "land"=>$cursor['LAND'],
                "ort"=>$cursor['ORT'],
                "plz"=>$cursor['PLZ'],
                "telNr"=>$cursor['TELNR']];
        
    }

    public static function loginUser($email, $password){ 
        databaseController::createDatabaseConnection();
        global $coll_benutzer;
        $cursor = $coll_benutzer->findOne(array('EMAIL' => $email, 'PASSWORT' => $password));
	    databaseController::closeDatabaseConnection();
        return $cursor['_id'];
    }

    public static function isAdmin($userID)
    {
        databaseController::createDatabaseConnection();
        global $coll_benutzer;
        $cursor = $coll_benutzer->findOne(array("_id" => $userID), array("ADMIN"=>1));
		databaseController::closeDatabaseConnection();
        if($cursor['ADMIN']=="false"){
            return null;
        }else{
            return "1";
        }
    }

    public static function makeAdmin($otherID){
        
        databaseController::createDatabaseConnection();
        global $coll_benutzer;
        $newdata = array('$set' => array("ADMIN" => "true"));
        $cursor = $coll_benutzer->update(array('_id' => $otherID),$newdata);
		databaseController::closeDatabaseConnection();
        
    }
    
    public static function blockUser($otherID){
        databaseController::createDatabaseConnection();
        global $coll_benutzer;
        $newdata = array('$set' => array("SPERRE" => "1"));
        $cursor = $coll_benutzer->update(array('_id' => $otherID),$newdata);
        databaseController::closeDatabaseConnection();
    }

    public static function changeInteresse($userID, $interests)
    {
        databaseController::createDatabaseConnection();
        global $coll_benutzer;
        $newdata = array('$set' => array("INTERESSE" => $interests));
        $cursor = $coll_benutzer->update(array('_id' => $userID),$newdata);
        databaseController::closeDatabaseConnection();
    }

    public static function changeKontaktdaten($userID, $email, $land, $ort, $plz, $telNr)
    {
		databaseController::createDatabaseConnection();
        global $coll_benutzer;
        
        $cursor = $coll_benutzer->findOne(array("EMAIL"=>$email));
        if(!empty(cursor)){
            $newdata = array('$set' => array(
                                            "LAND"=>$land,
                                             "ORT"=>$ort,
                                             "PLZ"=>$plz,
                                             "TELNR"=>$telNr
                                            ));
            $cursor = $coll_benutzer->update(array('_id' => $userID),$newdata);
        }else{
            $newdata = array('$set' => array("EMAIL" => $email,
                                            "LAND"=>$land,
                                             "ORT"=>$ort,
                                             "PLZ"=>$plz,
                                             "TELNR"=>$telNr
                                            ));
            $cursor = $coll_benutzer->update(array('_id' => $userID),$newdata);
        }
		databaseController::closeDatabaseConnection();
    }

    public static function changePassword($userID, $newPW1)
    {
		databaseController::createDatabaseConnection();
        global $coll_benutzer;
        $newdata = array('$set' => array("PASSWORT" => $newPW1
                                        ));
        $cursor = $coll_benutzer->update(array('_id' => $userID),$newdata);
		databaseController::closeDatabaseConnection();
		return true;
    }

    public static function changePersInfo($userID, $nName, $vName, $age, $groesse, $gender, $work)
    {
        databaseController::createDatabaseConnection();
        global $coll_benutzer;
        $newdata = array('$set' => array("VORNAME" => $vName,
                                        "NACHNAME"=>$nName,
                                         "AGE"=>$age,
                                         "GROESSE"=>$groesse,
                                         "BERUF"=>$work,
                                         "GESCHLECHT"=>$gender
                                        ));
        $cursor = $coll_benutzer->update(array('_id' => $userID),$newdata);
		databaseController::closeDatabaseConnection();
    }


}
?>