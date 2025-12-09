<?php
session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("admin");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$regId = $_GET['reg_id'] ?? null;
if (!$regId) {
    header("Location: approvals.php");
    exit;
}

// ===============================
// 1) AMBIL DATA REGISTRASI
// ===============================
$stmt = $conn->prepare("
    SELECT * FROM registrations
    WHERE reg_id = ?
");
$stmt->execute([$regId]);
$reg = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reg) {
    die("Data tidak ditemukan.");
}

if ($reg['status'] !== 'approved') {
    die("Pendaftaran belum disetujui Ketua Lab.");
}

try {
    $conn->beginTransaction();

    // ===============================
    // 2) INSERT KE TABEL MAHASISWA
    // ===============================
    $check = $conn->prepare("SELECT nim FROM mahasiswa WHERE nim = ?");
    $check->execute([$reg['nim']]);
    $exists = $check->fetch();

    if (!$exists) {
        $mhsStmt = $conn->prepare("
            INSERT INTO mahasiswa (nim, nama, email, prodi, angkatan, status, kategori, tanggal_join)
            VALUES (?, ?, ?, ?, ?, 'aktif', 'riset', ?)
        ");
        $mhsStmt->execute([
            $reg['nim'],
            $reg['nama'],
            $reg['email'],
            $reg['prodi'],
            $reg['angkatan'],
            $reg['approved_at'] // isi ke tanggal_join
        ]);
    }

    // ===============================
    // 3) GENERATE USER LOGIN
    // ===============================
    $newUserId = $conn->query("SELECT COALESCE(MAX(user_id),0)+1 FROM users")->fetchColumn();

    $username = $reg['nim']; // login pakai NIM
    $defaultPass = "123456";
    $hash = md5($defaultPass);

    $insertUser = $conn->prepare("
        INSERT INTO users (user_id, nip, nim, username, password)
        VALUES (?, NULL, ?, ?, ?)
    ");
    $insertUser->execute([
        $newUserId,
        $reg['nim'],
        $username,
        $hash
    ]);

    // ===============================
    // 4) user_id mahasiswa TIDAK DI UPDATE
    //    (biarkan NULL sesuai request)
    // ===============================

    // ===============================
    // 5) INSERT ROLE → mahasiswa
    // ===============================
    $roleInsert = $conn->prepare("
        INSERT INTO user_roles (user_id, role_id)
        VALUES (?, (SELECT role_id FROM roles WHERE role_name = 'mahasiswa'))
    ");
    $roleInsert->execute([$newUserId]);

    // ===============================
    // 6) Registrasi ditandai selesai
    // ===============================
    $updateReg = $conn->prepare("
        UPDATE registrations
        SET account_created = TRUE
        WHERE reg_id = ?
    ");
    $updateReg->execute([$regId]);

    $conn->commit();

    header("Location: ../admin/user.php");
    exit;

} catch (Exception $e) {
    $conn->rollBack();
    die("Gagal buat akun: " . $e->getMessage());
}
