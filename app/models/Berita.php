<?php

require_once __DIR__ . '/../Config/database.php';

class Berita {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function getAll() {
        $sql = "SELECT * FROM berita ORDER BY berita_id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT * FROM berita WHERE berita_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insert($judul, $deskripsi, $foto, $file_url, $kategori, $user_id) {
        $sql = "INSERT INTO berita (judul, deskripsi, foto, file_url, kategori, tgl_post, user_id)
                VALUES (:judul, :deskripsi, :foto, :file_url, :kategori, CURRENT_DATE, :user_id)";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':judul' => $judul,
            ':deskripsi' => $deskripsi,
            ':foto' => $foto,
            ':file_url' => $file_url,
            ':kategori' => $kategori,
            ':user_id' => $user_id
        ]);
    }

    public function update($id, $judul, $deskripsi, $foto, $file_url, $kategori) {
        $sql = "UPDATE berita 
                SET judul = :judul, deskripsi = :deskripsi, foto = :foto,
                    file_url = :file_url, kategori = :kategori 
                WHERE berita_id = :id";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':judul' => $judul,
            ':deskripsi' => $deskripsi,
            ':foto' => $foto,
            ':file_url' => $file_url,
            ':kategori' => $kategori,
            ':id' => $id
        ]);
    }

    public function delete($id) {
        $sql = "DELETE FROM berita WHERE berita_id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    public function count() {
        $sql = "SELECT COUNT(*) AS total FROM berita";
        return $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
