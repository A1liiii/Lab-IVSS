<?php

class Database {
    public static function connect() {

        $host = "localhost";
        $port = "5432";              // default PostgreSQL port
        $user = "postgres";          // default user
        $pass = "12345678";          // your actual PostgreSQL password
        $db   = "labivss";           // use lowercase unless created quoted

        $conn_string = "host=$host port=$port dbname=$db user=$user password=$pass";

        $conn = pg_connect($conn_string);

        if (!$conn) {
            die("FAILED TO CONNECT DB: " . pg_last_error());
        }

        return $conn;
    }
}
