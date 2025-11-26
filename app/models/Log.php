<?php

require_once __DIR__ . '/../Config/database.php';

class Log {

    private $conn;

    public function __construct() {
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