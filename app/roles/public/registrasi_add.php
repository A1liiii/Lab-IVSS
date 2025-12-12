<?php 
require_once __DIR__ . '/../../core/Database.php'; 

$pdo = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama     = $_POST['nama'] ?? '';
    $nim      = $_POST['nim'] ?? null;
    $email    = $_POST['email'] ?? '';
    $prodi    = $_POST['prodi'] ?? null;
    $angkatan = $_POST['angkatan'] ?? null;
    $alasan   = $_POST['alasan'] ?? null;

    if (trim($nama) === '' || trim($email) === '') {
        echo "ERROR: Nama dan Email wajib diisi.";
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
        echo "OK"; // wajib pakai ini agar php-email-form tidak error
        exit;
    } else {
        echo "ERROR: Gagal menyimpan data pendaftaran.";
        exit;
    }
}

echo "ERROR: Invalid request.";
exit;
?>
