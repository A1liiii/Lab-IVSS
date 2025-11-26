<?php

require_once __DIR__ . '/../Config/database.php';

class Approval {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect(); // PDO
    }

    // Hitung semua pendaftar
    public function count() {
        $sql = "SELECT COUNT(*) AS total FROM registrations";
        $stmt = $this->conn->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Hitung pending approval
    public function countPending() {
        $sql = "SELECT COUNT(*) AS total FROM registrations WHERE status = 'pending'";
        $stmt = $this->conn->query($sql);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    // Ambil data pending
    public function getPending($limit = 10) {
        $sql = "SELECT * FROM registrations 
                WHERE status = 'pending'
                ORDER BY tanggal_daftar DESC
                LIMIT :limit";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Approve pendaftaran
    public function approve($reg_id, $approved_by) {
        $sql = "UPDATE registrations 
                SET status = 'approved',
                    approved_by = :approved_by,
                    approved_at = NOW()
                WHERE reg_id = :reg_id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            'approved_by' => $approved_by,
            'reg_id' => $reg_id
        ]);
    }
}
