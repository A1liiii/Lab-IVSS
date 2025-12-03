<?php

require_once __DIR__ . '/../Config/database.php';

class Role {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    // ambil semua role dari tabel roles
    public function getAll() {
        $sql = "SELECT * FROM roles ORDER BY role_id ASC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ambil satu role berdasarkan id
    public function find($role_id) {
        $stmt = $this->conn->prepare("SELECT * FROM roles WHERE role_id = :id");
        $stmt->execute([':id' => $role_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
