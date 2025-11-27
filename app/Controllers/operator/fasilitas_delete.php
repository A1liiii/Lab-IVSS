<?php

require_once __DIR__ . '/../../models/Fasilitas.php';

class OperatorFasilitasDeleteController {

    public function index() {

        if (!isset($_GET['id'])) {
            die("ID tidak ditemukan!");
        }

        $id = $_GET['id'];
        $m = new Fasilitas();

        $m->delete($id);

        header("Location: index.php?page=operator-fasilitas");
        exit;
    }
}

