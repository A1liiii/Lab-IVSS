<?php

require_once __DIR__ . '/../Config/database.php';

class Log {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect(); // PDO
    }

    public function last($limit = 10) {
        $sql = "SELECT * FROM log_activity 
                ORDER BY waktu DESC 
                LIMIT :limit";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
