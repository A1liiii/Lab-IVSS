<?php

class Database {
    public static function connect() {
        $host = "localhost";
        $port = "5432"; 
        $user = "postgres"; 
        $pass = "258369";        
        $db   = "labIVSS";

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

