<?php

require_once __DIR__ . '/../../models/Proyek.php';

class OperatorProyekDeleteController {

    public function index() {

        $id = $_GET['id'];

        $m = new Proyek();
        $m->delete($id);

        header("Location: index.php?page=operator-proyek");
        exit;
    }
}
