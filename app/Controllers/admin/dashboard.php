<?php

require_once __DIR__ . '/../../models/Mahasiswa.php';
require_once __DIR__ . '/../../models/User.php';
<<<<<<< HEAD
// require_once __DIR__ . '/../../models/Approval.php';
require_once __DIR__ . '/../../models/Dokumentasi.php';
// require_once __DIR__ . '/../../models/Log.php';
=======
require_once __DIR__ . '/../../models/Approval.php';
require_once __DIR__ . '/../../models/Dokumentasi.php';
require_once __DIR__ . '/../../models/Log.php';
>>>>>>> 971395209f191832ab1f5edc129c8db792146eff

class DashboardController {

    public function index() {

        // === Panggil model ===
        $Mahasiswa = new Mahasiswa();
        $User = new User();
<<<<<<< HEAD
        // $Registration = new Approval();
        // $pendingReg = $Registration->countPending();
        $Dokumentasi = new Dokumentasi();
        // $Log = new Log();
=======
        $Registration = new Approval();
        $Dokumentasi = new Dokumentasi();
        $Log = new Log();
>>>>>>> 971395209f191832ab1f5edc129c8db792146eff

        // === Ambil data ===
        $totalMahasiswa   = $Mahasiswa->count();
        $totalUser        = $User->count();
<<<<<<< HEAD
        // $pendingReg       = $User->countPending();
        $totalDokumentasi = $Dokumentasi->count();
        // $recentActivity   = $Log->last(10);
=======
        $pendingReg       = $User->countPending();
        $totalDokumentasi = $Dokumentasi->count();
        $recentActivity   = $Log->last(10);
>>>>>>> 971395209f191832ab1f5edc129c8db792146eff

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
