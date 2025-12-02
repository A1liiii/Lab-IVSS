<?php

require_once __DIR__ . '/../../models/Dokumentasi.php';
require_once __DIR__ . '/../../Helper/LogHelper.php';

class OperatorDokumentasiEditController {

    public function index() {

        $title  = "Memperbarui Dokumentasi";
        $active = "dokumentasi";

        $m = new Dokumentasi();

        if (!isset($_GET['documentation_id'])) {
            echo "ID tidak ditemukan.";
            exit;
        }

        $id = $_GET['documentation_id'];
        $data = $m->getById($id);

        // Jika submit
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $judul_kegiatan     = $_POST['judul_kegiatan'];
            $deskripsi_kegiatan = $_POST['deskripsi_kegiatan'];
            $caption            = $_POST['caption'];
            $tanggal_kegiatan   = $_POST['tanggal_kegiatan'];
            $jenis_kegiatan     = $_POST['jenis_kegiatan'];

            // Ambil data lama
            $dok = $m->getById($id);

            $file_path = $dok['file_path'];
            $type_file = $dok['type_file'];

            // Jika ada file baru →
            if (!empty($_FILES['file_path']['name'])) {

                $folder = __DIR__ . '/../../public/uploads/dokumentasi/';

                // Hapus file lama
                $old = $folder . $file_path;
                if (file_exists($old)) unlink($old);

                $namaBaru = time() . "_" . $_FILES['file_path']['name'];
                move_uploaded_file($_FILES['file_path']['tmp_name'], $folder . $namaBaru);

                $file_path = $namaBaru;
                $type_file = pathinfo($namaBaru, PATHINFO_EXTENSION);
            }

            // Update
            $m->update(
                $id,
                $file_path,
                $type_file,
                $caption,
                $judul_kegiatan,
                $deskripsi_kegiatan,
                $tanggal_kegiatan,
                $jenis_kegiatan
            );

            addLog($_SESSION['user_id'], "Memperbarui dokumentasi ID $id", "UPDATE");

            header("Location: /lab-ivss/index.php?page=operator-dokumentasi");
            exit;
        }

        // Ambil data untuk form
        $data = $m->getById($id);

        require_once __DIR__ . '/../../Views/layouts/operator_header.php';
        require_once __DIR__ . '/../../Views/layouts/operator_sidebar.php';
        require_once __DIR__ . '/../../Views/Operator/dokumentasi_edit.php';
        require_once __DIR__ . '/../../Views/layouts/operator_footer.php';
    }
}
