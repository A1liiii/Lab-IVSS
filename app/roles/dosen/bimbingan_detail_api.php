<?php
// app/roles/dosen/bimbingan_detail_api.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("dosen");

header('Content-Type: application/json');
require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$nim = $_GET['nim'] ?? null;
if (!$nim) {
    echo json_encode(['error' => 'NIM tidak disediakan']);
    exit;
}

$stmt = $conn->prepare("SELECT nim, nama, email, prodi, angkatan, status, foto, tanggal_join FROM mahasiswa WHERE nim = ? LIMIT 1");
$stmt->execute([$nim]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
    echo json_encode(['error' => 'Mahasiswa tidak ditemukan']);
    exit;
}

echo json_encode(['data' => $row]);
