<?php
function showReminder() {

    if (!isset($_SESSION['user'])) return "";

    $nip = $_SESSION['user']['nip'] ?? null;
    $nim = $_SESSION['user']['nim'] ?? null;

    $alert = "";

    // ============================================================
    // 1. PENGINGAT AKTIVITAS — UNTUK SEMUA ROLE
    // ============================================================
    $alert .= "
    <div class='alert alert-info d-flex align-items-center gap-2 p-2 small mb-3'>
        <i class='bi bi-pencil-square'></i>
        <span>Ingat untuk selalu mengisi <strong>Aktivitas</strong> setiap selesai melakukan kegiatan di role fundamental Anda.</span>
    </div>
    ";


    // ============================================================
    // 2. PENGINGAT EDIT PROFIL — UNTUK SEMUA ROLE
    // ============================================================

    if (!empty($nip)) {
        $alert .= "
        <div class='alert alert-warning d-flex align-items-center gap-2 p-2 small mb-3'>
            <i class='bi bi-exclamation-triangle-fill'></i>
            <span>Profil utama Anda hanya dapat diubah melalui <strong>Role Dosen</strong>. Silakan switch role.</span>
        </div>
        ";
    }

    if (!empty($nim)) {
        $alert .= "
        <div class='alert alert-warning d-flex align-items-center gap-2 p-2 small mb-3'>
            <i class='bi bi-exclamation-triangle-fill'></i>
            <span>Profil utama Anda hanya dapat diubah melalui <strong>Role Mahasiswa</strong>. Silakan switch role.</span>
        </div>
        ";
    }

    return $alert;
}
?>
