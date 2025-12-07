<?php
// app/core/auth.php
// jangan panggil session_start() di file lain sebelum include ini
function isLoggedIn() {
    return isset($_SESSION['user']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../../../login.php");
        exit;
    }
}

// require bahwa user sudah login dan active_role cocok
function requireRole($role) {
    requireLogin();
    if (!isset($_SESSION['active_role']) || $_SESSION['active_role'] !== $role) {
        // akses ditolak
        http_response_code(403);
        echo "<h3>Akses ditolak — Anda tidak memiliki role: " . htmlentities($role) . "</h3>";
        echo '<p><a href="../../../select_role.php">Pilih role lain</a> atau <a href="../../../logout.php">Logout</a></p>';
        exit;
    }
}

// redirect sesuai role ke file dashboard/landing masing-masing
function redirectByRole($role) {
    $map = [
        'admin'     => 'app/roles/admin/dashboard.php',
        'operator'  => 'app/roles/operator/dashboard.php',
        'dosen'     => 'app/roles/dosen/dashboard.php',
        'mahasiswa' => 'app/roles/mahasiswa/dashboard.php',
        'ketua lab'    => 'app/roles/ka_lab/dashboard.php',
        'public'    => 'app/roles/public/home.php',
    ];

    if (isset($map[$role])) {
        header("Location: " . $map[$role]);
        exit;
    } else {
        // fallback -> public home
        header("Location: app/roles/public/home.php");
        exit;
    }
}
