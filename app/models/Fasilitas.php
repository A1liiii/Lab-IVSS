<?php

require_once __DIR__ . '/../Config/database.php';

class Fasilitas {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect(); // PDO
    }

    // Ambil semua data fasilitas
    public function all() {
        $sql = "SELECT * FROM fasilitas ORDER BY fasilitas_id DESC";
        $stmt = $this->conn->query($sql);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // untuk akses di view: $f['id']
        foreach ($data as &$f) {
            $f['id'] = $f['fasilitas_id'];
        }

        return $data;
    }

    // Ambil data berdasarkan ID
    public function find($id) {
        $stmt = $this->conn->prepare("SELECT * FROM fasilitas WHERE fasilitas_id = :id");
        $stmt->execute([':id' => $id]);
        $f = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($f) $f['id'] = $f['fasilitas_id'];
        return $f;
    }

    // Insert fasilitas
    public function insert($user_id, $nama, $deskripsi, $status, $foto, $kategori) {
        $sql = "INSERT INTO fasilitas (user_id, nama, deskripsi, status, foto, kategori)
                VALUES (:user_id, :nama, :deskripsi, :status, :foto, :kategori)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':user_id'   => $user_id,
            ':nama'      => $nama,
            ':deskripsi' => $deskripsi,
            ':status'    => $status,
            ':foto'      => $foto,
            ':kategori'  => $kategori
        ]);
    }

    // Update fasilitas
    public function update($id, $nama, $deskripsi, $status, $foto, $kategori) {
        $sql = "UPDATE fasilitas SET
                nama = :nama,
                deskripsi = :deskripsi,
                status = :status,
                foto = :foto,
                kategori = :kategori
                WHERE fasilitas_id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':id'        => $id,
            ':nama'      => $nama,
            ':deskripsi' => $deskripsi,
            ':status'    => $status,
            ':foto'      => $foto,
            ':kategori'  => $kategori
        ]);
    }

    // Hapus fasilitas
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM fasilitas WHERE fasilitas_id = :id");
        $stmt->execute([':id' => $id]);
    }

    // Hitung total untuk dashboard
    public function count() {
        $sql = "SELECT COUNT(*) AS total FROM fasilitas";
        $stmt = $this->conn->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

}


