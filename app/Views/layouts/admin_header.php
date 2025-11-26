<?php require_once __DIR__ . '/../../Config/app.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title><?= isset($title) ? $title : 'Admin Panel - IVSS' ?></title>

  <!-- CSS -->
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/main.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>assets/vendor/bootstrap-icons/bootstrap-icons.css">

  <style>
        /* SIDEBAR */
    .admin-wrapper {
      display: flex;
      min-height: 100vh;
      background: var(--background-color);
    }

    .admin-sidebar {
      width: 260px;
      background: var(--surface-color);
      border-right: 1px solid color-mix(in srgb, var(--default-color), transparent 85%);
      padding: 20px;
    }

    .admin-sidebar h3 {
      color: var(--heading-color);
      font-size: 20px;
      margin-bottom: 20px;
    }

    .admin-sidebar a {
      display: block;
      padding: 10px 14px;
      margin-bottom: 6px;
      color: var(--default-color);
      font-weight: 500;
      border-radius: 4px;
      transition: 0.3s;
    }

    .admin-sidebar a:hover,
    .admin-sidebar .active {
      background: var(--accent-color);
      color: var(--contrast-color);
    }

    /* CONTENT */
    .admin-content {
      flex: 1;
      padding: 30px;
      background: var(--background-color);
    }

    .admin-card {
      background: var(--surface-color);
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 2px 18px rgba(0, 0, 0, 0.06);
      color: var(--default-color);
    }

    /* TABLE */
    table th {
      background: color-mix(in srgb, var(--accent-color), transparent 85%);
      color: var(--accent-color);
      font-weight: 600;
    }
  </style>
</head>

<body>
<div class="admin-wrapper">
