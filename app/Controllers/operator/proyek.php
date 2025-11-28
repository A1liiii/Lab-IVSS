<?php
require_once __DIR__ . '/../../models/Proyek.php';

class OperatorProyekController {

    public function index() {

        $proyek = new Proyek();
        $data = $proyek->all();

        $title = "Kelola Proyek";
        $active = "proyek";

        require 'app/Views/layouts/operator_header.php';
        require 'app/Views/layouts/operator_sidebar.php';
        require 'app/Views/operator/proyek.php';
        require 'app/Views/layouts/operator_footer.php';
    }
}



