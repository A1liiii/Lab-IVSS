<?php

require_once __DIR__ . '/../../models/Publikasi.php';

class OperatorPublikasiController {

    public function index() {

        $m = new Publikasi();
        $data = $m->all();

        // Generate tahun 2025–2035
        $years = range(2025, 2035);

        // Jika mau filter berdasarkan tahun (opsional)
        if (!empty($_GET['tahun'])) {
            $tahun = $_GET['tahun'];

            // Filter manual karena tidak ada kolom tahun di DB
            $data = array_filter($data, function($d) use ($tahun) {
                return strpos($d['tanggal_mulai'], $tahun) === 0 
                       || strpos($d['tanggal_selesai'], $tahun) === 0;
            });
        }

        $title  = "Publikasi";
        $active = "publikasi";

        require_once __DIR__ . '/../../Views/layouts/operator_header.php';
        require_once __DIR__ . '/../../Views/layouts/operator_sidebar.php';
        require_once __DIR__ . '/../../Views/operator/publikasi.php';
        require_once __DIR__ . '/../../Views/layouts/operator_footer.php';
    }
}
