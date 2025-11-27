<?php 

require_once __DIR__ . '/../../models/Fasilitas.php';

class OperatorFasilitasAddController {

    public function index() {

        $title  = "Tambah Fasilitas";
        $active = "fasilitas";

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
                $m = new Fasilitas();
    
                $user_id = $_SESSION['user_id']; // nanti ganti session
                $nama      = $_POST['nama'];
                $deskripsi = $_POST['deskripsi'];
                $status    = $_POST['status'];
                $kategori  = $_POST['kategori'];
    
                // --- Upload FOTO ---
                $foto = null;
    
                if (!empty($_FILES['foto']['name'])) {
                    $folder = __DIR__ . '/../../public/uploads/fasilitas/';
                    if (!is_dir($folder)) mkdir($folder, 0777, true);
    
                    $namaFile = time() . "_" . $_FILES['foto']['name'];
                    move_uploaded_file($_FILES['foto']['tmp_name'], $folder . $namaFile);
    
                    $foto = $namaFile;
                }
    
                $m->insert($user_id, $nama, $deskripsi, $status, $foto, $kategori);
    
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


