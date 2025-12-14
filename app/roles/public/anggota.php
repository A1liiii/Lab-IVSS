<?php
$title  = "Anggota | IVSS";
$active = "team";

ob_start();

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

/*
 RULE:
 - filter hanya all / dosen / mahasiswa / alumni
 - role utama (ketua lab/admin/operator) MENANG
 - tidak boleh dobel
 - detail_anggota.php tetap
*/

/* ============================================================
   1. DOSEN
============================================================ */
$sqlDosen = "
    SELECT 
        d.nip AS id_anggota,
        d.nama,
        u.user_id,
        d.jabatan,
        NULL AS kategori,
        NULL AS status_mhs,
        'dosen' AS tipe,
        COALESCE(r.role_name, 'dosen') AS role_name
    FROM dosen d
    LEFT JOIN users u ON u.nip = d.nip
    LEFT JOIN user_roles ur ON ur.user_id = u.user_id
    LEFT JOIN roles r ON r.role_id = ur.role_id
";
$dosen = $conn->query($sqlDosen)->fetchAll(PDO::FETCH_ASSOC);

/* ============================================================
   2. MAHASISWA
============================================================ */
$sqlMhs = "
    SELECT
        m.nim AS id_anggota,
        m.nama,
        u.user_id,
        'Mahasiswa' AS jabatan,
        m.kategori,
        m.status AS status_mhs,
        'mahasiswa' AS tipe,
        COALESCE(r.role_name, 'mahasiswa') AS role_name
    FROM mahasiswa m
    LEFT JOIN users u ON u.nim = m.nim
    LEFT JOIN user_roles ur ON ur.user_id = u.user_id
    LEFT JOIN roles r ON r.role_id = ur.role_id
";
$mahasiswa = $conn->query($sqlMhs)->fetchAll(PDO::FETCH_ASSOC);

/* ============================================================
   3. STAFF (ROLE UTAMA)
============================================================ */
$sqlStaff = "
    SELECT
        u.user_id,
        u.username AS nama,
        'Staff' AS jabatan,
        NULL AS kategori,
        NULL AS status_mhs,
        'staff' AS tipe,
        r.role_name
    FROM users u
    LEFT JOIN roles r
      ON r.role_id = (SELECT role_id FROM user_roles WHERE user_id = u.user_id LIMIT 1)
    WHERE r.role_name IN ('ketua lab','admin','operator')
";
$staff = $conn->query($sqlStaff)->fetchAll(PDO::FETCH_ASSOC);

/* ============================================================
   4. ROLE PRIORITY
============================================================ */
function rolePriority($role) {
    return match(strtolower($role)) {
        'ketua lab' => 1,
        'admin'     => 2,
        'operator'  => 3,
        'dosen'     => 4,
        'mahasiswa' => 5,
        default     => 6
    };
}

/* ============================================================
   🔥 FIX DOBEL + ROLE UTAMA MENANG (MINIMAL)
============================================================ */
$raw = array_merge($staff, $dosen, $mahasiswa);
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
?>

<style>
.anggota-filter-group{text-align:center;margin-bottom:1.5rem}
.anggota-filter-btn{background:none;border:none;padding:6px 14px;font-weight:600;color:#64748b;cursor:pointer}
.anggota-filter-btn.active{color:#facc15}
.team-member{background:#fff;border-radius:18px;padding:20px 16px 26px;box-shadow:0 8px 20px rgba(15,23,42,.06);text-align:center}
.member-img{width:100%;height:240px;overflow:hidden;border-radius:14px;background:#f1f5f9}
.member-img img{width:100%;height:100%;object-fit:cover}
.member-info h4{font-size:1.1rem;font-weight:700;margin-top:10px}
.member-info span{font-size:.85rem;color:#64748b;text-transform:uppercase}
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

$role = strtolower($row['role_name'] ?? '');
$kategori = strtolower($row['kategori'] ?? '');
$status = strtolower($row['status_mhs'] ?? '');

// FILTER BASED ON AKADEMIK, BUKAN ROLE
if ($row['tipe'] === 'dosen') $filter = 'dosen';
elseif ($row['tipe'] === 'mahasiswa' && ($kategori==='alumni'||$status==='lulus')) $filter='alumni';
elseif ($row['tipe'] === 'mahasiswa') $filter='mahasiswa';
else $filter='dosen'; // staff dosen tetap masuk dosen

$foto = "../../../public/uploads/profiles/" . ($row['user_id'] ?? 0) . ".jpg";
$default = "../../../public/assets/img/default-user.png";

$link = ($row['tipe']==='staff')
    ? "#"
    : "anggota_detail.php?tipe={$row['tipe']}&id={$row['id_anggota']}";
?>

<div class="col-lg-3 col-md-6 anggota-item" data-type="<?= $filter ?>">
<a href="<?= $link ?>" class="text-decoration-none text-dark">
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
