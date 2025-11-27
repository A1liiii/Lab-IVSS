<?php

require_once __DIR__ . '/../../models/Fasilitas.php';

class OperatorFasilitasController {
    
    public function index() {
        $m = new Fasilitas();
        $data  = $m->all();

        $title = "Kelola Fasilitas";
        $active = "fasilitas";

        require_once __DIR__ . '/../../Views/layouts/operator_header.php';
        require_once __DIR__ . '/../../Views/layouts/operator_sidebar.php';
        require_once __DIR__ . '/../../Views/Operator/fasilitas.php';
        require_once __DIR__ . '/../../Views/layouts/operator_footer.php';
    }
}

