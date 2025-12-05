<?php
session_start();

require_once __DIR__ . '/../../models/lab_info.php';
require_once __DIR__ . '/../../Config/database.php';

class LabInfoController {

    private $LabInfo;

    public function __construct()
    {
        $this->LabInfo = new LabInfoModel(Database::connect());
    }

    // =========================================
    // HALAMAN INFO LAB
    // =========================================
    public function index()
    {
        $labinfo = $this->LabInfo->get();

        $title  = "Kelola Info Lab";
        $active = "labinfo";

        include __DIR__ . '/../../Views/layouts/admin_header.php';
        include __DIR__ . '/../../Views/layouts/admin_sidebar.php';
        include __DIR__ . '/../../Views/Admin/lab_info.php';
        include __DIR__ . '/../../Views/layouts/admin_footer.php';
    }

    // =========================================
    // UPDATE INFO LAB
    // =========================================
    public function update()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: index.php?page=admin-labinfo");
        exit;
    }

    $data = [
        'nama'       => $_POST['nama'] ?? '',
        'visi'       => $_POST['visi'] ?? '',
        'misi'       => $_POST['misi'] ?? '',
        'deskripsi'  => $_POST['deskripsi'] ?? '',
        'motto'      => $_POST['motto'] ?? '',
        'alamat'     => $_POST['alamat'] ?? '',
        'email'      => $_POST['email'] ?? '',
        'no_telp'    => $_POST['no_telp'] ?? '',
        'youtube'    => $_POST['youtube'] ?? '',
        'instagram'  => $_POST['instagram'] ?? '',
        'tiktok'     => $_POST['tiktok'] ?? ''
    ];

    // pakai instance yang sudah dibuat di __construct()
    $this->LabInfo->update($data);

    $_SESSION['success'] = "Data berhasil diperbarui!";
    header("Location: index.php?page=admin-labinfo");
    exit;
}

// =========================================
// HALAMAN ABOUT PUBLIC
// =========================================
public function aboutPublic()
{
    $labInfo = $this->LabInfo->get(); // Ambil data dari DB

    // Jika data kosong, buat default supaya tidak error
    if (!$labInfo) {
        $labInfo = [
            'nama' => 'LAB IVSS',
            'deskripsi' => 'Deskripsi laboratorium belum tersedia.',
            'visi' => 'Visi belum tersedia.',
            'misi' => 'Misi belum tersedia.'
        ];
    }

    include __DIR__ . '/../../Views/layouts/public_header.php';
    include __DIR__ . '/../../Views/Public/about.php';
    include __DIR__ . '/../../Views/layouts/public_footer.php';
}



}
