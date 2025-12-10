<?php
// app/roles/mahasiswa/dashboard.php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("mahasiswa");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$user_id = $_SESSION['user']['user_id'] ?? null;
if (!$user_id) die("User tidak ditemukan.");

// total aktivitas
$stmt = $conn->prepare("SELECT COUNT(*) FROM log_activity WHERE user_id = ?");
$stmt->execute([$user_id]);
$totalActivity = (int)$stmt->fetchColumn();

// pembimbing (jika ada)
$stmt = $conn->prepare("
    SELECT u.user_id, d.nama
    FROM users u
    LEFT JOIN dosen d ON d.nip = u.nip
    WHERE u.user_id = (
        SELECT pembimbing_user_id FROM mahasiswa WHERE user_id = ? LIMIT 1
    )
    LIMIT 1
");
$stmt->execute([$user_id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);

// render page
ob_start();
?>
<h2 class="fw-bold mb-4 text-primary"><i class="bi bi-speedometer2"></i> Dashboard Mahasiswa</h2>

<div class="row g-4 mb-4">
  <div class="col-md-4">
    <div class="card p-3 shadow-sm h-100">
      <div class="small text-muted">Total Aktivitas</div>
      <h3 class="fw-bold"><?= (int)$totalActivity ?></h3>
      <div class="small text-muted">(rekam aktivitas Anda)</div>
    </div>
  </div>

  <div class="col-md-8">
    <div class="card p-3 shadow-sm">
      <div class="d-flex justify-content-between align-items-center">
        <div><strong>Aktivitas Bulanan</strong></div>
        <div>
          <select id="chartYear" class="form-select form-select-sm" style="width:120px;display:inline-block;">
            <?php
            $currentYear = (int)date("Y");
            for($y = $currentYear; $y >= $currentYear-5; $y--):
            ?>
              <option value="<?= $y ?>" <?= $y===$currentYear ? "selected":"" ?>><?= $y ?></option>
            <?php endfor; ?>
          </select>
        </div>
      </div>

      <div style="height:240px;" class="mt-3">
        <canvas id="activityChart"></canvas>
      </div>
    </div>
  </div>
</div>

<?php if($p): ?>
  <div class="card p-3 shadow-sm mb-4">
    <strong>Pembimbing:</strong> <?= htmlspecialchars($p['nama'] ?? '-') ?>
  </div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const userId = <?= json_encode($user_id) ?>;
let chart = null;

function fetchChart(year){
  fetch('chart_mahasiswa_api.php?user_id=' + encodeURIComponent(userId) + '&tahun=' + encodeURIComponent(year))
    .then(r=>r.json())
    .then(json=>{
      const labels = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
      const data = new Array(12).fill(0);
      (json.data||[]).forEach(r=>{
        const b = parseInt(r.bulan,10);
        if(!isNaN(b) && b>=1 && b<=12) data[b-1]=parseInt(r.total,10);
      });

      const ctx = document.getElementById('activityChart').getContext('2d');
      if(chart) chart.destroy();
      chart = new Chart(ctx, {
        type: 'line',
        data: {
          labels,
          datasets: [{
            label: 'Aktivitas',
            data,
            fill:true,
            tension:0.3,
            borderWidth:3
          }]
        },
        options: { maintainAspectRatio:false, scales:{ y:{ beginAtZero:true } } }
      });
    });
}

document.getElementById('chartYear').addEventListener('change', (e)=> fetchChart(e.target.value));
fetchChart(document.getElementById('chartYear').value);
</script>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
