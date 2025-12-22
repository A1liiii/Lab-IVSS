<?php
require_once __DIR__ . "/app/core/database.php";

session_start();
$conn = Database::connect();

// LOGOUT LOG (SEBELUM SESSION DIHANCURKAN)
if (isset($_SESSION['user']['user_id'])) {
    try {
        $log = $conn->prepare("
            INSERT INTO public.log_activity (user_id, aksi, deskripsi, waktu)
            VALUES (?, ?, ?, NOW())
        ");
        $log->execute([
            $_SESSION['user']['user_id'],
            'logout',
            'Logout dari sistem'
        ]);
    } catch (PDOException $e) {
        // biarkan kosong, logout tetap jalan
    }
}

// HANCURKAN SESSION
session_unset();
session_destroy();

// REDIRECT KE LOGIN
header("Location: login.php");
exit;
