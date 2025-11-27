<?php

require_once __DIR__ . '/../../models/Berita.php';

class OperatorBeritaEditController {

    public function index() {
        $title = "Edit Berita";
        $active = "berita";

        $model = new Berita();
        $id = $_GET['id'];
        $data = $model->getById($id);

        if (!$data) {
            echo "Data tidak ditemukan.";
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $judul = $_POST['judul'];
            $deskripsi = $_POST['deskripsi'];
            $kategori = $_POST['kategori'];

            // FOTO BARU?
            $foto = $data['foto'];
            if (!empty($_FILES['foto']['name'])) {
                $fotoName = time() . "_" . $_FILES['foto']['name'];
                $dest = "public/uploads/" . $fotoName;
                move_uploaded_file($_FILES['foto']['tmp_name'], $dest);
                $foto = $fotoName;
            }

            // FILE BARU?
            $file_url = $data['file_url'];
            if (!empty($_FILES['file_url']['name'])) {
                $fileName = time() . "_" . $_FILES['file_url']['name'];
                $dest = "public/uploads/" . $fileName;
                move_uploaded_file($_FILES['file_url']['tmp_name'], $dest);
                $file_url = $fileName;
            }

            // UPDATE
            $model->update($id, $judul, $deskripsi, $foto, $file_url, $kategori);

            header("Location: /lab-ivss/index.php?page=operator-berita");
            exit;
        }

        require_once __DIR__ . '/../../Views/layouts/operator_header.php';
        require_once __DIR__ . '/../../Views/layouts/operator_sidebar.php';
        require_once __DIR__ . '/../../Views/Operator/berita_edit.php';
        require_once __DIR__ . '/../../Views/layouts/operator_footer.php';
    }
}
