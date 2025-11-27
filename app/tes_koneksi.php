<?php

require_once __DIR__ . "/app/config/database.php";

try {
    $conn = Database::connect();
    echo "Koneksi ke PostgreSQL BERHASIL!";
} catch (Exception $e) {
    echo "Koneksi GAGAL: " . $e->getMessage();
}