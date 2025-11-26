<?php

class Database {
    public static function connect() {
        $host = "localhost";
        $port = "5432";
        $user = "postgres";   // default user PostgreSQL
        $pass = "258369";  // ganti dengan password pgAdmin kamu
        $db   = "labIVSS";

        $dsn = "pgsql:host=$host;port=$port;dbname=$db;";

        try {
            $conn = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            return $conn;

        } catch (PDOException $e) {
            die("FAILED TO CONNECT DB: " . $e->getMessage());
        }
    }
}