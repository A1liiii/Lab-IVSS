<?php

require_once __DIR__ . '/../Config/database.php';

class Mahasiswa {

    private $conn;

    public function __construct() {
        $this->conn = Database::connect();
    }

    public function count() {
        $sql = "SELECT COUNT(*) AS total FROM mahasiswa";
        $stmt = $this->conn->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
public function create($data) {

    // Cek apakah nim sudah pernah dibuat
    $stmtCheck = $this->conn->prepare("SELECT nim FROM mahasiswa WHERE nim = :nim");
    $stmtCheck->execute([':nim' => $data['nim']]);
    if ($stmtCheck->fetch()) {
        return false;
    }

    $sql = "INSERT INTO mahasiswa 
        (nim, user_id, nama, email, prodi, angkatan, status, foto, kategori, tanggal_join)
        VALUES 
        (:nim, :user_id, :nama, :email, :prodi, :angkatan, :status, :foto, :kategori, :tanggal_join)";

    $stmt = $this->conn->prepare($sql);

    return $stmt->execute([
        ':nim'          => $data['nim'],
        ':user_id'  => isset($data['user_id']) ? $data['user_id'] : null,
        ':nama'         => $data['nama'],
        ':email'        => $data['email'],
        ':prodi'        => $data['prodi'],
        ':angkatan'     => $data['angkatan'],
        ':status'       => $data['status'],
        ':foto'     => isset($data['foto']) ? $data['foto'] : null,   // ← FIX PENTING
        ':kategori'     => $data['kategori'],
        ':tanggal_join' => $data['tanggal_join']
    ]);
}
    public function getAvailableMahasiswa() {
        $sql = "SELECT * FROM mahasiswa WHERE user_id IS NULL";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function setUserId($nim, $user_id) {
        $sql = "UPDATE mahasiswa SET user_id = :user_id WHERE nim = :nim";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':user_id' => $user_id,
            ':nim'     => $nim
        ]);
    }
    public function updateUserID($nim, $user_id) {
    $stmt = $this->conn->prepare("
        UPDATE mahasiswa SET user_id = :uid WHERE nim = :nim
    ");
    $stmt->execute([
        ':uid' => $user_id,
        ':nim' => $nim
    ]);
}

}
?>
