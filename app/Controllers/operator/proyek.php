<?php

require_once __DIR__ . '/../../models/Proyek.php';

class OperatorProyekController {

    public function index() {

        $m = new Proyek();
        $data = $m->getAll();

        $title  = "Kelola Proyek";
        $active = "proyek";

        require_once __DIR__ . '/../../Views/layouts/operator_header.php';
        require_once __DIR__ . '/../../Views/layouts/operator_sidebar.php';
        require_once __DIR__ . '/../../Views/Operator/proyek.php';
        require_once __DIR__ . '/../../Views/layouts/operator_footer.php';
    }
}
