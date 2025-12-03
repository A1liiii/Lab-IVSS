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

    // Untuk penambahan mahasiswa setelah APPROVE
    public function create($data) {
    // Cek apakah nim sudah ada
    $stmtCheck = $this->conn->prepare("SELECT nim FROM mahasiswa WHERE nim = :nim");
    $stmtCheck->execute([':nim' => $data['nim']]);
    if ($stmtCheck->fetch()) {
        // Nim sudah ada, jangan insert lagi
        return false;
    }

    // Insert baru
    $sql = "INSERT INTO mahasiswa 
            (nim, user_id, nama, email, prodi, angkatan, status, foto, kategori, tanggal_join)
            VALUES 
            (:nim, :user_id, :nama, :email, :prodi, :angkatan, :status, :foto, :kategori, :tanggal_join)";

    $stmt = $this->conn->prepare($sql);

    return $stmt->execute([
        ':nim'          => $data['nim'],
        ':user_id'      => $data['user_id'],
        ':nama'         => $data['nama'],
        ':email'        => $data['email'],
        ':prodi'        => $data['prodi'],
        ':angkatan'     => $data['angkatan'],
        ':status'       => $data['status'],
        ':foto'         => $data['foto'],  
        ':kategori'     => $data['kategori'],
        ':tanggal_join' => $data['tanggal_join']
    ]);
}
}
?>
