<?php
session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("dosen");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$user_id = $_SESSION['user']['user_id'] ?? null;

if(!$user_id){
    die("<div class='alert alert-danger'>Session invalid — user tidak ditemukan.</div>");
}

// Fetch dosen
$stmt = $conn->prepare("
    SELECT d.nip, d.nama, d.email, d.jabatan  
    FROM users u
    LEFT JOIN dosen d ON d.user_id = u.user_id
    WHERE u.user_id = ?
    LIMIT 1
");
$stmt->execute([$user_id]);
$dosen = $stmt->fetch(PDO::FETCH_ASSOC);

$nip = $dosen['nip'] ?? "-";

// Count publikasi
$qPublikasi = $conn->prepare("SELECT COUNT(*) FROM publikasi WHERE user_id = ?");
$qPublikasi->execute([$user_id]);
$totalPublikasi = $qPublikasi->fetchColumn();

// Count matkul
$qMatkul = $conn->prepare("SELECT COUNT(*) FROM mata_kuliah WHERE nip = ?");
$qMatkul->execute([$nip]);
$totalMatkul = $qMatkul->fetchColumn();

// Count bimbingan
$qBimbingan = $conn->prepare("SELECT COUNT(*) FROM mahasiswa WHERE kategori = 'bimbingan'");
$qBimbingan->execute();
$totalBimbingan = $qBimbingan->fetchColumn();

// Logs
$qLogs = $conn->prepare("SELECT aksi, deskripsi, waktu FROM log_activity WHERE user_id = ? ORDER BY waktu DESC LIMIT 5");
$qLogs->execute([$user_id]);
$logs = $qLogs->fetchAll(PDO::FETCH_ASSOC);

function safe($x){
    return htmlspecialchars($x ?? "-", ENT_QUOTES, 'UTF-8');
}

ob_start();
?>

<style>
/* ULTRA STYLE */
.stat-card {
    background: #fff;
    border-radius: 18px;
    padding: 25px;
    box-shadow: 0 10px 28px rgba(0,0,0,0.08);
    transition: .25s;
    cursor: pointer;
    border-top: 5px solid #004aad;
}

.stat-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 14px 30px rgba(0,0,0,0.12);
}

.stat-icon {
    font-size: 48px;
    color: #004aad;
    opacity: .9;
}

.hero-box {
    background: linear-gradient(135deg, #004aad, #006be6);
    padding: 35px 30px;
    border-radius: 18px;
    color: white;
    box-shadow: 0 12px 30px rgba(0,0,0,0.18);
}

.hero-box h2 {
    font-weight: 800;
}

.log-item {
    padding: 13px 10px;
    border-radius: 10px;
    transition: .2s;
}

.log-item:hover {
    background: #f0f5ff;
    transform: translateX(4px);
}

.divider {
    border-bottom: 2px solid #e3e7ff;
    margin: 18px 0;
}
</style>

<!-- HERO HEADER -->
<div class="hero-box mb-4">
    <h2 class="mb-1">Selamat Datang, <?= safe($dosen['nama']) ?></h2>
    <p class="mb-0">Pantau aktivitas akademik, publikasi, mata kuliah, dan bimbingan Anda.</p>
</div>

<!-- STATISTICS SECTION -->
<div class="row g-4">

    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="stat-icon"><i class="bi bi-journal-text"></i></div>
            <h5 class="fw-bold mt-2">Publikasi Ilmiah</h5>
            <h1 class="fw-bold text-primary"><?= $totalPublikasi ?></h1>
            <small class="text-muted">Total karya Anda di sistem</small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="stat-icon"><i class="bi bi-book-half"></i></div>
            <h5 class="fw-bold mt-2">Mata Kuliah</h5>
            <h1 class="fw-bold text-primary"><?= $totalMatkul ?></h1>
            <small class="text-muted">Matkul yang Anda ampu</small>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <h5 class="fw-bold mt-2">Mahasiswa Bimbingan</h5>
            <h1 class="fw-bold text-primary"><?= $totalBimbingan ?></h1>
            <small class="text-muted">Jumlah mahasiswa aktif</small>
        </div>
    </div>

</div>

<div class="divider"></div>

<!-- LATEST LOGS -->
<div class="panel-card mt-3">
    <h5 class="fw-bold mb-3"><i class="bi bi-clock-history"></i> Aktivitas Terbaru Anda</h5>

    <?php if(empty($logs)): ?>
        <p class="text-muted fst-italic">Belum ada aktivitas tercatat.</p>
    <?php else: ?>
        <?php foreach($logs as $log): ?>
            <div class="log-item d-flex justify-content-between align-items-start">
                <div>
                    <strong><?= safe($log['aksi']) ?></strong><br>
                    <span class="text-muted"><?= safe($log['deskripsi']) ?></span>
                </div>
                <span class="text-muted"><?= date("d M Y • H:i", strtotime($log['waktu'])) ?></span>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
