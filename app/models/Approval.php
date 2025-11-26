<?php

require_once __DIR__ . '/../Config/database.php';

class Approval {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    // Hitung semua pendaftar
    public function count() {
        $sql = "SELECT COUNT(*) AS total FROM registrations";
        return $this->conn->query($sql)->fetch_assoc()['total'];
    }

    // Hitung pending approval
    public function countPending() {
        $sql = "SELECT COUNT(*) AS total FROM registrations WHERE status = 'pending'";
        return $this->conn->query($sql)->fetch_assoc()['total'];
    }

    // Ambil data pending untuk ditampilkan ke ketua lab
    public function getPending($limit = 10) {
        $sql = "SELECT * FROM registrations 
                WHERE status = 'pending'
                ORDER BY tanggal_daftar DESC
                LIMIT $limit";

        return $this->conn->query($sql)->fetch_all(MYSQLI_ASSOC);
    }

    // Approve pendaftaran
    public function approve($reg_id, $approved_by) {
        $sql = "UPDATE registrations 
                SET status = 'approved',
                    approved_by = $approved_by,
                    approved_at = NOW()
                WHERE reg_id = $reg_id";

        return $this->conn->query($sql);
    }
}
