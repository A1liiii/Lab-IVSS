<?php
// app/roles/operator/_layout.php
// Session, auth, dan $conn sudah di-handle di file halaman (dashboard.php, berita.php, dst)

$username = isset($_SESSION['user']['username']) ? $_SESSION['user']['username'] : 'Operator';
$active   = isset($active) ? $active : '';
$title    = isset($title)  ? $title  : 'Operator Panel';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --blue: #004aad;
            --yellow: #ffde59;
        }

        body {
            background-color: #f5f8ff;
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

        /* SIDEBAR */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: var(--blue);
            padding: 25px 15px;
            color: #fff;
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

        .sidebar a.active {
            background: #ffde59;
            color: #000;
        }

        .logout-btn {
            background: var(--blue);
            color: #fff;
            border: none;
        }

        .logout-btn:hover {
            background: #00378a;
            color: #fff;
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

            <a href="../../../logout.php" class="btn logout-btn btn-sm px-3">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>

    <div class="d-flex">

        <!-- SIDEBAR -->
        <div class="sidebar">
            <h5>Menu Operator</h5>

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

            <a href="profile.php" class="<?= $active === 'profile' ? 'active' : '' ?>">
                <i class="bi bi-gear"></i> Profil Saya
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
        <?= !empty($content) ? $content : "" ?>
        </main>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
