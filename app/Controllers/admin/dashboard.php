<?php

require_once __DIR__ . '/../../models/Mahasiswa.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Approval.php';
require_once __DIR__ . '/../../models/Dokumentasi.php';
require_once __DIR__ . '/../../models/Log.php';

class DashboardController {

    public function index() {

        // === Panggil model ===
        $Mahasiswa = new Mahasiswa();
        $User = new User();
        $Registration = new Approval();
        $pendingReg = $Registration->getPending();
        $Dokumentasi = new Dokumentasi();
        $Log = new Log();

        // === Ambil data ===
        $totalMahasiswa   = $Mahasiswa->count();
        $totalUser        = $User->count();
        $pendingReg       = $User->countPending();
        $totalDokumentasi = $Dokumentasi->count();
        $recentActivity   = $Log->last(10);

        // === Data untuk view ===
        $title  = 'Dashboard - IVSS';
        $active = 'dashboard';

        // === Kirim ke view ===
        include __DIR__ . '/../../Views/layouts/admin_header.php';
        include __DIR__ . '/../../Views/layouts/admin_sidebar.php';
        include __DIR__ . '/../../Views/Admin/dashboard.php';
        include __DIR__ . '/../../Views/layouts/admin_footer.php';
    }
}
