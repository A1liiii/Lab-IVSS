<?php

<<<<<<< HEAD
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
=======
class Approval {

    private $db;

    public function __construct() {
        $this->db = new PDO(
            "pgsql:host=localhost;port=5432;dbname=labIVSS",
            "postgres",
            "258369"
        );
    }

    public function getPending() {
       $stmt = $this->db->query("
        SELECT r.*, u.username AS approved_by_username
        FROM registrations r
        LEFT JOIN users u ON r.approved_by = u.user_id
        ORDER BY r.reg_id ASC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM registrations WHERE reg_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

   public function setApproved($id, $approvedByUserId) {
    $stmt = $this->db->prepare(
        "UPDATE registrations 
         SET status='approved', approved_by = ?, approved_at = NOW() 
         WHERE reg_id=?"
    );
    return $stmt->execute([$approvedByUserId, $id]);
}

>>>>>>> 971395209f191832ab1f5edc129c8db792146eff
}
