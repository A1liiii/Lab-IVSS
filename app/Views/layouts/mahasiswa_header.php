<?php require_once __DIR__ . '/../../Config/app.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?php echo isset($title) ? $title : 'Dashboard Mahasiswa - IVSS'; ?></title>

    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/vendor/bootstrap-icons/bootstrap-icons.css">

    <style>
        .mhs-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .mhs-sidebar {
            width: 240px;
            background: var(--surface-color);
            border-right: 1px solid rgba(0,0,0,0.1);
            padding: 20px;
        }

        .mhs-sidebar h3 {
            color: var(--heading-color);
            font-size: 20px;
            margin-bottom: 20px;
        }

        .mhs-sidebar a {
            display: block;
            padding: 10px 14px;
            margin-bottom: 6px;
            color: var(--default-color);
            font-weight: 500;
            border-radius: 4px;
            transition: 0.3s;
            text-decoration: none;
        }

        .mhs-sidebar a:hover,
        .mhs-sidebar .active {
            background: var(--accent-color);
            color: var(--contrast-color);
        }

        .mhs-content {
            flex: 1;
            padding: 30px;
            background: var(--background-color);
        }
    </style>
</head>

<body>
<div class="mhs-wrapper">
