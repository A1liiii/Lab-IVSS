<?php
session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("ketua lab");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

function safe($x){ return htmlspecialchars($x ?? "-", ENT_QUOTES,'UTF-8'); }
function timeFormat($ts){ return date("d M Y • H:i", strtotime($ts)); }

/* ============================
    FILTER INPUT
============================ */
$search = $_GET['search'] ?? "";
$start  = $_GET['start'] ?? "";
$end    = $_GET['end'] ?? "";

/* ============================
    PAGINATION
============================ */
$per_page = 3;
$page = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * $per_page;

/* ============================
    BUILD QUERY
============================ */

$where = [];
$params = [];

if ($search != "") {
    $where[] = "(COALESCE(d.nama,m.nama,'Unknown') ILIKE ? OR l.deskripsi ILIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($start != "" && $end != "") {
    $where[] = "l.waktu BETWEEN ? AND ?";
    $params[] = $start . " 00:00:00";
    $params[] = $end   . " 23:59:59";
}

$filterQuery = $where ? "WHERE ".implode(" AND ", $where) : "";

/* ============================
    HITUNG TOTAL DATA
============================ */
$countSQL = "
    SELECT COUNT(*)
    FROM log_activity l
    LEFT JOIN users u ON u.user_id = l.user_id
    LEFT JOIN dosen d ON d.nip = u.nip
    LEFT JOIN mahasiswa m ON m.nim = u.nim
    $filterQuery
";

$stmtCount = $conn->prepare($countSQL);
$stmtCount->execute($params);
$total = $stmtCount->fetchColumn();
$total_pages = ceil($total / $per_page);

/* ============================
    FETCH DATA LOG PAGINATED
============================ */
$sql = "
    SELECT 
        l.log_id, l.user_id, l.deskripsi, l.aksi, l.waktu,
        COALESCE(d.nama, m.nama, 'Unknown User') AS nama
    FROM log_activity l
    LEFT JOIN users u ON u.user_id = l.user_id
    LEFT JOIN dosen d ON d.nip = u.nip
    LEFT JOIN mahasiswa m ON m.nim = u.nim
    $filterQuery
    ORDER BY l.waktu DESC
    LIMIT $per_page OFFSET $offset
";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<h2 class="fw-bold mb-4 text-primary">
    <i class="bi bi-clock-history"></i> Aktivitas Sistem
</h2>

<!-- FILTER BAR -->
<div class="card p-3 mb-3 shadow-sm border-0">
    <form method="GET" class="row g-2 align-items-end">

        <div class="col-md-4">
            <label class="small fw-semibold">Search</label>
            <input type="text" name="search" class="form-control"
                value="<?=safe($search)?>" placeholder="Cari nama atau aksi...">
        </div>

        <div class="col-md-3">
            <label class="small fw-semibold">Tanggal Mulai</label>
            <input type="date" name="start" class="form-control" value="<?=safe($start)?>">
        </div>

        <div class="col-md-3">
            <label class="small fw-semibold">Tanggal Akhir</label>
            <input type="date" name="end" class="form-control" value="<?=safe($end)?>">
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">
                <i class="bi bi-search"></i> Filter
            </button>
        </div>

    </form>
</div>

<style>
.log-card {
    background:#fff;
    border-radius:12px;
    padding:14px;
    display:flex;
    gap:14px;
    border-left:5px solid #004aad;
    transition:.2s;
}
.log-card:hover {
    transform: translateX(4px);
    box-shadow:0 4px 14px rgba(0,0,0,.08);
}
.log-avatar {
    width:42px;
    height:42px;
    border-radius:50%;
    object-fit:cover;
}
.log-meta { font-size:12px; color:#999; }
</style>


<div class="d-flex flex-column gap-3">

<?php if(empty($logs)): ?>
    <div class="text-center text-muted py-5">
        <i class="bi bi-inboxes"></i> Tidak ada aktivitas ditemukan.
    </div>
<?php else: ?>

    <?php foreach($logs as $log): ?>
        <div class="log-card shadow-sm">

            <img src="../../../public/uploads/profiles/<?=safe($log['user_id'])?>.jpg"
                onerror="this.src='../../../public/assets/img/default-user.png';"
                class="log-avatar">

            <div>
                <strong><?=safe($log['nama'])?></strong>
                <div class="log-meta"><?=timeFormat($log['waktu'])?></div>
                <div class="mt-1"><?=safe($log['deskripsi'])?></div>
            </div>
        </div>
    <?php endforeach; ?>

<?php endif; ?>

</div>

<!-- PAGINATION -->
<?php
$totalPages = ceil($total / $per_page);
$current    = max(1, intval($_GET['page'] ?? 1));

if ($totalPages > 1):
?>

<nav class="d-flex justify-content-center mt-4">
    <ul class="pagination">

        <!-- PREVIOUS -->
        <li class="page-item <?=($current <= 1 ? 'disabled' : '')?>">
            <a class="page-link"
               href="?page=<?=($current - 1)?>&search=<?=safe($search)?>&start=<?=safe($start)?>&end=<?=safe($end)?>">
                Previous
            </a>
        </li>

        <!-- PAGE NUMBERS -->
        <?php for($p = 1; $p <= $totalPages; $p++): ?>
            <li class="page-item <?=($current == $p ? 'active' : '')?>">
                <a class="page-link"
                   href="?page=<?=$p?>&search=<?=safe($search)?>&start=<?=safe($start)?>&end=<?=safe($end)?>">
                    <?=$p?>
                </a>
            </li>
        <?php endfor; ?>

        <!-- NEXT -->
        <li class="page-item <?=($current >= $totalPages ? 'disabled' : '')?>">
            <a class="page-link"
               href="?page=<?=($current + 1)?>&search=<?=safe($search)?>&start=<?=safe($start)?>&end=<?=safe($end)?>">
                Next
            </a>
        </li>

    </ul>
</nav>

<?php endif; ?>


<?php
$content = ob_get_clean();
include "_layout.php";
?>
