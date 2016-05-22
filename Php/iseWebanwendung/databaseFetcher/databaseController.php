<?php

class databaseController
{
    public function createDatabaseConnection(){
        // ToDo: create Database Connection
    }

    public function closeDatabaseConnection(){
        // ToDo: close DatabaseConnection
    }

    public static function getPic($userID){
        // ToDo Interessen zurueckgeben. Kein Bild, Pfad -> "../pic/error.jpg" return

        return "../pic/error.jpg";
    }

    public static function getInteresse($userID){
        // Todo Bild zurückgeben. Kein Interesse -> "" zurückgeben

        return "Schwimmen, laufen usw";
    }

    public static function getPersInfo($userID){
        //ToDo get all Info and return it. Leere Felder mit ""
        //Reihenfolge: Nachname, Vorname, Alter, Groesse, Geschlecht, Beruf

        return ["nName"=>"Mustermann" , "vName"=>"Max" , "age"=>"18", "groesse"=>"180", "gender"=>"Male", "work"=>"Student"];
    }

    public static function getKontaktdaten($userID){
        // ToDo get all Info, and return it. Alle leeren Felder mit "" zurückgeben
        // Rückgabe (in Reihenfolge): Email, Land, Stadt, PLZ, TelNr

        return ["email" =>"email@test.com",
                "land"=>"Austria",
                "ort"=>"Vienna",
                "plz"=> "1030",
                "telNr"=>"06500005550"];
    }

    public static function addUserToDatabase($email, $password) {
        databaseController::createDatabaseConnection();
        /**
         * ToDO: try to Register a new User with email and password. Wenns klappt --> return userID aus der Datenbank, sonst return "null"
         */
        databaseController::closeDatabaseConnection();
        return 1;
    }

    public static function loginUser($email, $password){
        databaseController::createDatabaseConnection();
        /**
         * ToDo: try to Login a User with email and password. Wenns klappt --> return userID aus der Datenbank, sonst return "null";
         */
        databaseController::closeDatabaseConnection();
        return 1;
    }



}
?>