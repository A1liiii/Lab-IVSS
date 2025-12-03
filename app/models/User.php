<?php 

require_once __DIR__ . '/../Config/database.php';

class User {

    private $conn;

    public function __construct() {
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

    // Ambil semua user untuk dropdown anggota proyek
    public function getAll()
    {
        $sql = "SELECT 
                    user_id,
                    username AS nama,
                    nip,
                    nim
                FROM users
                ORDER BY username ASC";

        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil detail user berdasarkan ID
    public function find($id) {
        $stmt = $this->conn->prepare("
            SELECT user_id, 
                   COALESCE(nip, nim) AS identitas,
                   username
            FROM users 
            WHERE user_id = :id
        ");

        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


}

