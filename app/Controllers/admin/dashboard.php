<?php

require_once __DIR__ . '/../../models/Mahasiswa.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Approval.php';
require_once __DIR__ . '/../../models/Berita.php';
require_once __DIR__ . '/../../models/Dokumentasi.php';
require_once __DIR__ . '/../../models/Publikasi.php';
require_once __DIR__ . '/../../models/Fasilitas.php';
require_once __DIR__ . '/../../models/Log.php';

class DashboardController {

    public function index() {
        // === Data untuk view ===
        $title  = 'Dashboard - IVSS';
        $active = 'dashboard';

        // === Panggil model ===
        $mahasiswaModel   = new Mahasiswa();
        $User = new User();
        $Registration = new Approval();
        $beritaModel      = new Berita();
        $dokumentasiModel = new Dokumentasi();
        $logModel         = new Log();
        // $proyekModel      = new Proyek();
        $publikasiModel   = new Publikasi();
        $fasilitasModel   = new Fasilitas();


        // === Ambil total masing-masing modul ===
        $totalMahasiswa   = $mahasiswaModel->count();
        $totalUser = $User->count();
        $pendingReg = $User->countPending();
        $totalBerita      = $beritaModel->count();
        $totalDokumentasi = $dokumentasiModel->count();
        // $totalProyek      = $proyekModel->count();
        $totalPublikasi   = $publikasiModel->count();
        $totalFasilitas   = $fasilitasModel->count();


        // === Total semua modul ===
        $totalSemuaModul = $totalMahasiswa + $totalUser + $pendingReg + $totalBerita + $totalDokumentasi + /* $totalProyek +*/ $totalPublikasi + $totalFasilitas;

        // === Ambil recent activity ===
        $recentActivity = $logModel->recent(10); // Ambil 10 aktivitas terakhir

        // === Kirim ke view ===
        include __DIR__ . '/../../Views/layouts/admin_header.php';
        include __DIR__ . '/../../Views/layouts/admin_sidebar.php';
        include __DIR__ . '/../../Views/Admin/dashboard.php';
        include __DIR__ . '/../../Views/layouts/admin_footer.php';
    }
}
