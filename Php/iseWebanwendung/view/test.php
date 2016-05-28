<?php





            $servername = 'localhost';
            $username = 'root';
            $password = '';
            $dbname= 'kis';
        
        $con = mysqli_connect($servername, $username, $password, $dbname);
        
        $sql = "SELECT benutzer.nutzerID FROM benutzer WHERE ". "benutzer.passwort='1234' AND benutzer.email='test1@test.com'";
       
        $db_erg = mysqli_query( $con, $sql );
        $row = mysqli_fetch_row($db_erg);
        mysqli_free_result($db_erg); 

        
        echo $row[0];



?>