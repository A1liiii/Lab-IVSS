<?php

require_once __DIR__ . '/../../models/Fasilitas.php';

class OperatorFasilitasEditController {

    public function index() {

        $title  = "Memperbarui Fasilitas";
        $active = "fasilitas";

        $m = new Fasilitas();
        $id = $_GET['fasilitas_id'];

        $data = $m->find($id);

        if (!$data) {
            die("Data fasilitas tidak ditemukan!");
        }

        // jika submit
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nama      = $_POST['nama'];
            $deskripsi = $_POST['deskripsi'];
            $status    = $_POST['status'];
            $kategori  = $_POST['kategori'];

            // foto lama
            $foto = $_POST['foto_lama'];

            // upload baru?
            if (!empty($_FILES['foto']['name'])) {
                $folder = __DIR__ . '/../../public/uploads/fasilitas/';
                if (!is_dir($folder)) mkdir($folder, 0777, true);

                $namaFile = time() . "_" . $_FILES['foto']['name'];
                move_uploaded_file($_FILES['foto']['tmp_name'], $folder . $namaFile);

                $foto = $namaFile;
            }

            $m->update($id, $nama, $deskripsi, $status, $foto, $kategori);

            header("Location: index.php?page=operator-fasilitas");
            exit;
        }
        require_once __DIR__ . '/../../Views/layouts/operator_header.php';
        require_once __DIR__ . '/../../Views/layouts/operator_sidebar.php';
        require_once __DIR__ . '/../../Views/Operator/fasilitas_edit.php';
        require_once __DIR__ . '/../../Views/layouts/operator_footer.php';
    }
}