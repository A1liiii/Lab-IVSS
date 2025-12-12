<?php
$title  = "Anggota | IVSS";
$active = "team";

ob_start();

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

/*
   RULE TUAN:
   - id_anggota = nip / nim (bukan user_id)
   - staff TIDAK pakai nip/nim
   - foto mengikuti user_management: uploads/profiles/{user_id}.jpg
   - role priority: ketua lab → admin → operator → dosen → mahasiswa
*/

/* ============================================================
   1. AMBIL DOSEN + ROLE + USER_ID
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
   2. AMBIL MAHASISWA + ROLE
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
   3. AMBIL STAFF (ADMIN / OPERATOR / KETUA LAB)
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
    WHERE r.role_name IN ('ketua lab', 'admin', 'operator')
";

$staff = $conn->query($sqlStaff)->fetchAll(PDO::FETCH_ASSOC);

/* ============================================================
   4. GABUNGKAN SEMUA ANGGOTA
============================================================ */
$anggota = array_merge($staff, $dosen, $mahasiswa);

/* ============================================================
   5. ROLE PRIORITY SORTING
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

usort($anggota, function($a, $b) {
    return rolePriority($a['role_name']) <=> rolePriority($b['role_name']);
});
?>

<!-- ============================================================== -->
<!-- STYLE -->
<!-- ============================================================== -->
<style>
.anggota-filter-group{text-align:center;margin-bottom:1.5rem}
.anggota-filter-btn{background:none;border:none;padding:6px 14px;margin:0 4px;font-weight:600;font-size:1rem;color:#64748b;cursor:pointer;position:relative}
.anggota-filter-btn.active{color:#facc15}
.anggota-filter-btn.active::after{width:60%}
.anggota-filter-btn::after{content:"";position:absolute;left:50%;bottom:-4px;width:0;height:2px;background:#facc15;border-radius:999px;transform:translateX(-50%);transition:width .2s ease}

.team-member{background:#fff;border-radius:18px;padding:20px 16px 26px;box-shadow:0 8px 20px rgba(15,23,42,.06);transition:.25s;text-align:center}
.team-member:hover{transform:translateY(-8px);box-shadow:0 16px 35px rgba(15,23,42,.15)}
.team-member .member-img{width:100%;height:240px;overflow:hidden;border-radius:14px;background:#f1f5f9}
.team-member .member-img img{width:100%;height:100%;object-fit:cover}
.team-member::before{content:"";width:70%;height:1.5px;background:#e2e8f0;margin:12px auto 18px;display:block;border-radius:2px}
.member-info h4{font-size:1.15rem;font-weight:700;color:#0f172a;margin-bottom:6px}
.member-info span{font-size:.85rem;font-weight:500;color:#64748b;text-transform:uppercase}
</style>

<!-- ============================================================== -->
<!-- TEAM SECTION -->
<!-- ============================================================== -->
<section id="team" class="team section">

  <div class="container section-title">
    <h2>Team</h2>
    <p>Anggota Lab</p>
  </div>

  <div class="container">

    <!-- FILTER -->
    <div class="anggota-filter-group">
        <button class="anggota-filter-btn active" data-filter="all">All</button>
        <button class="anggota-filter-btn" data-filter="dosen">Dosen</button>
        <button class="anggota-filter-btn" data-filter="mahasiswa">Mahasiswa</button>
        <button class="anggota-filter-btn" data-filter="alumni">Alumni</button>
    </div>

    <div class="row gy-4">

    <?php foreach ($anggota as $row): ?>

        <?php
        $role = strtolower($row['role_name'] ?? '');
        $kategori = strtolower($row['kategori'] ?? '');
        $status = strtolower($row['status_mhs'] ?? '');

        // FILTER TYPE
        if (in_array($role, ['ketua lab','admin','operator'])) {
            $filter = 'staff';
        } elseif ($role === 'dosen') {
            $filter = 'dosen';
        } elseif ($role === 'mahasiswa' && ($kategori === 'alumni' || $status === 'lulus')) {
            $filter = 'alumni';
        } else {
            $filter = 'mahasiswa';
        }

        // FOTO — mengikuti USER MANAGEMENT
        $foto = "../../../public/uploads/profiles/" . ($row['user_id'] ?? "0") . ".jpg";
        $defaultFoto = "../../../public/assets/img/default-user.png";

        // LINK DETAIL
        $link = ($row['tipe'] === 'staff')
            ? "#"
            : "anggota_detail.php?tipe={$row['tipe']}&id={$row['id_anggota']}";
        ?>

        <div class="col-lg-3 col-md-6 anggota-item" data-type="<?= $filter ?>">

            <a href="<?= $link ?>" class="text-decoration-none text-dark">

                <div class="team-member">

                <div class="member-img">
                    <img src="<?= $foto ?>" onerror="this.src='<?= $defaultFoto ?>';">
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
// FILTER SCRIPT
document.addEventListener("DOMContentLoaded", () => {
    const btns = document.querySelectorAll(".anggota-filter-btn");
    const items = document.querySelectorAll(".anggota-item");

    btns.forEach(btn => {
        btn.addEventListener("click", () => {
            btns.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");

            const filter = btn.dataset.filter;

            items.forEach(item => {
                item.classList.toggle("d-none", !(filter === "all" || item.dataset.type === filter));
            });
        });
    });
});
</script>

<?php
$content = ob_get_clean();
include __DIR__ . "/_layout.php";
?>
