<?php 

require_once __DIR__ . '/../Config/database.php';

class Publikasi {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect(); // PDO
    }

    // Hitung total publikasi
    public function count() {
        $sql = "SELECT COUNT(*) AS total FROM publikasi";
        $stmt = $this->conn->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Ambil semua publikasi
    public function all() {
        $sql = "SELECT *, publikasi_id AS id FROM publikasi ORDER BY publikasi_id DESC";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Ambil data berdasarkan ID
    public function find($id) {
        $sql = "SELECT *, publikasi_id AS id FROM publikasi WHERE publikasi_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Insert publikasi baru
    public function insert($user_id, $judul, $deskripsi, $tanggal_mulai, $tanggal_selesai, $status, $link) {
        $sql = "INSERT INTO publikasi 
                (user_id, judul, deskripsi, tanggal_mulai, tanggal_selesai, status, link)
                VALUES 
                (:user_id, :judul, :deskripsi, :tanggal_mulai, :tanggal_selesai, :status, :link)";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':user_id'         => $user_id,
            ':judul'           => $judul,
            ':deskripsi'       => $deskripsi,
            ':tanggal_mulai'   => $tanggal_mulai,
            ':tanggal_selesai' => $tanggal_selesai,
            ':status'          => $status,
            ':link'            => $link
        ]);
    }

    // Update publikasi
    public function update($id, $judul, $deskripsi, $tanggal_mulai, $tanggal_selesai, $status, $link) {
        $sql = "UPDATE publikasi SET
                judul = :judul,
                deskripsi = :deskripsi,
                tanggal_mulai = :tanggal_mulai,
                tanggal_selesai = :tanggal_selesai,
                status = :status,
                link = :link
                WHERE publikasi_id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id'              => $id,
            ':judul'           => $judul,
            ':deskripsi'       => $deskripsi,
            ':tanggal_mulai'   => $tanggal_mulai,
            ':tanggal_selesai' => $tanggal_selesai,
            ':status'          => $status,
            ':link'            => $link
        ]);
    }

    // Hapus publikasi
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM publikasi WHERE publikasi_id = :id");
        $stmt->execute([':id' => $id]);
    }
}
