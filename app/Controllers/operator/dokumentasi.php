<?php

require_once __DIR__ . '/../../models/Dokumentasi.php';

class OperatorDokumentasiController {
    
    public function index() {
        $m = new Dokumentasi();
        $data  = $m->all();

        $title = "Kelola Dokumentasi";
        $active = "dokumentasi";

        require_once __DIR__ . '/../../Views/layouts/operator_header.php';
        require_once __DIR__ . '/../../Views/layouts/operator_sidebar.php';
        require_once __DIR__ . '/../../Views/Operator/dokumentasi.php';
        require_once __DIR__ . '/../../Views/layouts/operator_footer.php';
    }
}
