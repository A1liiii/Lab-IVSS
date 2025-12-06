<?php

require_once __DIR__ . '/../../models/Mahasiswa.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Proyek.php';
require_once __DIR__ . '/../../models/Log.php';

class DashboardMahasiswaController {

    private $modelMahasiswa;
    private $modelProyek;
    private $modelLog;

    public function __construct()
    {
        $this->modelMahasiswa = new Mahasiswa();
        $this->modelProyek = new Proyek();
        $this->modelLog = new Log();
    }

    public function index() {
        // === Data untuk view ===
        $title  = 'Dashboard Mahasiswa - IVSS';
        $active = 'dashboard-mahasiswa';

        // === Ambil user_id dari session ===
        if (!isset($_SESSION['user_id'])) {
            header("Location: /lab-ivss/index.php?page=login");
            exit;
        }

        $user_id = $_SESSION['user_id'];

        // === Panggil models ===
        $mahasiswaModel = new Mahasiswa();
        $userModel      = new User();
        $proyekModel    = new Proyek();
        $logModel       = new Log();

        // === Profil Mahasiswa ===
        $profil = $mahasiswaModel->findByUserId($user_id);

        // === Riset / Proyek yang diikuti mahasiswa ===
        // Jika tidak ada hubungan, ini mengembalikan array kosong
        $idMahasiswa = (is_array($profil) && isset($profil['id'])) ? $profil['id'] : null;
        $riset = $proyekModel->getByMahasiswa($idMahasiswa);


        // === Log aktivitas mahasiswa (untuk dashboard) ===
        $logs = $logModel->recentByUser($user_id, 10); // ambil 10 log terakhir


        // === Load Halaman View ===
        include __DIR__ . '/../../Views/layouts/mahasiswa_header.php';
        include __DIR__ . '/../../Views/layouts/mahasiswa_sidebar.php';
        include __DIR__ . '/../../Views/Mahasiswa/dashboard.php';
    }
}
