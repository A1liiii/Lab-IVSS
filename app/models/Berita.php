<?php

require_once __DIR__ . '/../Config/database.php';

class Berita {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    // GET ALL BERITA + JOIN PENULIS
    public function getAll() {
        $sql = "SELECT b.*, u.username AS penulis 
                FROM berita b
                LEFT JOIN users u ON b.user_id = u.user_id
                ORDER BY b.berita_id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // GET DETAIL BERITA + PENULIS
    public function getById($id) {
        $sql = "SELECT b.*, u.username AS penulis
                FROM berita b
                LEFT JOIN users u ON b.user_id = u.user_id
                WHERE berita_id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // INSERT DATA BERITA
    public function insert($judul, $deskripsi, $foto, $file_url, $kategori, $tanggal, $user_id) {

        $sql = "INSERT INTO berita 
                (judul, deskripsi, foto, file_url, kategori, tgl_post, user_id)
                VALUES
                (:judul, :deskripsi, :foto, :file_url, :kategori, :tgl_post, :user_id)";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':judul'     => $judul,
            ':deskripsi' => $deskripsi,
            ':foto'      => $foto,
            ':file_url'  => $file_url,
            ':kategori'  => $kategori,
            ':tgl_post'  => $tanggal,
            ':user_id'   => $user_id
        ]);
    }

    // UPDATE BERITA (dengan / tanpa ganti foto atau file)
    public function update($id, $judul, $deskripsi, $foto, $file_url, $kategori, $tanggal) {

        $sql = "UPDATE berita SET
                    judul      = :judul,
                    deskripsi  = :deskripsi,
                    foto       = :foto,
                    file_url   = :file_url,
                    kategori   = :kategori,
                    tgl_post   = :tgl_post
                WHERE berita_id = :id";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ':judul'     => $judul,
            ':deskripsi' => $deskripsi,
            ':foto'      => $foto,
            ':file_url'  => $file_url,
            ':kategori'  => $kategori,
            ':tgl_post'  => $tanggal,
            ':id'        => $id
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
