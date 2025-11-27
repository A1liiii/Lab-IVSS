<?php

class OperatorProyekController {
    public function index() {
        $active = 'proyek';
        $title = "Kelola Proyek";

        require 'app/Views/layouts/admin_header.php';
        require 'app/Views/layouts/operator_sidebar.php';
        require 'app/Views/Operator/proyek.php';
        require 'app/Views/layouts/admin_footer.php';
    }
}
