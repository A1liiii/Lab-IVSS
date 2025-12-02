<?php

require_once __DIR__ . '/../../models/Publikasi.php';
require_once __DIR__ . '/../../Helper/LogHelper.php';

class OperatorPublikasiEditController {

    public function index() {

        $m = new Publikasi();
        $id = $_GET['id'];

        $data = $m->find($id);

        if (!$data) {
            die("Data publikasi tidak ditemukan!");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $judul           = $_POST['judul'];
            $deskripsi       = $_POST['deskripsi'];
            $tanggal_mulai   = $_POST['tanggal_mulai'];
            $tanggal_selesai = $_POST['tanggal_selesai'];
            $status          = $_POST['status'];
            $link            = $_POST['link'];

            $m->update($id, $judul, $deskripsi, $tanggal_mulai, $tanggal_selesai, $status, $link);
            
            addLog($_SESSION['user_id'], "Memperbarui publikasi ID $id", "UPDATE");

            header("Location: index.php?page=operator-publikasi");
            exit;
        }

        $title  = "Edit Publikasi";
        $active = "publikasi";

        require_once __DIR__ . '/../../Views/layouts/operator_header.php';
        require_once __DIR__ . '/../../Views/layouts/operator_sidebar.php';
        require_once __DIR__ . '/../../Views/operator/publikasi_edit.php';
        require_once __DIR__ . '/../../Views/layouts/operator_footer.php';
    }
}
