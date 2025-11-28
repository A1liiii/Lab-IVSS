<?php

require_once __DIR__ . '/../../models/Publikasi.php';

class OperatorPublikasiAddController {

    public function index() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $m = new Publikasi();

            $user_id         = 3; // nanti diganti ke SESSION
            $judul           = $_POST['judul'];
            $deskripsi       = $_POST['deskripsi'];
            $tanggal_mulai   = $_POST['tanggal_mulai'];
            $tanggal_selesai = $_POST['tanggal_selesai'];
            $status          = $_POST['status'];
            $link            = $_POST['link'];

            $m->insert($user_id, $judul, $deskripsi, $tanggal_mulai, $tanggal_selesai, $status, $link);

            header("Location: index.php?page=operator-publikasi");
            exit;
        }

        $title  = "Tambah Publikasi";
        $active = "publikasi";

        require_once __DIR__ . '/../../Views/layouts/operator_header.php';
        require_once __DIR__ . '/../../Views/layouts/operator_sidebar.php';
        require_once __DIR__ . '/../../Views/operator/publikasi_add.php';
        require_once __DIR__ . '/../../Views/layouts/operator_footer.php';
    }
}
