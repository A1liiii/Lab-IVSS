<?php
$active = "dashboard";
$title  = "Dashboard Operator";

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

// ====== STAT KARTU DASHBOARD OPERATOR ====== //

// Total Berita
$result = $conn->query("SELECT COUNT(*) AS total FROM berita")->fetch();
$totalBerita = isset($result['total']) ? $result['total'] : 0;

// Total Proyek
$result = $conn->query("SELECT COUNT(*) AS total FROM proyek")->fetch();
$totalProyek = isset($result['total']) ? $result['total'] : 0;

// Total Fasilitas
$result = $conn->query("SELECT COUNT(*) AS total FROM fasilitas")->fetch();
$totalFasilitas = isset($result['total']) ? $result['total'] : 0;

// Total Dokumentasi
$result = $conn->query("SELECT COUNT(*) AS total FROM act_documentation")->fetch();
$totalDokumentasi = isset($result['total']) ? $result['total'] : 0;

// Tahun tersedia untuk filter grafik (pakai log_activity)
$stmtYear = $conn->query("
    SELECT DISTINCT EXTRACT(YEAR FROM waktu) AS tahun
    FROM log_activity
    ORDER BY tahun ASC
");
$availableYears = $stmtYear->fetchAll(PDO::FETCH_COLUMN);

ob_start();
?>

<h2 class="fw-bold mb-4 text-primary">
    <i class="bi bi-speedometer2"></i> Dashboard Operator
</h2>

<!-- STAT CARDS -->
<div class="row g-4 mb-4">
    <!-- Total Berita -->
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 p-3 stat-card" style="border-left: 6px solid #004aad;">
            <div>
                <h6 class="text-muted mb-1">Total Berita</h6>
                <h3 class="fw-bold text-primary mb-0"><?= $totalBerita ?></h3>
            </div>
            <i class="bi bi-newspaper fs-3 text-primary"></i>
        </div>
    </div>

    <!-- Total Proyek -->
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 p-3 stat-card" style="border-left: 6px solid #ffb100;">
            <div>
                <h6 class="text-muted mb-1">Total Proyek</h6>
                <h3 class="fw-bold text-warning mb-0"><?= $totalProyek ?></h3>
            </div>
            <i class="bi bi-kanban fs-3 text-warning"></i>
        </div>
    </div>

    <!-- Total Fasilitas -->
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 p-3 stat-card" style="border-left: 6px solid #4caf50;">
            <div>
                <h6 class="text-muted mb-1">Total Fasilitas</h6>
                <h3 class="fw-bold text-success mb-0"><?= $totalFasilitas ?></h3>
            </div>
            <i class="bi bi-building fs-3 text-success"></i>
        </div>
    </div>

    <!-- Total Dokumentasi -->
    <div class="col-lg-3 col-md-6">
        <div class="card shadow-sm border-0 p-3 stat-card" style="border-left: 6px solid #ff4d4d;">
            <div>
                <h6 class="text-muted mb-1">Total Dokumentasi</h6>
                <h3 class="fw-bold text-danger mb-0"><?= $totalDokumentasi ?></h3>
            </div>
            <i class="bi bi-image-fill fs-3 text-danger"></i>
        </div>
    </div>
</div>

<hr class="my-4">

<!-- GRAPH SECTION -->
<div class="card shadow-sm border-0 p-4 position-relative">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-semibold m-0">
            <i class="bi bi-graph-up-arrow"></i> Aktivitas Pengelolaan Konten
        </h5>

        <div class="filter-wrapper">
            <select id="filterTahun" class="filter-select">
                <?php foreach($availableYears as $tahun): ?>
                    <option value="<?= $tahun ?>"><?= $tahun ?></option>
                <?php endforeach; ?>
            </select>

            <select id="filterBulan" class="filter-select">
                <option value="">Semua Bulan</option>
                <?php
                $bulanList = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
                foreach($bulanList as $i => $b): ?>
                    <option value="<?= $i+1 ?>"><?= $b ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div style="height: 240px;">
        <canvas id="activityChart"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let activityChart = null;

function fetchChartData() {
    const tahun = document.getElementById("filterTahun").value;
    const bulan = document.getElementById("filterBulan").value;

    // sementara pakai endpoint yang sama dengan admin
    fetch(`chart_api.php?tahun=${tahun}&bulan=${bulan}`)
        .then(res => res.json())
        .then(response => updateChart(response.labels, response.data));
}

function updateChart(labels, data) {
    if (activityChart) activityChart.destroy();

    const ctx = document.getElementById("activityChart").getContext("2d");

    activityChart = new Chart(ctx, {
        type: "line",
        data: {
            labels,
            datasets: [{
                label: "Log Aktivitas",
                data,
                borderColor: "#004aad",
                backgroundColor: "rgba(0, 74, 173, 0.15)",
                fill: true,
                tension: 0.35,
                borderWidth: 3,
                pointRadius: 4,
                pointBackgroundColor: "#004aad",
            }]
        },
        options: {
            animation: { duration: 400, easing: "easeOutQuart" },
            maintainAspectRatio: false,
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });
}

document.getElementById("filterTahun").addEventListener("change", fetchChartData);
document.getElementById("filterBulan").addEventListener("change", fetchChartData);

// auto load chart pertama kali
fetchChartData();
</script>

<style>
main {
    min-height: calc(100vh - 120px);
    padding-bottom: 0 !important;
}
.stat-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-radius: 14px;
    transition: 0.2s;
}
.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}

/* FILTER STYLE */
.filter-select {
    border: none !important;
    padding: 6px 14px !important;
    background: #eef3ff !important;
    color: #003d87 !important;
    font-weight: 500;
    border-radius: 20px !important;
    cursor: pointer;
    transition: all .25s ease;
    box-shadow: inset 0 0 0 1px #d0ddff;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg fill='%23004aad' viewBox='0 0 16 16' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M4 6l4 4 4-4z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-size: 14px;
    background-position: right 10px center;
}
.filter-select:hover { background: #dce8ff !important; }
.filter-select:focus {
    outline: none !important;
    box-shadow: 0 0 6px rgba(0, 85, 255, 0.25) !important;
    background: white !important;
}
.filter-wrapper {
    display: flex;
    gap: 10px;
    align-items: center;
    background: white;
    padding: 6px 10px;
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.06);
    transition: .25s ease;
}
.filter-wrapper:hover {
    box-shadow: 0 5px 14px rgba(0,0,0,0.12);
}
</style>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
