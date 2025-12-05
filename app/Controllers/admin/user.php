<?php
session_start(); 

require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Role.php';
require_once __DIR__ . '/../../Config/database.php';

class UserController {

    // =========================================
    // HALAMAN USER MANAGEMENT
    // =========================================
    public function index() {
        $User  = new User();
        $Role  = new Role();

        $users = $User->getAll();
        $roles = $Role->getAll();

        // Jika sedang edit user
        $userEdit = null;
        if (isset($_GET['edit'])) {
            $userEdit = $User->find($_GET['edit']);
        }

        $title  = "User Management";
        $active = "user";

        include __DIR__ . '/../../Views/layouts/admin_header.php';
        include __DIR__ . '/../../Views/layouts/admin_sidebar.php';
        include __DIR__ . '/../../Views/Admin/user.php';
        include __DIR__ . '/../../Views/layouts/admin_footer.php';
    }
    // =========================================
    // UPDATE USER
    // =========================================
    public function update($data) {

        $User = new User();

        try {
            $User->updateUser($data);
            $_SESSION['success'] = "User berhasil diperbarui!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Gagal update user: " . $e->getMessage();
        }

        header("Location: index.php?page=admin-user");
        exit;
    }


    // =========================================
    // HAPUS USER
    // =========================================
    public function delete($id) {
        if (!$id) {
            $_SESSION['error'] = "ID user tidak valid!";
            header("Location: index.php?page=admin-user");
            exit;
        }

        $User = new User();

        try {
            $User->delete($id);
            $_SESSION['success'] = "User berhasil dihapus!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Gagal menghapus user: " . $e->getMessage();
        }

        header("Location: index.php?page=admin-user");
        exit;
    }
    // =========================================
    // TAMBAH DOSEN
    // =========================================

    public function addDosen() {
    $title = "Tambah Dosen";
    $active = "user";

    include __DIR__ . '/../../Views/layouts/admin_header.php';
    include __DIR__ . '/../../Views/layouts/admin_sidebar.php';
    include __DIR__ . '/../../Views/Admin/dosen_add.php';
    include __DIR__ . '/../../Views/layouts/admin_footer.php';
    }

    public function storeDosen($data) {
    $User = new User();
    // INSERT ke tabel dosen
    $sql = "INSERT INTO dosen (nip, nama, nidn, email, jabatan, pendidikan, foto)
            VALUES (:nip, :nama, :nidn, :email, :jabatan, :pendidikan, :foto)";

    $stmt = Database::connect()->prepare($sql);
    $stmt->execute([
        ':nip'         => $data['nip'],
        ':nama'        => $data['nama'],
        ':nidn'        => $data['nidn'],
        ':email'       => $data['email'],
        ':jabatan'     => $data['jabatan'],
        ':pendidikan'  => $data['pendidikan'],
        ':foto'        => $data['foto'] ?? null
    ]);

    // Redirect ke form tambah user
    header("Location: index.php?page=admin-user-create&nip=" . $data['nip']);
    exit;
    }

    public function store($data) {
    $User = new User();

    try {
        $user_id = $User->create([
            'nip'      => $data['identitas'], // NIP DOSEN
            'nim'      => null,
            'username' => $data['username'], // NIDN
            'password' => $data['password']  // NIP
        ]);

        $User->assignRole($user_id, $data['role_id']);

        $_SESSION['success'] = "User dosen berhasil dibuat!";
        header("Location: index.php?page=admin-user");
        exit;

    } catch (Exception $e) {
        $_SESSION['error'] = "Gagal membuat user: " . $e->getMessage();
        header("Location: index.php?page=admin-user");
        exit;
    }
}


    public function createUserFromDosen($nip = null) {

    if (!$nip) {
        $_SESSION['error'] = "Data dosen tidak ditemukan!";
        header("Location: index.php?page=admin-user");
        exit;
    }

    $conn = Database::connect();

    $stmt = $conn->prepare("SELECT * FROM dosen WHERE nip = ?");
    $stmt->execute([$nip]);
    $dosen = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dosen) {
        $_SESSION['error'] = "Dosen tidak ditemukan!";
        header("Location: index.php?page=admin-user");
        exit;
    }

    $d = $dosen; // untuk view

    $title  = "Tambah User Dari Dosen";
    $active = "user";

    include __DIR__ . '/../../Views/layouts/admin_header.php';
    include __DIR__ . '/../../Views/layouts/admin_sidebar.php';
    include __DIR__ . '/../../Views/admin/user_create_from_dosen.php';
    include __DIR__ . '/../../Views/layouts/admin_footer.php';
}

}
