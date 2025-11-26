<?php

require_once __DIR__ . '/../Config/database.php';

class Log {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function last($limit = 10) {
        $sql = "SELECT * FROM log_activity ORDER BY created_at DESC LIMIT $limit";
        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }
}
