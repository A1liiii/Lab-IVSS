<?php
session_start();
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

    $Approval  = new Approval();
    $Mahasiswa = new Mahasiswa();
    $User      = new User();

    // Ambil data pendaftaran
    $data = $Approval->find($id);

    if (!$data || empty($data['nim'])) {
        die("ERROR: NIM tidak ditemukan!");
    }

    // Insert mahasiswa dulu
    $Mahasiswa->create([
        'nim'          => $data['nim'],
        'user_id'      => null,
        'nama'         => $data['nama'],
        'email'        => $data['email'],
        'prodi'        => $data['prodi'],
        'angkatan'     => $data['angkatan'],
        'status'       => 'aktif',
        'kategori'     => 'riset',
        'tanggal_join' => date('Y-m-d')
    ]);

    // Baru buat user
    $user_id = $User->create([
        'nim'      => $data['nim'],
        'username' => $data['nama'],
        'password' => password_hash($data['nim'], PASSWORD_BCRYPT)
        
    ]);
    $User->assignRole($user_id, 4); 

    // Update mahasiswa.user_id
    $Mahasiswa->updateUserID($data['nim'], $user_id);

    // Update status pendaftaran
    $Approval->setApproved($id, $_SESSION['user_id']);

    header("Location: index.php?page=admin-approvals&success=1");
    exit;
}
}

