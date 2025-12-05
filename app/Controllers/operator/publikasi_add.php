<?php

require_once __DIR__ . '/../../models/Publikasi.php';
require_once __DIR__ . '/../../Helper/LogHelper.php';

class OperatorPublikasiAddController {

    public function index() {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $m = new Publikasi();

            $user_id   = $_SESSION['user_id'];  // wajib
            $judul     = trim($_POST['judul']);
            $deskripsi = trim($_POST['deskripsi']);
            $tahun     = trim($_POST['tahun']); // input tahun dari form
            $status    = $_POST['status'];  
            $link      = $_POST['link'];

            // Konversi tahun ke tanggal_mulai & tanggal_selesai
            $tanggal_mulai   = $tahun . '-01-01';
            $tanggal_selesai = $tahun . '-12-31';

            $m->insert(
                $user_id,
                $judul,
                $deskripsi,
                $tanggal_mulai,
                $tanggal_selesai,
                $status,
                $link
            );

            addLog($_SESSION['user_id'], "Menambah publikasi baru", "CREATE");

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
