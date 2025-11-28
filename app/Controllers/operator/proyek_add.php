<?php

require_once __DIR__ . '/../../models/Proyek.php';
require_once __DIR__ . '/../../models/User.php';

class OperatorProyekAddController {

    public function index() {

        $userModel = new User();
        $users = $userModel->getAll();

        $title = "Tambah Proyek";
        $active = "proyek";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $judul = $_POST['judul'];
            $deskripsi = $_POST['deskripsi'];
            $mulai = $_POST['tanggal_mulai'];
            $selesai = $_POST['tanggal_selesai'];
            $status = $_POST['status'];

            // anggota
            $anggota = [];
            if (!empty($_POST['user_id'])) {
                foreach ($_POST['user_id'] as $i => $uid) {
                    $anggota[] = [
                        'user_id' => $uid,
                        'role' => $_POST['role'][$i]
                    ];
                }
            }

            $proyek = new Proyek();
            $proyek->insertWithMembers($judul, $deskripsi, $mulai, $selesai, $status, $anggota);

            header("Location: index.php?page=operator-proyek");
            exit;
        }

        require 'app/Views/layouts/operator_header.php';
        require 'app/Views/layouts/operator_sidebar.php';
        require 'app/Views/operator/proyek_add.php';
        require 'app/Views/layouts/operator_footer.php';
    }
}
