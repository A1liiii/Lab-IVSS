<?php
require_once __DIR__ . "/app/core/auth.php";

if (!isLoggedIn()) {
    header("Location: /app/roles/public/home.php");
    exit;
}

redirectByRole($_SESSION['user']['role']);
