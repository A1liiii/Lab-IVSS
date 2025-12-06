<?php

require_once __DIR__ . '/../Config/database.php';

class Log {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect();  // PDO
    }

    // Tambah log
    public function add($user_id, $deskripsi, $aksi) {
        $sql = "INSERT INTO log_activity (user_id, deskripsi, aksi) 
                VALUES (:user_id, :deskripsi, :aksi)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':user_id'   => $user_id,
            ':deskripsi' => $deskripsi,
            ':aksi'      => $aksi
        ]);
    }

    // Ambil last logs (bisa untuk dashboard & admin)
    public function last($limit = 10) {
        $sql = "SELECT la.*, u.username
                FROM log_activity la
                LEFT JOIN users u ON la.user_id = u.user_id
                ORDER BY la.waktu DESC
                LIMIT :limit";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Versi formatted (untuk debugging / export)
    public function getLogsFormatted($limit = 20) {
        $logs = $this->last($limit);
        $formatted = [];

        foreach ($logs as $l) {
            $formatted[] = "{$l['aksi']} - {$l['deskripsi']} {$l['waktu']} ({$l['username']})";
        }

        return $formatted;
    }  

    public function recent($limit = 10) {
        $sql = "SELECT log_activity.*, users.username
                FROM log_activity
                LEFT JOIN users ON users.user_id = log_activity.user_id
                ORDER BY waktu DESC
                LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }    

        // Ambil semua log (untuk admin)
    public function getAll() {
        $sql = "SELECT la.*, u.username
                FROM log_activity la
                LEFT JOIN users u ON la.user_id = u.user_id
                ORDER BY la.waktu DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Recent activity by user
    public function recentByUser($user_id, $limit = 10) {
        $sql = "SELECT * FROM log_activity 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT $limit";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
