<?php

require_once __DIR__ . '/../../models/Proyek.php';
require_once __DIR__ . '/../../models/AnggotaProyek.php';
require_once __DIR__ . '/../../Config/database.php';

class OperatorProyekAddController {

    public function index() {

        $title  = "Tambah Proyek";
        $active = "proyek";

        // FIX: Gunakan koneksi DB yang benar
        $db = Database::connect();

        // Ambil user dosen + mahasiswa
        // Ambil user dosen + mahasiswa, kecuali admin
        $users = $db->query("
        SELECT user_id, username AS nama, nip, nim
        FROM users
        WHERE username <> 'admin'          -- admin di exclude
        AND (nip IS NOT NULL OR nim IS NOT NULL)  -- hanya dosen / mahasiswa
        ORDER BY username ASC
        ")->fetchAll(PDO::FETCH_ASSOC);


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $mProyek  = new Proyek();
            $mAnggota = new AnggotaProyek();

            // Insert proyek dan ambil ID langsung dari model
            $proyek_id = $mProyek->insert($_POST);

            // simpan anggota
            if (!empty($_POST['user_id'])) {

                $list = [];

                foreach ($_POST['user_id'] as $i => $uid) {
                    $list[] = [
                        'user_id' => !empty($uid) ? $uid : null,
                        'role' => isset($_POST['role'][$i]) ? $_POST['role'][$i] : null
                    ];
                }

                $mAnggota->insertBatch($proyek_id, $list);
            }

            header("Location: index.php?page=operator-proyek");
            exit;
        }

        require_once __DIR__ . '/../../Views/layouts/operator_header.php';
        require_once __DIR__ . '/../../Views/layouts/operator_sidebar.php';
        require_once __DIR__ . '/../../Views/Operator/proyek_add.php';
        require_once __DIR__ . '/../../Views/layouts/operator_footer.php';
    }
}
