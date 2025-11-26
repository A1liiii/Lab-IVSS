<?php

require_once __DIR__ . '/../Config/database.php';

class Mahasiswa {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function count() {
        $sql = "SELECT COUNT(*) AS total FROM mahasiswa";
        $result = $this->conn->query($sql);
        return $result->fetch_assoc()['total'];
    }
}
?>
