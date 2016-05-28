<?php





            $servername = 'localhost';
            $username = 'root';
            $password = '';
            $dbname= 'kis';
        
        $con = mysqli_connect($servername, $username, $password, $dbname);
        
        $sql = "SELECT benutzer.Benutzer_ID FROM benutzer,profil,kontaktdaten WHERE ". "benutzer.passwort='1234' AND benutzer.Profil_ID=profil.Profil_ID AND ". "Profil.Kontaktdaten_ID"." = kontaktdaten.Kontaktdaten_ID AND kontaktdaten.Email='123@123.at'";
       
        $db_erg = mysqli_query( $con, $sql );
        $row = mysqli_fetch_row($db_erg);
        mysqli_free_result($db_erg); 

        
        echo $row[0];



?>