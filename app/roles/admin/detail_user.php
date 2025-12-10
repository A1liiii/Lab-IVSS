<?php
session_start();
require_once "../../core/auth.php";
requireRole("admin");

require_once "../../core/database.php";
$conn = Database::connect();

// fix baca id
$user_id = $_GET['id'] ?? ($_GET['user_id'] ?? null);

if(!$user_id){
    die("User tidak ditemukan");
}

// cek user termasuk kategori apa
$stmt = $conn->prepare("
    SELECT
        u.user_id, u.nip, u.nim,
        CASE 
            WHEN u.nip IS NOT NULL THEN 'dosen'
            WHEN u.nim IS NOT NULL THEN 'mahasiswa'
            ELSE 'other'
        END AS role_type
    FROM users u
    WHERE u.user_id = ?
");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$user){
    die("User ID tidak valid");
}

// Redirect otomatis sesuai tipe data
if($user['role_type'] == 'dosen'){
    header("Location: detail_dosen.php?user_id=".$user_id);
    exit;
}

if($user['role_type'] == 'mahasiswa'){
    header("Location: detail_mahasiswa.php?user_id=".$user_id);
    exit;
}

// fallback jika bukan keduanya
header("Location: detail_user_basic.php?user_id=".$user_id);
exit;
?>
