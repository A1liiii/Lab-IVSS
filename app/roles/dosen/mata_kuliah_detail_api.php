<?php
// app/roles/dosen/mata_kuliah_detail_api.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("dosen");

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$kode = $_GET['kode'] ?? null;
if (!$kode) {
    echo json_encode(['error' => 'Parameter kode tidak disediakan']);
    exit;
}

// ambil nip dari user session
$user_id = $_SESSION['user']['user_id'] ?? null;
$stmt = $conn->prepare("SELECT nip FROM users WHERE user_id = ? LIMIT 1");
$stmt->execute([$user_id]);
$r = $stmt->fetch(PDO::FETCH_ASSOC);
$nip = $r['nip'] ?? null;
if (!$nip) {
    echo json_encode(['error' => 'NIP dosen tidak ditemukan']);
    exit;
}

$stmt = $conn->prepare("SELECT kode_matkul, nip, nama_matkul, semester, prodi, sks, tahun_ajar FROM mata_kuliah WHERE kode_matkul = ? AND nip = ? LIMIT 1");
$stmt->execute([$kode, $nip]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    echo json_encode(['error' => 'Mata kuliah tidak ditemukan atau bukan milik Anda.']);
    exit;
}

echo json_encode(['data' => $row]);
