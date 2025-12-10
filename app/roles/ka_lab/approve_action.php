<?php
session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("ketua lab"); // security check

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$regId = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;
$userId = $_SESSION['user']['user_id'];

if(!$regId || !in_array($action, ['approve','reject'])) {
    header("Location: approvals.php");
    exit;
}

$status = ($action == "approve") ? "approved" : "rejected";

try {
    $stmt = $conn->prepare("
        UPDATE registrations
        SET status = ?,
            approved_by = ?,
            approved_at = NOW()
        WHERE reg_id = ?
    ");

    $stmt->execute([$status, $userId, $regId]);

} catch(Exception $e) {
    echo "Error: " . $e->getMessage();
    exit;
}

header("Location: approvals.php");
exit;
