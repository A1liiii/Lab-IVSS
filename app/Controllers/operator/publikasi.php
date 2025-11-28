<?php

require_once __DIR__ . '/../../models/Publikasi.php';

class OperatorPublikasiController {

    public function index() {

        $m = new Publikasi();
        $data = $m->all();

        $title  = "Publikasi";
        $active = "publikasi";

        require_once __DIR__ . '/../../Views/layouts/operator_header.php';
        require_once __DIR__ . '/../../Views/layouts/operator_sidebar.php';
        require_once __DIR__ . '/../../Views/operator/publikasi.php';
        require_once __DIR__ . '/../../Views/layouts/operator_footer.php';
    }
}
