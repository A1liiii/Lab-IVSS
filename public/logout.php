<?php
session_start();

// Hapus session
session_unset();
session_destroy();

// Pastikan tidak ada parameter page=login tertinggal
if (isset($_GET['page'])) {
    header("Location: login.php");
    exit;
}

header("Location: login.php");
exit;