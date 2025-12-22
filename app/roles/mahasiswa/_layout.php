<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/../../core/auth.php";
requireRole("mahasiswa");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();
$roles = $_SESSION['roles'] ?? [];
$onlyMhs = (count($roles) === 1 && in_array('mahasiswa', $roles, true));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <link href="../../../public/assets/img/logo_ivss2.png" rel="icon">
    <meta charset="UTF-8">
    <title><?= $title ?? "Mahasiswa Panel" ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="../../../public/assets/img/logo_ivss2.png" rel="icon">
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
/* ================= GLOBAL FIX ================= */
html, body {
    height: 100%;
    overflow: hidden; /* FIX utama: sidebar & konten tidak bikin body scroll */
}

body {
    display: flex;
    flex-direction: column;
    background-color: #f5f8ff;
}

:root {
    --blue: #004aad;
    --yellow: #ffde59;
}

/* ================= TOPBAR ================= */
.topbar {
    background: #ffffff;
    border-bottom: 1px solid #e6e6e6;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* ================= LAYOUT WRAPPER ================= */
.layout-wrapper {
    height: calc(100vh - 63px);
    display: flex;
    overflow: hidden;
}

/* ================= SIDEBAR ================= */
.sidebar {
    width: 240px;
    background: var(--blue);
    padding: 25px 15px;
    color: #fff;
    overflow-y: auto;
    scrollbar-width: thin;
}

.sidebar a {
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    padding: 10px 12px;
    border-radius: 10px;
    margin-bottom: 8px;
    font-weight: 500;
    transition: .2s;
}

.sidebar a:hover {
    background: var(--yellow);
    color: #000;
    transform: translateX(5px);
}

.sidebar .active {
    background: var(--yellow);
    color: #000 !important;
    font-weight: 700;
}

/* ================= MAIN CONTENT ================= */
main {
    flex-grow: 1;
    overflow-y: auto; /* hanya area konten yang scroll */
    padding: 25px;
    scrollbar-width: thin;
}

main::-webkit-scrollbar,
.sidebar::-webkit-scrollbar {
    width: 6px;
}

main::-webkit-scrollbar-thumb,
.sidebar::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.2);
    border-radius: 6px;
}

main::-webkit-scrollbar-thumb:hover,
.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(0,0,0,0.35);
}
</style>
</head>

<body>

    <!-- TOPBAR -->
    <div class="topbar shadow-sm">
        <div class="topbar-title d-flex align-items-center gap-2 fw-bold text-primary">
            <i class="bi bi-backpack-fill"></i> Mahasiswa Panel
        </div>

    <div class="d-flex align-items-center gap-3">
        <a href="profile.php"
        class="text-secondary text-decoration-none">
            <i class="bi bi-person-circle"></i>
            <?= htmlspecialchars($_SESSION['user']['username']) ?>
        </a>
    </div>
    </div>

    <!-- WRAPPER -->
    <div class="layout-wrapper">

        <!-- SIDEBAR -->
        <div class="sidebar">
            <h5 class="mb-3">Menu Mahasiswa</h5>

            <a href="dashboard.php" class="<?= ($active=='dashboard'?'active':'') ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <a href="profile.php" class="<?= ($active=='profile'?'active':'') ?>">
                <i class="bi bi-person-badge"></i> Profil Saya
            </a>

            <a href="logs.php" class="<?= ($active=='logs'?'active':'') ?>">
                <i class="bi bi-clock-history"></i> Aktivitas
            </a>

            <hr style="border-color: rgba(255,255,255,0.3)">

            <?php if (!$onlyMhs): ?>
            <a href="../../../select_role.php">
                <i class="bi bi-arrow-left-right"></i> Switch Role
            </a>
            <?php endif; ?>

            <a href="../../../logout.php">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>

        <!-- MAIN CONTENT -->
        <main>
            <?php 
            require_once __DIR__ . "/../../core/notification.php";
            echo showReminder();
            ?>

            <?= $content ?? "" ?>
        </main>

    </div>

</body>
</html>
