<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../../core/auth.php";
// requireRole("dosen");

header("Content-Type: application/json");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$dosen_user_id = $_SESSION['user']['user_id'];
$nim = $_GET['nim'] ?? null;

if(!$nim){
    echo json_encode(['error' => 'NIM tidak disediakan']);
    exit;
}

$stmt = $conn->prepare("
    SELECT nim, nama, email, prodi, angkatan, status, foto, tanggal_join
    FROM mahasiswa
    WHERE nim = ? AND kategori='bimbingan' AND user_id = ?
    LIMIT 1
");
$stmt->execute([$nim, $dosen_user_id]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$row){
    echo json_encode(['error' => 'Mahasiswa tidak ditemukan atau bukan bimbingan Anda.']);
    exit;
}

echo json_encode(['data' => $row]);
