<?php

require_once __DIR__ . '/../Config/database.php';

class Dokumentasi {

    private $conn;

    public function __construct() {
<<<<<<< HEAD
        $this->conn = Database::connect();
=======
        $this->conn = Database::connect(); // PDO
>>>>>>> 971395209f191832ab1f5edc129c8db792146eff
    }

    public function count() {
        $sql = "SELECT COUNT(*) AS total FROM act_documentation";
<<<<<<< HEAD
        return $this->conn->query($sql)->fetch_assoc()['total'];
=======
        $stmt = $this->conn->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
>>>>>>> 971395209f191832ab1f5edc129c8db792146eff
    }
}
