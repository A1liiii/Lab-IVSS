<?php
if (session_status() === PHP_SESSION_NONE) session_start();
requireRole(["admin","ketua lab","dosen","operator"]);
require_once __DIR__ . "/../../core/auth.php";
// requireRole("dosen");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();
$roles = $_SESSION['roles'] ?? [];
$onlyDosen = (count($roles) === 1 && in_array('dosen', $roles, true));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? "Dosen Panel" ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* GLOBAL LAYOUT FIX */
        html, body {
            height: 100%;
            overflow: hidden; /* agar sidebar tidak nge-scroll body */
        }

        body {
            display: flex;
            flex-direction: column;
            background-color: #f5f8ff;
        }

        /* ROOT COLORS */
        :root {
            --blue: #004aad;
            --yellow: #ffde59;
        }

        /* TOPBAR */
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

        /* LAYOUT WRAPPER / CONTENT BODY */
        .layout-wrapper {
            height: calc(100vh - 63px); /* full layout minus topbar */
            display: flex;
            overflow: hidden;
        }

        /* SIDEBAR */
        .sidebar {
            flex-shrink: 0;
            width: 240px;
            background: var(--blue);
            padding: 25px 15px;
            color: #fff;
            height: 100%;
            overflow-y: auto;           /* kalau menu panjang, sidebar bisa scroll */
            scrollbar-width: thin;
            min-height: calc(100vh - 63px);
            box-shadow: inset -2px 0 6px rgba(0,0,0,0.05);
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

        /* MAIN CONTENT */
        main {
            flex-grow: 1;
            padding-bottom: 0 !important;
            overflow-y: auto;            /* scroll hanya area konten */
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

        .modal-dialog-scrollable .modal-body {
            overflow-y: auto !important;
            max-height: calc(100vh - 200px); /* bebas, tapi ini paling ideal */
        }

        .modal-open {
            overflow: hidden !important; /* body di-lock seperti biasa */
        }

        body.modal-open main {
            overflow: hidden !important; /* cegah double-scroll */
        }

    </style>
</head>

<body>

    <!-- TOPBAR -->
    <div class="topbar shadow-sm">
        <div class="topbar-title d-flex align-items-center gap-2 fw-bold text-primary">
            <i class="bi bi-mortarboard-fill"></i> Dosen Panel
        </div>

        <div class="d-flex align-items-center gap-3">
            <a href="/Lab-ivss/app/roles/dosen/profile.php"
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
            <h5 class="mb-3">Menu Dosen</h5>

            <a href="dashboard.php" class="<?= ($active=='dashboard'?'active':'') ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <a href="bimbingan.php" class="<?= ($active=='bimbingan'?'active':'') ?>">
                <i class="bi bi-people"></i> Daftar Bimbingan
            </a>

            <a href="publikasi.php" class="<?= ($active=='publikasi'?'active':'') ?>">
                <i class="bi bi-journal-text"></i> Publikasi Saya
            </a>

            <a href="mata_kuliah.php" class="<?= ($active=='mata_kuliah'?'active':'') ?>">
                <i class="bi bi-journal-bookmark-fill"></i> Mata Kuliah
            </a>

            <a href="logs.php" class="<?= ($active=='logs'?'active':'') ?>">
                <i class="bi bi-clock-history"></i> Aktivitas
            </a>

            <a href="profile.php" class="<?= ($active=='profile'?'active':'') ?>">
                <i class="bi bi-gear"></i> Profil Saya
            </a>

            <hr style="border-color: rgba(255,255,255,0.3)">

            <?php if (!$onlyDosen): ?>
            <a href="../../../select_role.php">
                <i class="bi bi-arrow-left-right"></i> Switch Role
            </a>
            <?php endif; ?>

            <a href="../../../logout.php">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>

        <!-- MAIN CONTENT -->
        <main class="flex-grow-1 p-4">
            <?php 
            require_once __DIR__ . "/../../core/notification.php";
            echo showReminder();
            ?>

            <?= $content ?? "" ?>
        </main>

    </div>

</body>
</html>
