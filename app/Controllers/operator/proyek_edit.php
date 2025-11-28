<?php

require_once __DIR__ . '/../../models/Proyek.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/AnggotaProyek.php';

class OperatorProyekEditController {

    public function index() {
        $id = $_GET['proyek_id'];

        $proyekModel = new Proyek();
        $userModel = new User();
        $anggotaModel = new AnggotaProyek();

        $proyek = $proyekModel->find($id);
        $users = $userModel->getAll();
        $anggota = $anggotaModel->getByProject($id);

        $title = "Edit Proyek";
        $active = "proyek";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $judul = $_POST['judul'];
            $deskripsi = $_POST['deskripsi'];
            $mulai = $_POST['tanggal_mulai'];
            $selesai = $_POST['tanggal_selesai'];
            $status = $_POST['status'];

            $proyekModel->update($id, $judul, $deskripsi, $mulai, $selesai, $status);

            // Tambah anggota baru
            if (!empty($_POST['user_id'])) {
                foreach ($_POST['user_id'] as $i => $uid) {
                    if (!empty($uid)) {
                        $anggotaModel->insert($id, $uid, $_POST['role'][$i]);
                    }
                }
            }

            header("Location: index.php?page=operator-proyek");
            exit;
        }

        require 'app/Views/layouts/operator_header.php';
        require 'app/Views/layouts/operator_sidebar.php';
        require 'app/Views/operator/proyek_edit.php';
        require 'app/Views/layouts/operator_footer.php';
    }
}
