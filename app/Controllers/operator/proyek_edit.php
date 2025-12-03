<?php
require_once __DIR__ . '/../../models/Proyek.php';
require_once __DIR__ . '/../../models/User.php';

class OperatorProyekEditController {

    public function index() {

        // ===== MODEL
        $m = new Proyek();
        $mu = new User();

        // ===== Ambil ID
        $id = $_GET['id'] ?? null;
        if (!$id) {
            die("ID proyek tidak ditemukan.");
        }

        // ===== Jika submit update
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $m->update($id, [
                'judul'          => $_POST['judul'],
                'deskripsi'      => $_POST['deskripsi'],
                'tanggal_mulai'  => $_POST['tanggal_mulai'],
                'tanggal_selesai'=> $_POST['tanggal_selesai'],
                'status'         => $_POST['status']
            ]);

            // Hapus semua anggota dulu
            $m->deleteAnggota($id);

            // Insert ulang anggota
            if (!empty($_POST['user_id'])) {
                foreach ($_POST['user_id'] as $i => $user_id) {

                    if (!$user_id) continue;

                    $role = $_POST['role'][$i];

                    $m->insertAnggota([
                        'proyek_id' => $id,
                        'user_id'   => $user_id,
                        'role'      => $role
                    ]);
                }
            }

            header("Location: /lab-ivss/index.php?page=operator-proyek");
            exit;
        }

        // ===== Ambil data proyek
        $data = $m->getById($id);

        // ===== Ambil anggota proyek
        $anggota = $m->getAnggota($id);

        // ===== Ambil user untuk dropdown
        $users = $mu->getAll();

        // ===== Tampilkan view
        $title  = "Edit Proyek";
        $active = "proyek";

        require_once __DIR__ . '/../../Views/layouts/operator_header.php';
        require_once __DIR__ . '/../../Views/layouts/operator_sidebar.php';
        require_once __DIR__ . '/../../Views/Operator/proyek_edit.php';
        require_once __DIR__ . '/../../Views/layouts/operator_footer.php';
    }
}
