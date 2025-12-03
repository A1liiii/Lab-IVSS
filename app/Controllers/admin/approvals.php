<?php

require_once __DIR__ . '/../../models/Mahasiswa.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Approval.php';

class ApprovalsController {

    public function index() {
        $Approval = new Approval();
        $pendaftar = $Approval->getPending();

        $title  = "Approval - IVSS";
        $active = "approvals";

        include __DIR__ . '/../../Views/layouts/admin_header.php';
        include __DIR__ . '/../../Views/layouts/admin_sidebar.php';
        include __DIR__ . '/../../Views/Admin/approvals.php';
        include __DIR__ . '/../../Views/layouts/admin_footer.php';
    }

    public function approve($id) {
    session_start();

    $Approval  = new Approval();
    $Mahasiswa = new Mahasiswa();
    $User      = new User();

    // Ambil data pendaftar
    $data = $Approval->find($id);

    if ($data && $data['status'] === 'pending') {
        // 1. Masukkan ke tabel mahasiswa dulu
        $Mahasiswa->create([
            'nim'          => $data['nim'],
            'user_id'      => null,
            'nama'         => $data['nama'],
            'email'        => $data['email'],
            'prodi'        => $data['prodi'],
            'angkatan'     => $data['angkatan'],
            'status'       => 'aktif',
            'foto'         => null,
            'kategori'     => 'riset',
            'tanggal_join' => date('Y-m-d')
        ]);

        // 2. Masukkan ke tabel users
        $passwordHash = password_hash($data['nim'], PASSWORD_BCRYPT);
        $user_id = $User->create([
            'nim'      => $data['nim'],
            'username' => $data['nama'],   // username = nama
            'password' => $passwordHash    // password = nim (hashed)
        ]);

        // 3. Assign role mahasiswa
        $role_mahasiswa_id = 4; // ganti sesuai id role "mahasiswa" di tabel role
        $User->assignRole($user_id, $role_mahasiswa_id);

        // 4. Update status pendaftar di tabel registrations
        $admin_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // user_id admin/dosen
        $Approval->setApproved($id, $admin_user_id);
    }

    // Redirect kembali ke daftar approvals
    header("Location: index.php?page=admin-approvals");
    exit;
}



}

