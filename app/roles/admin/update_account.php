<?php
session_start();
require_once "../../core/auth.php";
requireRole("admin");

require_once "../../core/database.php";
$conn = Database::connect();

$user_id = $_POST["user_id"] ?? null;
$username = $_POST["username"] ?? "";
$password = $_POST["password"] ?? "";

if(!$user_id || !$username){
    die("Data tidak valid.");
}

try{
    // update username
    $stmt = $conn->prepare("UPDATE users SET username = ? WHERE user_id = ?");
    $stmt->execute([$username, $user_id]);

    // jika password diisi → hash + update
    if(!empty($password)){
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt2 = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
        $stmt2->execute([$hashed, $user_id]);
    }

    // === LOG UPDATE AKUN LOGIN MAHASISWA ===
    try {
        $log = $conn->prepare("
            INSERT INTO log_activity (user_id, aksi, deskripsi, waktu)
            VALUES (?, ?, ?, NOW())
        ");
        $log->execute([
            $_SESSION['user']['user_id'], // admin
            'update',
            'Memperbarui akun login mahasiswa ' . $namaMhs . ' (' . $nim . ')'
        ]);
    } catch (PDOException $e) {}
        header("Location: detail_user.php?id=" . $user_id);
    exit;

}catch(Exception $e){
    die("Gagal update akun: ".$e->getMessage());
}
?>
