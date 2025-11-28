<?php

require_once __DIR__ . '/../../models/Proyek.php';
require_once __DIR__ . '/../../models/AnggotaProyek.php';

class OperatorProyekDeleteController {

    public function index() {
        $id = $_GET['proyek_id'];

        $anggota = new AnggotaProyek();
        $anggota->deleteByProject($id);

        $proyek = new Proyek();
        $proyek->delete($id);

        header("Location: index.php?page=operator-proyek");
        exit;
    }
}
