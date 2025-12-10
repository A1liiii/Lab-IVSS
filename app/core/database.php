<?php

class Database {
    public static function connect() {
        $host = "localhost";
        $port = "5432"; 
        $user = "postgres"; 
<<<<<<< HEAD
        $pass = "258369";        
        $db   = "labIVSS";
=======
        $pass = "1";        
        $db   = "labivss";
>>>>>>> df634fff9d713c444de86051229784fe5a2d57ec

        try {
            $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
            $conn = new PDO($dsn, $user, $pass);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            die("FAILED TO CONNECT DB: " . $e->getMessage());
        }
    }
}