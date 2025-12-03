<?php

require_once __DIR__ . '/../../models/Dokumentasi.php';
require_once __DIR__ . '/../../Helper/LogHelper.php';

class OperatorDokumentasiDeleteController {

    public function index() {

        if (!isset($_GET['documentation_id'])) {
            echo "ID tidak ditemukan.";
            exit;
        }

        $id = $_GET['documentation_id'];

        $m = new Dokumentasi();

        $data = $m->getById($id);

        if ($data) {
            $folder = __DIR__ . '/../../public/uploads/dokumentasi/';
            $file = $folder . $data['file_path'];

            if (file_exists($file)) unlink($file);
        }

        $m->delete($id);

        addLog($_SESSION['user_id'], "Menghapus dokumentasi ID $id", "DELETE");

        header("Location: /lab-ivss/index.php?page=operator-dokumentasi");
        exit;
    }
}
