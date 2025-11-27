<?php

require_once __DIR__ . '/../../models/Berita.php';

class OperatorBeritaController {

    public function index() {
        $title = "Kelola Berita";
        $active = "berita";

        $model = new Berita();
        $berita = $model->getAll();

        require_once __DIR__ . '/../../Views/layouts/operator_header.php';
        require_once __DIR__ . '/../../Views/layouts/operator_sidebar.php';
        require_once __DIR__ . '/../../Views/Operator/berita.php';
        require_once __DIR__ . '/../../Views/layouts/operator_footer.php';
    }
}
