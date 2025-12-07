<?php
session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("ketua lab");
require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ketua Lab Panel</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #004aad;
            --accent-yellow: #ffde59;
        }

        body {
            background-color: #f5f8ff;
        }

        /* SIDEBAR */
        .sidebar {
            width: 240px;
            background: var(--primary-blue);
            min-height: 100vh;
            padding: 20px 15px;
            color: #fff;
        }

        .sidebar a {
            color: #ffffff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 500;
            margin-bottom: 8px;
            transition: 0.2s;
        }

        .sidebar a:hover {
            background: var(--accent-yellow);
            color: #000;
            transform: translateX(4px);
        }

        /* TOPBAR */
        .topbar {
            background: #ffffff;
            border-bottom: 1px solid #e5e5e5;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .title {
            font-size: 20px;
            font-weight: 600;
            color: var(--primary-blue);
        }

        .logout-btn {
            color: #fff;
            background: var(--primary-blue);
        }

        .logout-btn:hover {
            background: #00357e;
        }
    </style>
</head>

<body>

    <!-- TOPBAR -->
    <div class="topbar shadow-sm">
        <div class="title d-flex align-items-center gap-2">
            <i class="bi bi-speedometer2"></i> Ketua Lab Panel
        </div>

        <div class="d-flex align-items-center gap-3">
            <span class="text-secondary">
                <i class="bi bi-person-circle"></i>
                <?= $_SESSION['user']['username'] ?>
            </span>

            <a href="../../../logout.php" class="btn logout-btn btn-sm px-3">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>

    <div class="d-flex">

        <!-- SIDEBAR -->
        <div class="sidebar">

            <h5 class="mb-3">Menu Ketua Lab</h5>

            <a href="dashboard.php"><i class="bi bi-bar-chart-line"></i> Dashboard</a>
            <a href="approvals.php"><i class="bi bi-file-check"></i> Approval Anggota Baru</a>
            <a href="bimbingan.php"><i class="bi bi-people"></i> Monitoring Bimbingan</a>
            <a href="publikasi.php"><i class="bi bi-journal-text"></i> Publikasi Lab</a>
            <a href="logs.php"><i class="bi bi-clock-history"></i> Log Sistem</a>
            <a href="profile.php"><i class="bi bi-gear"></i> Profil Saya</a>

            <hr style="border-color: rgba(255,255,255,0.3)">

            <a href="../../../select_role.php"><i class="bi bi-arrow-left-right"></i> Switch Role</a>
            <a href="../../../logout.php"><i class="bi bi-box-arrow-right"></i> Keluar</a>

        </div>

        <!-- MAIN CONTENT -->
        <main class="flex-grow-1 p-4">
            <div class="text-center text-secondary mt-5">
                <h3><i class="bi bi-mortarboard-fill"></i> Selamat Datang Ketua Lab</h3>
                <p>Pilih menu di sebelah kiri untuk mengelola sistem laboratorium.</p>
            </div>
        </main>

    </div>

</body>
</html>
