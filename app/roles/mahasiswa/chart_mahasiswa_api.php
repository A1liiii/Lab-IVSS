<?php
// app/roles/mahasiswa/chart_mahasiswa_api.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("mahasiswa");
header('Content-Type: application/json');

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$user_id = $_GET['user_id'] ?? $_SESSION['user']['user_id'] ?? null;
$tahun = intval($_GET['tahun'] ?? date("Y"));

$stmt = $conn->prepare("
  SELECT EXTRACT(MONTH FROM waktu) AS bulan, COUNT(*) AS total
  FROM log_activity
  WHERE user_id = ? AND EXTRACT(YEAR FROM waktu) = ?
  GROUP BY bulan ORDER BY bulan
");
$stmt->execute([$user_id, $tahun]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['data'=>$data]);
