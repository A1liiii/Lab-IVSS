<?php

require_once __DIR__ . '/../../models/Publikasi.php';
require_once __DIR__ . '/../../Helper/LogHelper.php';

class OperatorPublikasiDeleteController {

    public function index() {

        if (!isset($_GET['id'])) {
            die("ID tidak ditemukan!");
        }

        $id = $_GET['id'];

        $m = new Publikasi();
        $m->delete($id);

        addLog($_SESSION['user_id'], "Menghapus publikasi ID $id", "DELETE");

        header("Location: index.php?page=operator-publikasi");
        exit;
    }
}
