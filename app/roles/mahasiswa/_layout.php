<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/../../core/auth.php";
requireRole("mahasiswa");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Mahasiswa Panel</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* GLOBAL LAYOUT FIX */
        html, body {
            height: 100%;
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

    </style>
</head>

<body>

    <!-- TOPBAR -->
    <div class="topbar shadow-sm">
        <div class="topbar-title d-flex align-items-center gap-2">
            <i class="bi bi-backpack-fill"></i> Mahasiswa Panel
        </div>

        <div class="d-flex align-items-center gap-3">
            <span class="text-secondary">
                <i class="bi bi-person-circle"></i> <?= $_SESSION['user']['username'] ?>
            </span>

        </div>
    </div>

    <div class="d-flex">

        <!-- SIDEBAR -->
        <div class="sidebar">
            <h5>Menu Mahasiswa</h5>

            <a href="dashboard.php">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <a href="profile.php">
                <i class="bi bi-person-badge"></i> Profil Saya
            </a>

            <a href="logs.php">
                <i class="bi bi-clock-history"></i> Aktivitas
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
        <main class="flex-grow-1 p-4">
            <?= $content ?? "" ?>
        </main>

    </div>

</body>
</html>
