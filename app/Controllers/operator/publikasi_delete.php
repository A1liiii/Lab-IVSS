<?php

require_once __DIR__ . '/../../models/Publikasi.php';

class OperatorPublikasiDeleteController {

    public function index() {

        if (!isset($_GET['id'])) {
            die("ID tidak ditemukan!");
        }

        $id = $_GET['id'];

        $m = new Publikasi();
        $m->delete($id);

        header("Location: index.php?page=operator-publikasi");
        exit;
    }
}
