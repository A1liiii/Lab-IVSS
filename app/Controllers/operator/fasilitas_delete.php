<?php

require_once __DIR__ . '/../../models/Fasilitas.php';
require_once __DIR__ . '/../../Helper/LogHelper.php';

class OperatorFasilitasDeleteController {

    public function index() {

        if (!isset($_GET['id'])) {
            die("ID tidak ditemukan!");
        }

        $id = $_GET['id'];
        $m = new Fasilitas();

        $m->delete($id);

        addLog($_SESSION['user_id'], "Menghapus fasilitas ID $id", "DELETE");

        header("Location: index.php?page=operator-fasilitas");
        exit;
    }
}

