<?php

require_once __DIR__ . '/../Config/database.php';

class Log {

    private $conn;

    public function __construct() {
        // PDO Connection
        $this->conn = Database::connect();
    }

    // Ambil log terbaru dengan limit default 10
    //public function last($limit = 10) {
        //$sql = "SELECT * FROM log_activity ORDER BY created_at DESC LIMIT :limit";

        //$stmt = $this->conn->prepare($sql);
        //$stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        //$stmt->execute();

        //return $stmt->fetchAll(PDO::FETCH_ASSOC);
   // }
}
