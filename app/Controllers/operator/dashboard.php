<?php

require_once __DIR__ . '/../../models/Berita.php';
require_once __DIR__ . '/../../models/Dokumentasi.php';
require_once __DIR__ . '/../../models/Publikasi.php';
require_once __DIR__ . '/../../models/Proyek.php';
require_once __DIR__ . '/../../models/Fasilitas.php';

class OperatorDashboardController {

    public function index() {
        $title = "Dashboard Operator";
        $active = "dashboard";

        $mBerita = new Berita();
        $mDok = new Dokumentasi();
        $mPub = new Publikasi();
        $mProyek = new Proyek();
        $mFas = new Fasilitas();

        $stats = [
            "berita" => $mBerita->count(),
            "dokumentasi" => $mDok->count(),
            "publikasi" => $mPub->count(),
            "proyek" => $mProyek->count(),
            "fasilitas" => $mFas->count(),
        ];

        require_once __DIR__ . '/../../Views/layouts/operator_header.php';
        require_once __DIR__ . '/../../Views/layouts/operator_sidebar.php';
        require_once __DIR__ . '/../../Views/Operator/dashboard.php';
        require_once __DIR__ . '/../../Views/layouts/operator_footer.php';
    }
}
