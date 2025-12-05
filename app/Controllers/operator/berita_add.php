<?php
// Mulai session di paling atas file
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set default session sementara jika belum ada
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = 'Operator'; // Nama default sementara
}
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // ID default sementara
}

require_once __DIR__ . '/../../models/Berita.php';
require_once __DIR__ . '/../../Helper/LogHelper.php';

class OperatorBeritaAddController {

    public function index() {
        $title  = "Tambah Berita";
        $active = "berita";

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $judul     = trim($_POST['judul']);
            $deskripsi = trim($_POST['deskripsi']);
            $kategori  = $_POST['kategori'];
            $user_id   = $_SESSION['user_id'];
            // Tanggal post, default hari ini jika kosong
            if (isset($_POST['tgl_post']) && !empty($_POST['tgl_post'])) {
                $tanggal = $_POST['tgl_post'];
            } else {
                $tanggal = date('Y-m-d'); // tanggal sekarang
            }            

            $uploadPath = __DIR__ . '/../../../public/uploads/berita/';
            if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);

            // FOTO
            $foto = null;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
                $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $foto = "foto_" . time() . "." . $ext;
                move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPath.$foto);
            }

            // INSERT KE DB
            $model = new Berita();
            $model->insert($judul, $deskripsi, $foto, $file_url, $kategori, $tanggal, $user_id);

            addLog($user_id, "Menambah berita baru: $judul", "ADD");

            header("Location: /lab-ivss/index.php?page=operator-berita");
            exit;
        }

        require_once __DIR__ . '/../../Views/layouts/operator_header.php';
        require_once __DIR__ . '/../../Views/layouts/operator_sidebar.php';
        require_once __DIR__ . '/../../Views/Operator/berita_add.php';
        require_once __DIR__ . '/../../Views/layouts/operator_footer.php';
    }
}
