<?php 
require_once __DIR__ . '/../../core/Database.php'; 

// Gunakan method connect() sesuai struktur project
$pdo = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama     = $_POST['nama'] ?? '';
    $nim      = $_POST['nim'] ?? null;
    $email    = $_POST['email'] ?? '';
    $prodi    = $_POST['prodi'] ?? null;
    $angkatan = $_POST['angkatan'] ?? null;
    $alasan   = $_POST['alasan'] ?? null;

    // Validasi minimal
    if ($nama == '' || $email == '') {
        echo "Nama dan Email wajib diisi.";
        exit;
    }

    $sql = "INSERT INTO registrations 
            (nama, nim, email, prodi, angkatan, alasan, status)
            VALUES 
            (:nama, :nim, :email, :prodi, :angkatan, :alasan, 'pending')";

    $stmt = $pdo->prepare($sql);

    $stmt->bindParam(':nama', $nama);
    $stmt->bindParam(':nim', $nim);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':prodi', $prodi);
    $stmt->bindParam(':angkatan', $angkatan);
    $stmt->bindParam(':alasan', $alasan);

    if ($stmt->execute()) {
        header("Location: /?success=1");
        exit;
    } else {
        echo "Gagal menyimpan data pendaftaran.";
    }
}
?>
