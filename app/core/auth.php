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
function requireRole($requiredRoles) {
    requireLogin(); // pastikan sudah login

    if (!isset($_SESSION['active_role'])) {
        forbidden("Role aktif tidak ditemukan.");
    }

    $current = strtolower($_SESSION['active_role']);

    // jika yang dikirim 1 role string -> ubah jadi array
    if (!is_array($requiredRoles)) {
        $requiredRoles = [$requiredRoles];
    }

    // normalisasi value array required role
    $requiredRoles = array_map("strtolower", $requiredRoles);

    if (!in_array($current, $requiredRoles)) {
        forbidden("Anda tidak memiliki akses ke role ini.");
    }
}

// fungsi helper clean utk forbidden 
function forbidden($message) {
    http_response_code(403);
    echo "<h3>Akses ditolak — $message</h3>";
    echo '<p><a href="../../../select_role.php">Pilih role lain</a> atau <a href="../../../logout.php">Logout</a></p>';
    exit;
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
