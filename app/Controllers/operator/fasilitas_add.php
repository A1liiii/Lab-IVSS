<?php 

require_once __DIR__ . '/../../models/Fasilitas.php';
require_once __DIR__ . '/../../Helper/LogHelper.php';

class OperatorFasilitasAddController {

    public function index() {

        // Mulai session jika belum
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $title  = "Tambah Fasilitas";
        $active = "fasilitas";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $m = new Fasilitas();

            // Ambil data dari form
            $user_id   = $_SESSION['user_id'];
            $nama      = $_POST['nama'];
            $deskripsi = $_POST['deskripsi'];
            $status    = $_POST['status'];

            // Pastikan kategori sesuai enum di DB
            $validKategori = ['fasilitas', 'peralatan'];
            $kategori = in_array($_POST['kategori'], $validKategori) ? $_POST['kategori'] : null;

            // --- Upload FOTO ---
            $foto = null;
            if (!empty($_FILES['foto']['name'])) {
                $folder = __DIR__ . '/../../../public/uploads/fasilitas/';
                if (!is_dir($folder)) mkdir($folder, 0777, true);

                $namaFile = time() . "_" . basename($_FILES['foto']['name']);
                move_uploaded_file($_FILES['foto']['tmp_name'], $folder . $namaFile);

                $foto = $namaFile;
            }

            // Insert ke DB
            $m->insert($user_id, $nama, $deskripsi, $status, $foto, $kategori);

            // Log aktivitas
            addLog($user_id, "Menambah fasilitas baru", "INSERT");

            // Redirect
            header("Location: index.php?page=operator-fasilitas");
            exit;
        }

        // ===== TAMPILAN =====
        require_once __DIR__ . '/../../Views/layouts/operator_header.php';
        require_once __DIR__ . '/../../Views/layouts/operator_sidebar.php';
        require_once __DIR__ . '/../../Views/Operator/fasilitas_add.php';
        require_once __DIR__ . '/../../Views/layouts/operator_footer.php';
    }
}
