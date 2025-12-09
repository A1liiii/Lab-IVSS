<?php 
require_once __DIR__ . '/../../core/Database.php'; 

$db = new Database();
$pdo = $db->pdo;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama     = isset($_POST['nama']) ? $_POST['nama'] : '';
    $nim      = isset($_POST['nim']) ? $_POST['nim'] : null;
    $email    = isset($_POST['email']) ? $_POST['email'] : '';
    $prodi    = isset($_POST['prodi']) ? $_POST['prodi'] : null;
    $angkatan = isset($_POST['angkatan']) ? $_POST['angkatan'] : null;
    $alasan   = isset($_POST['alasan']) ? $_POST['alasan'] : null;

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
