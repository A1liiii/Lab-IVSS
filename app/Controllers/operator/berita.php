<?php

class OperatorBeritaController {
    public function index() {
        $active = 'berita';
        $title = "Kelola Berita";

        require 'app/Views/layouts/admin_header.php';
        require 'app/Views/layouts/operator_sidebar.php';
        require 'app/Views/Operator/berita.php';
        require 'app/Views/layouts/admin_footer.php';
    }
}
