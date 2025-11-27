<?php

require_once __DIR__ . '/../../models/Berita.php';

class OperatorBeritaAddController {

    public function index() {
        $title = "Tambah Berita";
        $active = "berita";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $judul = $_POST['judul'];
            $deskripsi = $_POST['deskripsi'];
            $kategori = $_POST['kategori'];
            $user_id = 1; // sementara (nanti bisa ambil dari session)

            // === Upload FOTO ===
            $foto = null;
            if (!empty($_FILES['foto']['name'])) {

                $folder = __DIR__ . '/../../public/uploads/berita/';
                if (!is_dir($folder)) mkdir($folder, 0755, true);

                $fotoName = time() . "_" . $_FILES['foto']['name'];
                move_uploaded_file($_FILES['foto']['tmp_name'], $folder . $fotoName);

                $foto = $fotoName;
            }

            // === Upload File Tambahan (PDF) ===
            $file_url = null;
            if (!empty($_FILES['file_url']['name'])) {

                $folder = __DIR__ . '/../../public/uploads/berita/';
                if (!is_dir($folder)) mkdir($folder, 0755, true);

                $fileName = time() . "_" . $_FILES['file_url']['name'];
                move_uploaded_file($_FILES['file_url']['tmp_name'], $folder . $fileName);

                $file_url = $fileName;
            }


            // SIMPAN
            $model = new Berita();
            $model->insert($judul, $deskripsi, $foto, $file_url, $kategori, $user_id);

            header("Location: /lab-ivss/index.php?page=operator-berita");
            exit;
        }

        require_once __DIR__ . '/../../Views/layouts/operator_header.php';
        require_once __DIR__ . '/../../Views/layouts/operator_sidebar.php';
        require_once __DIR__ . '/../../Views/Operator/berita_add.php';
        require_once __DIR__ . '/../../Views/layouts/operator_footer.php';

    }
}
