<?php

require_once __DIR__ . '/../Config/database.php';

class User {

    private $conn;

    public function __construct() {
        // PDO Connection
        $this->conn = Database::connect();
    }

    public function count() {
        $sql = "SELECT COUNT(*) AS total FROM users";
        $stmt = $this->conn->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function countPending() {
        $sql = "SELECT COUNT(*) AS total FROM registrations WHERE status = 'pending'";
        $stmt = $this->conn->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
   }
}
