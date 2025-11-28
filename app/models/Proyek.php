<?php

require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/AnggotaProyek.php';

class Proyek {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function all() {
        $sql = "SELECT * FROM proyek ORDER BY proyek_id DESC";
        $stmt = $this->conn->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Ambil anggota untuk masing-masing proyek
        $anggotaModel = new AnggotaProyek();
        foreach ($data as &$p) {
            $p['id'] = $p['proyek_id'];
            $p['anggota'] = $anggotaModel->getByProject($p['proyek_id']);
        }
    
        return $data;
    }    
    

    public function find($id) {
        $sql = "SELECT * FROM proyek WHERE proyek_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        $p = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($p) {
            $p['id'] = $p['proyek_id'];
        }
        return $p;

    }

    public function insertWithMembers($judul, $deskripsi, $mulai, $selesai, $status, $anggota) {
        $sql = "INSERT INTO proyek (judul, deskripsi, tanggal_mulai, tanggal_selesai, status)
                VALUES (:judul, :deskripsi, :mulai, :selesai, :status)
                RETURNING proyek_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':judul' => $judul,
            ':deskripsi' => $deskripsi,
            ':mulai' => $mulai,
            ':selesai' => $selesai,
            ':status' => $status
        ]);

        $proyek_id = $stmt->fetchColumn();

        // insert anggota
        require_once __DIR__ . '/AnggotaProyek.php';
        $ap = new AnggotaProyek();

        foreach ($anggota as $a) {
            $ap->insert($proyek_id, $a['user_id'], $a['role']);
        }

        return $proyek_id;
    }

    public function update($id, $judul, $deskripsi, $mulai, $selesai, $status) {
        $sql = "UPDATE proyek SET
                judul = :judul,
                deskripsi = :deskripsi,
                tanggal_mulai = :mulai,
                tanggal_selesai = :selesai,
                status = :status
                WHERE proyek_id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id' => $id,
            ':judul' => $judul,
            ':deskripsi' => $deskripsi,
            ':mulai' => $mulai,
            ':selesai' => $selesai,
            ':status' => $status
        ]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM proyek WHERE proyek_id = :id");
        $stmt->execute([':id' => $id]);
    }

    public function count() {
        $sql = "SELECT COUNT(*) AS total FROM proyek";
        $stmt = $this->conn->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
    
}
