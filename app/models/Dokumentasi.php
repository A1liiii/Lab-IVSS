<?php

require_once __DIR__ . '/../Config/database.php';

class Dokumentasi {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function count() {
        $sql = "SELECT COUNT(*) AS total FROM act_documentation";
        return $this->conn->query($sql)->fetch_assoc()['total'];
    }
}
