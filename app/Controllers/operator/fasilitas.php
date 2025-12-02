<?php

class OperatorFasilitasController {
    public function index() {
        $active = 'fasilitas';
        $title = "Kelola Fasilitas";

        require 'app/Views/layouts/admin_header.php';
        require 'app/Views/layouts/operator_sidebar.php';
        require 'app/Views/Operator/fasilitas.php';
        require 'app/Views/layouts/admin_footer.php';
    }
}
