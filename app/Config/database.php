<?php

class Database {
    public static function connect() {
        $host = "localhost";
        $user = "root";
        $pass = "";
        $db   = "labIVSS";

        $conn = new mysqli($host, $user, $pass, $db);

        if ($conn->connect_error) {
            die("FAILED TO CONNECT DB: " . $conn->connect_error);
        }

        return $conn;
    }
}
