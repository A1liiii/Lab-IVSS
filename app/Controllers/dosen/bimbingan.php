<?php
require_once '../config/database.php';
require_once '../models/MahasiswaModel.php';

$model = new MahasiswaModel($conn);

/*
========================================
✅ SIMULASI DOSEN LOGIN (SEMENTARA)
GANTI nanti jadi:
session_start();
$dosen_id = $_SESSION['user_id'];
========================================
*/
$dosen_id = 1;


// ✅ PROSES TAMBAH MAHASISWA
if (isset($_POST['simpan'])) {

    $data = [
        'nim'          => $_POST['nim'],
        'user_id'      => $dosen_id, // ✅ OTOMATIS dari dosen
        'nama'         => $_POST['nama'],
        'email'        => $_POST['email'],
        'prodi'        => $_POST['prodi'],
        'angkatan'     => $_POST['angkatan'],
        'status'       => $_POST['status'],
        'kategori'     => $_POST['kategori'],
        'tanggal_join' => date('Y-m-d')
    ];

    $model->insert($data);

    header("Location: ../views/dosen/mahasiswa.php");
}
