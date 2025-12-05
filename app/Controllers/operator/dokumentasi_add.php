<?php 

require_once __DIR__ . '/../../models/Dokumentasi.php';
require_once __DIR__ . '/../../Helper/LogHelper.php';

class OperatorDokumentasiAddController {

    public function index() {

        $title  = "Tambah Dokumentasi";
        $active = "dokumentasi";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $judul_kegiatan     = $_POST['judul_kegiatan'];
            $deskripsi_kegiatan = $_POST['deskripsi_kegiatan'];
            $tanggal_kegiatan   = $_POST['tanggal_kegiatan'];
            $jenis_kegiatan     = $_POST['jenis_kegiatan'];
            $user_id            = $_SESSION['user_id'];

            // ===== Upload File Utama =====
            $file_path = 'no-file.png'; // default jika tidak upload
            $type_file = null;

            if (!empty($_FILES['foto']['name'])) {

                $folder = __DIR__ . '/../../../public/uploads/dokumentasi/';
                if (!is_dir($folder)) mkdir($folder, 0755, true);

                $namaFile = time() . "_" . $_FILES['foto']['name'];
                move_uploaded_file($_FILES['foto']['tmp_name'], $folder . $namaFile);

                $file_path = $namaFile;
                $type_file = pathinfo($namaFile, PATHINFO_EXTENSION);
            }

            // ===== Simpan ke Database =====
            $m = new Dokumentasi();
            $m->insert(
                $file_path,
                $type_file,
                '', // caption diabaikan
                $judul_kegiatan,
                $deskripsi_kegiatan,
                $tanggal_kegiatan,
                $jenis_kegiatan,
                $user_id
            );

            addLog($_SESSION['user_id'], "Menambah dokumentasi baru", "INSERT");

            header("Location: /lab-ivss/index.php?page=operator-dokumentasi");
            exit;
        }

        // ===== TAMPILAN =====
        require_once __DIR__ . '/../../Views/layouts/operator_header.php';
        require_once __DIR__ . '/../../Views/layouts/operator_sidebar.php';
        require_once __DIR__ . '/../../Views/Operator/dokumentasi_add.php';
        require_once __DIR__ . '/../../Views/layouts/operator_footer.php';
    }
}
