<?php

class OperatorDokumentasiController {
    public function index() {
        $active = 'dokumentasi';
        $title = "Kelola Dokumentasi";

        require 'app/Views/layouts/admin_header.php';
        require 'app/Views/layouts/operator_sidebar.php';
        require 'app/Views/Operator/dokumentasi.php';
        require 'app/Views/layouts/admin_footer.php';
    }
}
