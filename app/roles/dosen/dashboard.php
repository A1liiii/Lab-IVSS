<?php
// app/roles/dosen/dashboard.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("dosen");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$active = "dashboard";
$title  = "Dashboard Dosen";

// Ambil user_id & nip dosen
$user_id = $_SESSION['user']['user_id'] ?? null;

$stmt = $conn->prepare("SELECT nip FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$nip = $row['nip'] ?? null;

// Default nilai
$totalMK = 0;
$totalBimbing = 0;
$totalPublikasi = 0;

// Jika dosen memiliki NIP
if ($nip) {

    // ==== Hitung Mata Kuliah ====
    $stmt = $conn->prepare("SELECT COUNT(*) FROM mata_kuliah WHERE nip = ?");
    $stmt->execute([$nip]);
    $totalMK = (int)$stmt->fetchColumn();

    // ==== Hitung Mahasiswa Bimbingan ====
    // mahasiswa.user_id = user_id dosen pembimbing
    $stmt = $conn->prepare("
        SELECT COUNT(*) 
        FROM mahasiswa 
        WHERE kategori = 'bimbingan' AND user_id = ?
    ");
    $stmt->execute([$user_id]);
    $totalBimbing = (int)$stmt->fetchColumn();

    // ==== Hitung Publikasi ====
    $stmt = $conn->prepare("SELECT COUNT(*) FROM publikasi WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $totalPublikasi = (int)$stmt->fetchColumn();
}

// ==== Ambil 5 aktivitas terbaru ====
$stmt = $conn->prepare("
    SELECT deskripsi, waktu 
    FROM log_activity 
    WHERE user_id = ?
    ORDER BY waktu DESC 
    LIMIT 5
");
$stmt->execute([$user_id]);
$recent = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<h2 class="fw-bold mb-4 text-primary d-flex align-items-center" style="font-size: 2rem;">
    <i class="bi bi-speedometer2 me-2"></i> Dashboard Dosen
</h2>

<style>
    /* === ULTRA MAX UI IMPROVEMENT === */
    .stat-card {
        border-radius: 16px;
        padding: 24px;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        transition: .25s ease;
        border: 1px solid #e9ecef;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.08);
    }
    .stat-icon {
        font-size: 2.2rem;
        color: #0d6efd;
        margin-right: 10px;
    }
    .timeline-item {
        position: relative;
        padding-left: 32px;
        margin-bottom: 20px;
    }
    .timeline-item::before {
        content: "";
        position: absolute;
        left: 12px;
        top: 6px;
        width: 10px;
        height: 10px;
        background: #0d6efd;
        border-radius: 50%;
    }
    .timeline-item::after {
        content: "";
        position: absolute;
        left: 16px;
        top: 20px;
        width: 2px;
        height: calc(100% - 20px);
        background: #dee2e6;
    }
    .timeline-item:last-child::after {
        display: none;
    }
</style>

<!-- STAT CARDS -->
<div class="row g-4 mb-5">

    <div class="col-md-4">
        <div class="stat-card shadow-sm">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-journal-bookmark stat-icon"></i>
                <span class="text-muted small">Mata Kuliah Diampu</span>
            </div>
            <h2 class="fw-bold"><?= $totalMK ?></h2>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card shadow-sm">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-people stat-icon"></i>
                <span class="text-muted small">Mahasiswa Bimbingan</span>
            </div>
            <h2 class="fw-bold"><?= $totalBimbing ?></h2>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card shadow-sm">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-file-earmark-text stat-icon"></i>
                <span class="text-muted small">Publikasi</span>
            </div>
            <h2 class="fw-bold"><?= $totalPublikasi ?></h2>
        </div>
    </div>

</div>

<!-- RECENT ACTIVITY -->
<div class="card p-4 shadow-sm mb-4" style="border-radius: 16px;">
    <h5 class="fw-bold mb-4 d-flex align-items-center">
        <i class="bi bi-clock-history me-2"></i> Aktivitas Terbaru
    </h5>

    <?php if (empty($recent)): ?>
        <p class="text-muted small">Belum ada aktivitas.</p>
    <?php else: ?>
        <?php foreach ($recent as $r): ?>
            <div class="timeline-item">
                <div class="fw-semibold"><?= htmlspecialchars($r['deskripsi']) ?></div>
                <div class="text-muted small">
                    <?= date("d M Y • H:i", strtotime($r['waktu'])) ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>


<?php
$content = ob_get_clean();
include "_layout.php";
?>
