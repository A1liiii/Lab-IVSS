<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../../core/auth.php";
requireRole("ketua lab");
require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Ketua Lab Panel' ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* GLOBAL LAYOUT FIX */
        html, body {
            height: 100%;
            overflow: hidden; /* mencegah seluruh body ikut scroll */
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

        /* WRAPPER BODY */
        .layout-wrapper {
            height: calc(100vh - 63px); /* minus tinggi topbar */
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
            overflow-y: auto;
            scrollbar-width: thin;
            min-height: calc(100vh - 63px);
            box-shadow: inset -2px 0 6px rgba(0,0,0,0.05);
        }

        .sidebar h5 {
            font-weight: 600;
            margin-bottom: 15px;
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
            padding: 25px;
            overflow-y: auto;
            scrollbar-width: thin;
        }

        /* Scrollbar */
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
            <i class="bi bi-speedometer2"></i> Ketua Lab Panel
        </div>

        <div class="d-flex align-items-center gap-3">
            <span class="text-secondary">
                <i class="bi bi-person-circle"></i> <?= $_SESSION['user']['username'] ?>
            </span>
        </div>
    </div>

    <!-- BODY WRAPPER -->
    <div class="layout-wrapper">

        <!-- SIDEBAR -->
        <div class="sidebar">

            <h5>Menu Ketua Lab</h5>

            <a href="dashboard.php" class="<?= ($active=='dashboard'?'active':'') ?>">
                <i class="bi bi-bar-chart-line"></i> Dashboard
            </a>

            <a href="approvals.php" class="<?= ($active=='approvals'?'active':'') ?>">
                <i class="bi bi-file-check"></i> Approval Anggota Baru
            </a>

            <a href="bimbingan.php" class="<?= ($active=='bimbingan'?'active':'') ?>">
                <i class="bi bi-people"></i> Monitoring Bimbingan
            </a>

            <a href="publikasi.php" class="<?= ($active=='publikasi'?'active':'') ?>">
                <i class="bi bi-journal-text"></i> Publikasi Lab
            </a>

            <a href="logs.php" class="<?= ($active=='logs'?'active':'') ?>">
                <i class="bi bi-clock-history"></i> Log Sistem
            </a>

            <a href="profile.php" class="<?= ($active=='profile'?'active':'') ?>">
                <i class="bi bi-gear"></i> Profil Saya
            </a>

            <hr style="border-color: rgba(255,255,255,0.3)">

            <a href="../../../select_role.php">
                <i class="bi bi-arrow-left-right"></i> Switch Role
            </a>
            <a href="../../../logout.php">
                <i class="bi bi-box-arrow-right"></i> Keluar
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
