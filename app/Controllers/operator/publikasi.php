<?php

class OperatorPublikasiController {
    public function index() {
        $active = 'publikasi';
        $title = "Kelola Publikasi";

        require 'app/Views/layouts/admin_header.php';
        require 'app/Views/layouts/operator_sidebar.php';
        require 'app/Views/Operator/publikasi.php';
        require 'app/Views/layouts/admin_footer.php';
    }
}
