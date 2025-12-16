<?php
$title  = "Anggota | IVSS";
$active = "team";

ob_start();
require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

/* ============================================================
   ROLE PRIORITY
============================================================ */
function rolePriority(string $role): int {
    return match (strtolower($role)) {
        'ketua lab' => 1,
        'admin'     => 2,
        'operator'  => 3,
        'dosen'     => 4,
        'mahasiswa' => 5,
        default     => 9
    };
}

/* ============================================================
   DOSEN
============================================================ */
$sqlDosen = "
    SELECT
        d.nip AS id_anggota,
        d.nama,
        u.user_id,
        'dosen' AS tipe,
        COALESCE(r.role_name,'dosen') AS role_name
    FROM dosen d
    LEFT JOIN users u        ON u.nip = d.nip
    LEFT JOIN user_roles ur ON ur.user_id = u.user_id
    LEFT JOIN roles r       ON r.role_id = ur.role_id
";
$dosen = $conn->query($sqlDosen)->fetchAll(PDO::FETCH_ASSOC);

/* ============================================================
   MAHASISWA
============================================================ */
$sqlMhs = "
    SELECT
        m.nim AS id_anggota,
        m.nama,
        u.user_id,
        'mahasiswa' AS tipe,
        COALESCE(r.role_name,'mahasiswa') AS role_name,
        m.status
    FROM mahasiswa m
    LEFT JOIN users u        ON u.nim = m.nim
    LEFT JOIN user_roles ur ON ur.user_id = u.user_id
    LEFT JOIN roles r       ON r.role_id = ur.role_id
";
$mahasiswa = $conn->query($sqlMhs)->fetchAll(PDO::FETCH_ASSOC);

/* ============================================================
   MERGE + PRIORITY + SORT
============================================================ */
$raw = array_merge($dosen, $mahasiswa);
$map = [];

foreach ($raw as $r) {
    $key = $r['user_id'] ?? ($r['tipe'].'-'.$r['id_anggota']);

    if (!isset($map[$key])) {
        $map[$key] = $r;
    } else {
        if (rolePriority($r['role_name']) < rolePriority($map[$key]['role_name'])) {
            $map[$key]['role_name'] = $r['role_name'];
        }
    }
}

$anggota = array_values($map);

usort($anggota, function($a,$b){
    return rolePriority($a['role_name']) <=> rolePriority($b['role_name']);
});
?>

<style>
/* ================= FILTER ================= */
.anggota-filter-group{text-align:center;margin-bottom:1.5rem}
.anggota-filter-btn{
    background:none;border:none;padding:6px 14px;
    font-weight:600;color:#64748b;cursor:pointer
}
.anggota-filter-btn.active{color:#facc15}

/* ================= CARD ================= */
.team-member{
    background:#fff;
    border-radius:18px;
    padding:18px 16px 22px;
    box-shadow:0 8px 20px rgba(15,23,42,.06);
    text-align:center;
    height:100%;
    display:flex;
    flex-direction:column;
}

.member-img{
    width:100%;
    height:220px;
    overflow:hidden;
    border-radius:14px;
    background:#f1f5f9;
    flex-shrink:0;
}

.member-img img{
    width:100%;
    height:100%;
    object-fit:cover;
}

/* ================= INFO FIX HEIGHT ================= */
.member-info{
    margin-top:12px;
    flex:1;
    display:flex;
    flex-direction:column;
    justify-content:flex-end;
}

.member-info h4{
    font-size:1rem;
    font-weight:700;
    line-height:1.25;
    margin-bottom:6px;

    display:-webkit-box;
    -webkit-line-clamp:2;   /* MAX 2 BARIS */
    -webkit-box-orient:vertical;
    overflow:hidden;
    min-height:2.6em;       /* BIKIN TINGGI SAMA */
}

.member-info span{
    font-size:.75rem;
    color:#64748b;
    text-transform:uppercase;
    letter-spacing:.04em;
}
</style>

<section id="team" class="team section">
<div class="container section-title">
    <p>Anggota Lab</p>
</div>

<div class="container">

<div class="anggota-filter-group">
    <button class="anggota-filter-btn active" data-filter="all">All</button>
    <button class="anggota-filter-btn" data-filter="dosen">Dosen</button>
    <button class="anggota-filter-btn" data-filter="mahasiswa">Mahasiswa</button>
    <button class="anggota-filter-btn" data-filter="alumni">Alumni</button>
</div>

<div class="row gy-4">

<?php foreach ($anggota as $row):

$tipe = $row['tipe'];

if ($tipe==='dosen') {
    $filter='dosen';
} elseif ($tipe==='mahasiswa' && strtolower($row['status']??'')==='lulus') {
    $filter='alumni';
} else {
    $filter='mahasiswa';
}

$foto = "../../../public/uploads/profiles/".($row['user_id']??0).".jpg";
$default = "../../../public/assets/img/default-user.png";
$link = "anggota_detail.php?tipe={$tipe}&id={$row['id_anggota']}";
?>

<div class="col-lg-3 col-md-6 anggota-item" data-type="<?= $filter ?>">
<a href="<?= $link ?>" class="text-decoration-none text-dark h-100 d-block">
<div class="team-member">
    <div class="member-img">
        <img src="<?= $foto ?>?v=<?= time() ?>" onerror="this.src='<?= $default ?>'">
    </div>
    <div class="member-info">
        <h4><?= htmlspecialchars($row['nama']) ?></h4>
        <span><?= strtoupper($row['role_name']) ?></span>
    </div>
</div>
</a>
</div>

<?php endforeach; ?>

</div>
</div>
</section>

<script>
document.querySelectorAll(".anggota-filter-btn").forEach(btn=>{
  btn.onclick=()=>{
    document.querySelectorAll(".anggota-filter-btn").forEach(b=>b.classList.remove("active"));
    btn.classList.add("active");
    const f=btn.dataset.filter;
    document.querySelectorAll(".anggota-item").forEach(i=>{
      i.classList.toggle("d-none",!(f==="all"||i.dataset.type===f));
    });
  }
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . "/_layout.php";
?>
