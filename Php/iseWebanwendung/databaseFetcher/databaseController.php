<?php

class databaseController
{
    public function createDatabaseConnection(){
        // ToDo: create Database Connection
    }

    public function closeDatabaseConnection(){
        // ToDo: close DatabaseConnection
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