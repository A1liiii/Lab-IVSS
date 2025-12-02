<?php
session_start(); 

require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Role.php';

class UserController {

    public function index() {
        $User  = new User();
        $Role  = new Role();

        $users = $User->getAll();
        $roles = $Role->getAll();

        // cek jika sedang edit
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

    public function edit($id) {
        $User  = new User();
        $Role  = new Role();

        $user  = $User->find($id);
        $roles = $Role->getAll();

        $title  = "Edit User";
        $active = "user";

        include __DIR__ . '/../../Views/layouts/admin_header.php';
        include __DIR__ . '/../../Views/layouts/admin_sidebar.php';
        include __DIR__ . '/../../Views/Admin/user_edit.php';
        include __DIR__ . '/../../Views/layouts/admin_footer.php';
    }

    public function update($data) {
    $User = new User();

    try {
        $User->updateUser($data);
    } catch (Exception $e) {
        // Jika ingin diam-diam gagal tanpa alert
        // cukup kosong
    }

    header("Location: index.php?page=admin-user");
    exit;
}
    public function delete($id) {
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
    public function store($data) {
    $User = new User();

    // ==========================
    // VALIDASI ROLE DIPILIH
    // ==========================
    if (!isset($data['role_id']) || empty($data['role_id'])) {
        $_SESSION['error'] = "Role harus dipilih!";
        header("Location: index.php?page=admin-user");
        exit;
    }

    // ==========================
    // VALIDASI NIM / NIP ADA DI DB
    // ==========================
    if (!$User->nimExists($data['nim'])) {
        $_SESSION['error'] = "NIM atau NIP tidak ditemukan di database!";
        header("Location: index.php?page=admin-user");
        exit;
    }

    // ==========================
    // MAPPING NIM / NIP BERDASARKAN ROLE
    // ==========================
    if ($data['role_id'] == 3) { 
        // DOSEN → pindahkan ke NIP
        $data['nip'] = $data['nim'];
        $data['nim'] = null;
    } 
    else if ($data['role_id'] == 4) {
        // MAHASISWA → gunakan NIM
        $data['nip'] = null;
    } 
    else {
        // ADMIN / OPERATOR → tidak punya nim/nip
        $data['nip'] = null;
        // tetap simpan nim karena form minta nim
    }

    // ==========================
    // INSERT USER
    // ==========================
    try {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
$user_id = $User->create($data);
        $User->assignRole($user_id, $data['role_id']);

        $_SESSION['success'] = "User berhasil ditambahkan!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
    }

    header("Location: index.php?page=admin-user");
    exit;
}

}
