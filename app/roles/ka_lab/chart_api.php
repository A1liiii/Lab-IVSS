<?php
require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$tahun = $_GET['tahun'] ?? date("Y");
$bulan = $_GET['bulan'] ?? "";

if ($bulan !== "") {
    $sql = "
        SELECT EXTRACT(DAY FROM waktu) AS hari, COUNT(*) AS total
        FROM log_activity
        WHERE EXTRACT(YEAR FROM waktu) = ?
        AND EXTRACT(MONTH FROM waktu) = ?
        GROUP BY hari
        ORDER BY hari
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$tahun, $bulan]);

    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
    $data = array_fill(1, $daysInMonth, 0);

    while($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[(int)$r['hari']] = (int)$r['total'];
    }

    echo json_encode([
        "labels" => range(1, $daysInMonth),
        "data" => array_values($data)
    ]);
} else {
    $sql = "
        SELECT EXTRACT(MONTH FROM waktu) AS bulan, COUNT(*) AS total
        FROM log_activity
        WHERE EXTRACT(YEAR FROM waktu) = ?
        GROUP BY bulan
        ORDER BY bulan
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$tahun]);

    $data = array_fill(1, 12, 0);

    while($r = $stmt->fetch(PDO::FETCH_ASSOC)){
        $data[(int)$r['bulan']] = (int)$r['total'];
    }

    echo json_encode([
        "labels" => ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"],
        "data" => array_values($data)
    ]);
}
