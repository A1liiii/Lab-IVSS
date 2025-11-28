<?php
require_once __DIR__ . '/../Config/database.php';

class AnggotaProyek {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function getByProject($proyek_id) {
        $sql = "SELECT 
                    ap.id,
                    u.user_id,
                    u.username,
                    COALESCE(u.nim, u.nip) AS identitas,
                    ap.role
                FROM anggota_proyek ap
                JOIN users u ON u.user_id = ap.user_id
                WHERE ap.proyek_id = :id";
    
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $proyek_id]);
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    


    public function insert($proyek_id, $user_id, $role) {
        $sql = "INSERT INTO anggota_proyek (proyek_id, user_id, role)
                VALUES (:proyek_id, :user_id, :role)";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':proyek_id' => $proyek_id,
            ':user_id' => $user_id,
            ':role' => $role
        ]);
    }

    public function deleteById($id) {
        $stmt = $this->conn->prepare("DELETE FROM anggota_proyek WHERE id = :id");
        $stmt->execute([':id' => $id]);
    }

    public function deleteByProject($proyek_id) {
        $stmt = $this->conn->prepare("DELETE FROM anggota_proyek WHERE proyek_id = :id");
        $stmt->execute([':id' => $proyek_id]);
    }
}
