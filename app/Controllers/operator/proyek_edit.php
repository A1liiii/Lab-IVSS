<?php
require_once __DIR__ . '/../../models/Berita.php';
class OperatorProyekEditController {
    private $conn;

    public function index() {
        $this->conn = Database::connect();
    }

    public function count() {
        $sql = "SELECT COUNT(*) AS total FROM proyek";
        $stmt = $this->conn->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    public function getAll() {
        $sql = "SELECT p.*, COUNT(a.id) AS jumlah_anggota
                FROM proyek p
                LEFT JOIN anggota_proyek a ON a.proyek_id = p.proyek_id
                GROUP BY p.proyek_id
                ORDER BY p.proyek_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT * FROM proyek WHERE proyek_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($data) {
        $sql = "INSERT INTO proyek (judul, deskripsi, tanggal_mulai, tanggal_selesai, status)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['judul'],
            $data['deskripsi'],
            $data['tanggal_mulai'],
            $data['tanggal_selesai'],
            $data['status']
        ]);
    }

    public function update($id, $data) {
        $sql = "UPDATE proyek SET judul=?, deskripsi=?, tanggal_mulai=?, tanggal_selesai=?, status=? WHERE proyek_id=?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            $data['judul'],
            $data['deskripsi'],
            $data['tanggal_mulai'],
            $data['tanggal_selesai'],
            $data['status'],
            $id
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM proyek WHERE proyek_id=?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function getAnggota($proyek_id) {
        $sql = "SELECT a.*, u.username AS nama, u.nip, u.nim
                FROM anggota_proyek a
                LEFT JOIN users u ON a.user_id = u.user_id
                WHERE a.proyek_id=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$proyek_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
