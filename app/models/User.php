<?php

require_once __DIR__ . '/../Config/database.php';

class User {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function count() {
        $sql = "SELECT COUNT(*) AS total FROM users";
        return $this->conn->query($sql)->fetch_assoc()['total'];
    }

    public function countPending() {
        $sql = "SELECT COUNT(*) AS total FROM users WHERE status = 'pending'";
        return $this->conn->query($sql)->fetch_assoc()['total'];
    }
}
