<?php

require_once __DIR__ . '/../Config/database.php';

class Dokumentasi {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect(); // PDO
    }

    public function count() {
        $sql = "SELECT COUNT(*) AS total FROM act_documentation";
        $stmt = $this->conn->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
