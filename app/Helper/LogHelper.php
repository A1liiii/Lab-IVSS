<?php
require_once __DIR__ . '/../Models/Log.php';

function addLog($user_id, $deskripsi, $aksi) {
    $log = new Log();
    $log->add($user_id, $deskripsi, $aksi);
}
