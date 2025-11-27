<?php

require_once __DIR__ . '/../../models/Berita.php';

class OperatorBeritaDeleteController {

    public function index() {

        if (!isset($_GET['id'])) {
            echo "ID tidak ditemukan.";
            exit;
        }

        $id = $_GET['id'];

        $model = new Berita();

        // HAPUS FILE FOTO + PDF
        $data = $model->getById($id);

        if ($data) {

            // Hapus foto jika ada
            if (!empty($data['foto'])) {
                $pathFoto = __DIR__ . '/../../public/uploads/berita/' . $data['foto'];
                if (file_exists($pathFoto)) unlink($pathFoto);
            }

            // Hapus file_url jika ada
            if (!empty($data['file_url'])) {
                $pathFile = __DIR__ . '/../../public/uploads/berita/' . $data['file_url'];
                if (file_exists($pathFile)) unlink($pathFile);
            }
        }

        // Hapus dari database
        $model->delete($id);

        // Redirect kembali ke list
        header("Location: /lab-ivss/index.php?page=operator-berita");
        exit;
    }
}
