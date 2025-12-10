<?php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . "/../../core/auth.php";
requireRole("operator");

$username = $_SESSION['user']['username'] ?? "Operator";
$active   = $active ?? "";
$title    = $title  ?? "Operator Panel";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
/* ================= GLOBAL FIX ================= */
html, body {
    height: 100%;
    overflow: hidden; /* Sidebar dan content scroll sendiri */
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

.topbar-title {
    font-size: 21px;
    font-weight: 700;
    color: var(--blue);
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
    box-shadow: inset -2px 0 6px rgba(0,0,0,0.05);
}

.sidebar a {
    color: white;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 12px;
    text-decoration: none;
    border-radius: 10px;
    margin-bottom: 8px;
    font-weight: 500;
    transition: .2s;
}

.sidebar a:hover {
    background: var(--yellow);
    color: black;
    transform: translateX(5px);
}

.sidebar .active {
    background: var(--yellow);
    color: black !important;
    font-weight: 700;
}

/* ================= MAIN CONTENT ================= */
main {
    flex-grow: 1;
    overflow-y: auto; /* hanya content yg scroll */
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
        <div class="topbar-title d-flex align-items-center gap-2">
            <i class="bi bi-hdd-rack-fill"></i> Operator Panel
        </div>

        <div class="d-flex align-items-center gap-3">
            <span class="text-secondary">
                <i class="bi bi-person-circle"></i> <?= htmlspecialchars($username) ?>
            </span>
        </div>
    </div>

    <!-- CONTENT WRAPPER -->
    <div class="layout-wrapper">

        <!-- SIDEBAR -->
        <div class="sidebar">
            <h5 class="mb-3">Menu Operator</h5>

            <a href="dashboard.php" class="<?= $active === 'dashboard' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <a href="berita.php" class="<?= $active === 'berita' ? 'active' : '' ?>">
                <i class="bi bi-newspaper"></i> Berita
            </a>

            <a href="proyek.php" class="<?= $active === 'proyek' ? 'active' : '' ?>">
                <i class="bi bi-kanban"></i> Proyek
            </a>

            <a href="fasilitas.php" class="<?= $active === 'fasilitas' ? 'active' : '' ?>">
                <i class="bi bi-building-gear"></i> Fasilitas
            </a>

            <a href="dokumentasi.php" class="<?= $active === 'dokumentasi' ? 'active' : '' ?>">
                <i class="bi bi-camera-reels"></i> Dokumentasi
            </a>

            <hr style="border-color: rgba(255,255,255,0.3)">

            <a href="../../../select_role.php">
                <i class="bi bi-arrow-left-right"></i> Switch Role
            </a>

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
