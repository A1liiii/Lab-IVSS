<?php

require_once __DIR__ . '/../Config/database.php';

class Log {

    private $conn;

    public function __construct() {
<<<<<<< HEAD
<<<<<<< HEAD
        $this->conn = Database::connect();
=======
        $this->conn = Database::connect();  // PDO
>>>>>>> 7978cd1693c8bde036abea373cd14c4c5e5f1032
    }

    // Untuk dashboard (last 10 logs)
    public function last($limit = 10) {
        $sql = "SELECT * FROM log_activity ORDER BY waktu DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Untuk halaman admin-logs (semua log)
    public function getAll() {
        $sql = "SELECT 
                    log_activity.*, 
                    users.username
                FROM log_activity
                LEFT JOIN users 
                    ON users.user_id = log_activity.user_id
                ORDER BY waktu DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
=======
        $this->conn = Database::connect();  // PDO
    }

    // Untuk dashboard (last 10 logs)
    public function last($limit = 10) {
        $sql = "SELECT * FROM log_activity ORDER BY waktu DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Untuk halaman admin-logs (semua log)
    public function getAll() {
        $sql = "SELECT 
                    log_activity.*, 
                    users.username
                FROM log_activity
                LEFT JOIN users 
                    ON users.user_id = log_activity.user_id
                ORDER BY waktu DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
>>>>>>> 971395209f191832ab1f5edc129c8db792146eff
