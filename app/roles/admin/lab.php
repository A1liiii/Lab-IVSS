<?php
// lab.php (single file view + edit, enterprise+ AI-OWL header)
// Pastikan Database::connect() mengembalikan instance PDO
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../core/auth.php";
requireRole("admin");

$title = "Informasi Laboratorium";
$active = "lab";

require_once __DIR__ . "/../../core/database.php";
$db = Database::connect();

// CSRF token helper
if (!isset($_SESSION['csrf_lab_token'])) {
    $_SESSION['csrf_lab_token'] = bin2hex(random_bytes(24));
}
$csrf_token = $_SESSION['csrf_lab_token'];

// Handle POST update
$alert = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_lab') {
    // CSRF check
    if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf_lab_token'], $_POST['csrf'])) {
        $alert = ['type' => 'danger', 'msg' => 'Token invalid. Coba muat ulang halaman.'];
    } else {
        // Collect & sanitize input (trim)
        $fields = [
            'nama','deskripsi','visi','misi','motto','alamat','email','no_telp','youtube','instagram','tiktok'
        ];
        $data = [];
        foreach ($fields as $f) {
            $data[$f] = isset($_POST[$f]) ? trim($_POST[$f]) : null;
        }

        // minimal validation
        if ($data['nama'] === '') {
            $alert = ['type' => 'warning', 'msg' => 'Nama lab tidak boleh kosong.'];
        } elseif ($data['email'] && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $alert = ['type' => 'warning', 'msg' => 'Format email tidak valid.'];
        } else {
            try {
                // Check if row exists
                $stmt = $db->query("SELECT COUNT(*) as c FROM lab_info");
                $exists = (int)$stmt->fetch(PDO::FETCH_ASSOC)['c'] > 0;

                if ($exists) {
                    // PostgreSQL: no LIMIT on UPDATE. Use WHERE if you have pk.
                    $sql = "UPDATE lab_info SET 
                        nama = :nama,
                        deskripsi = :deskripsi,
                        visi = :visi,
                        misi = :misi,
                        motto = :motto,
                        alamat = :alamat,
                        email = :email,
                        no_telp = :no_telp,
                        youtube = :youtube,
                        instagram = :instagram,
                        tiktok = :tiktok";
                    $prep = $db->prepare($sql);
                    $prep->execute([
                        ':nama'=>$data['nama'],
                        ':deskripsi'=>$data['deskripsi'],
                        ':visi'=>$data['visi'],
                        ':misi'=>$data['misi'],
                        ':motto'=>$data['motto'],
                        ':alamat'=>$data['alamat'],
                        ':email'=>$data['email'],
                        ':no_telp'=>($data['no_telp'] === '' ? null : $data['no_telp']),
                        ':youtube'=>$data['youtube'],
                        ':instagram'=>$data['instagram'],
                        ':tiktok'=>$data['tiktok'],
                    ]);

                    try {
                        $log = $db->prepare("
                            INSERT INTO public.log_activity (user_id, aksi, deskripsi, waktu)
                            VALUES (?, ?, ?, NOW())
                        ");
                        $log->execute([
                            $_SESSION['user']['user_id'],
                            'update',
                            'Memperbarui informasi laboratorium'
                        ]);
                    } catch (PDOException $e) {}
                } else {
                    $sql = "INSERT INTO lab_info (nama,deskripsi,visi,misi,motto,alamat,email,no_telp,youtube,instagram,tiktok)
                            VALUES (:nama,:deskripsi,:visi,:misi,:motto,:alamat,:email,:no_telp,:youtube,:instagram,:tiktok)";
                    $prep = $db->prepare($sql);
                    $prep->execute([
                        ':nama'=>$data['nama'],
                        ':deskripsi'=>$data['deskripsi'],
                        ':visi'=>$data['visi'],
                        ':misi'=>$data['misi'],
                        ':motto'=>$data['motto'],
                        ':alamat'=>$data['alamat'],
                        ':email'=>$data['email'],
                        ':no_telp'=>$data['no_telp'] === '' ? null : $data['no_telp'],
                        ':youtube'=>$data['youtube'],
                        ':instagram'=>$data['instagram'],
                        ':tiktok'=>$data['tiktok'],
                    ]);
                }

                // regenerate token to prevent replay
                $_SESSION['csrf_lab_token'] = bin2hex(random_bytes(24));
                // redirect to avoid resubmission
                header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . "?updated=1");
                exit;
            } catch (Exception $e) {
                $alert = ['type' => 'danger', 'msg' => 'Gagal menyimpan data: ' . $e->getMessage()];
            }
        }
    }
}

// Fetch lab data (single row)
try {
    $stmt = $db->query("SELECT * FROM lab_info LIMIT 1");
    $lab = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $lab = false;
    $alert = ['type'=>'danger','msg'=>'Gagal mengambil data: '.$e->getMessage()];
}

// Show success message if redirected after update
if (isset($_GET['updated']) && $_GET['updated'] == '1' && !$alert) {
    $alert = ['type'=>'success','msg'=>'Informasi lab berhasil diperbarui.'];
}

// --- Render content (ke layout) ---
ob_start();
?>

<!-- Begin page content -->
<div class="container py-4">

    <!-- HERO SECTION - FUTURISTIC AI / OWL STYLE -->
    <div class="row g-3 align-items-center mb-4">
        <div class="col-12">
            <div class="p-4 rounded-4 shadow-lg hero-box"
                 style="
                    background: linear-gradient(125deg, #002b8a 0%, #005CFF 35%, #6246EA 70%, #8B5CF6 100%);
                    color: #fff;
                    border-radius: 18px;
                    position: relative;
                    overflow: hidden;
                 ">

                <!-- NEURAL CIRCUIT OVERLAY (optional file "circuit_pattern.png") -->
                <div style="
                    position:absolute;
                    inset:0;
                    background: radial-gradient(circle at 10% 10%, rgba(255,255,255,0.02), transparent 10%),
                                linear-gradient(180deg, rgba(255,255,255,0.02), transparent 50%);
                    pointer-events:none;
                    z-index:1;
                "></div>

                <div class="d-flex align-items-center justify-content-between position-relative" style="z-index:2;">
                    
                    <!-- LEFT (Logo + Title + Motto) -->
                    <div class="d-flex flex-column">
                        
                        <div class="d-flex align-items-center gap-4 mb-2">
                            <img src="../../../public/assets/img/logo_ivss2.png"
                                 alt="Logo Lab"
                                 style="
                                    height:72px;
                                    width:auto;
                                    border-radius:12px;
                                    box-shadow:
                                        0 6px 18px rgba(0,120,255,0.45),
                                        0 0 28px rgba(120,60,255,0.25);
                                 ">

                            <div>
                                <h1 class="fw-bold m-0"
                                    style="
                                        font-size: 1.9rem;
                                        letter-spacing: 0.4px;
                                        text-shadow: 0 6px 18px rgba(10,10,30,0.4);
                                    ">
                                    <?= htmlspecialchars($lab['nama'] ?? 'Laboratorium') ?>
                                </h1>
                                
                                <p class="m-0 mt-1"
                                   style="
                                       font-size: 1rem;
                                       opacity: .95;
                                       font-weight: 500;
                                       color: rgba(255,255,255,0.95);
                                       text-shadow: 0 2px 10px rgba(0,0,0,0.18);
                                   ">
                                    <?= htmlspecialchars($lab['motto'] ?? 'Motto belum diset') ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    

                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <?php if ($alert): ?>
        <div class="alert alert-<?= htmlspecialchars($alert['type']) ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($alert['msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Left: Details card -->
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <h5 class="mb-3 fw-bold"><i class="bi bi-file-text"></i> Deskripsi & Visi Misi</h5>

                    <div class="mb-3">
                        <div class="text-muted small mb-1">Deskripsi</div>
                        <div class="p-3 rounded bg-light" style="min-height:80px;">
                            <?= nl2br(htmlspecialchars($lab['deskripsi'] ?? '-')) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small mb-1">Visi</div>
                            <div class="p-3 rounded bg-light" style="min-height:70px;">
                                <?= nl2br(htmlspecialchars($lab['visi'] ?? '-')) ?>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small mb-1">Misi</div>
                            <div class="p-3 rounded bg-light" style="min-height:70px;">
                                <?= nl2br(htmlspecialchars($lab['misi'] ?? '-')) ?>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="text-muted small mb-1">Motto</div>
                        <div class="fs-5 fst-italic text-primary"><?= htmlspecialchars($lab['motto'] ?? '-') ?></div>
                    </div>

                </div>
            </div>

            <!-- Address card -->
            <div class="card shadow border-0 mt-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-geo-alt"></i> Alamat</h6>
                    <div class="p-3 rounded bg-light">
                        <?= nl2br(htmlspecialchars($lab['alamat'] ?? '-')) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Quick info -->
        <div class="col-lg-4">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-info-circle"></i> Informasi Singkat</h6>

                    <dl class="row">
                        <dt class="col-5 text-muted">Nama</dt>
                        <dd class="col-7 fw-semibold"><?= htmlspecialchars($lab['nama'] ?? '-') ?></dd>

                        <dt class="col-5 text-muted">Email</dt>
                        <dd class="col-7"><?= htmlspecialchars($lab['email'] ?? '-') ?></dd>

                        <dt class="col-5 text-muted">Telp</dt>
                        <dd class="col-7"><?= htmlspecialchars($lab['no_telp'] ?? '-') ?></dd>

                        <dt class="col-5 text-muted">YouTube</dt>
                        <dd class="col-7 text-truncate" title="<?= htmlspecialchars($lab['youtube'] ?? '') ?>">
                            <?php if (!empty($lab['youtube'])): ?>
                                <a href="<?= htmlspecialchars($lab['youtube']) ?>" target="_blank"><?= htmlspecialchars($lab['youtube']) ?></a>
                            <?php else: ?> - <?php endif; ?>
                        </dd>

                        <dt class="col-5 text-muted">Instagram</dt>
                        <dd class="col-7">
                            <?php if (!empty($lab['instagram'])): ?>
                                <a href="<?= htmlspecialchars($lab['instagram']) ?>" target="_blank"><?= htmlspecialchars($lab['instagram']) ?></a>
                            <?php else: ?> - <?php endif; ?>
                        </dd>

                        <dt class="col-5 text-muted">TikTok</dt>
                        <dd class="col-7">
                            <?= !empty($lab['tiktok']) ? '<a href="'.htmlspecialchars($lab['tiktok']).'" target="_blank">Link</a>' : '-' ?>
                        </dd>
                    </dl>

                    <div class="d-grid mt-3">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEdit">
                            <i class="bi bi-pencil"></i> Edit Sekarang
                        </button>
                    </div>

                </div>
            </div>

            <!-- small notes card -->
            <div class="card shadow border-0 mt-4">
                <div class="card-body p-3">
                    <small class="text-muted">Tip: gunakan tombol <strong>Edit</strong> untuk mengubah informasi. Perubahan disimpan langsung ke database.</small>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End page content -->

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form method="post" novalidate>
                <input type="hidden" name="action" value="update_lab">
                <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf_token) ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalEditLabel"><i class="bi bi-pencil-square"></i> Edit Informasi Laboratorium</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Rows of inputs -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lab</label>
                            <input type="text" name="nama" class="form-control form-control-lg" value="<?= htmlspecialchars($lab['nama'] ?? '') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Motto</label>
                            <input type="text" name="motto" class="form-control" value="<?= htmlspecialchars($lab['motto'] ?? '') ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" rows="4" class="form-control"><?= htmlspecialchars($lab['deskripsi'] ?? '') ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Visi</label>
                            <textarea name="visi" rows="3" class="form-control"><?= htmlspecialchars($lab['visi'] ?? '') ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Misi</label>
                            <textarea name="misi" rows="3" class="form-control"><?= htmlspecialchars($lab['misi'] ?? '') ?></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" rows="2" class="form-control"><?= htmlspecialchars($lab['alamat'] ?? '') ?></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($lab['email'] ?? '') ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="no_telp" class="form-control" value="<?= htmlspecialchars($lab['no_telp'] ?? '') ?>">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">YouTube (URL)</label>
                            <input type="url" name="youtube" class="form-control" value="<?= htmlspecialchars($lab['youtube'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Instagram (URL)</label>
                            <input type="url" name="instagram" class="form-control" value="<?= htmlspecialchars($lab['instagram'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">TikTok (URL)</label>
                            <input type="url" name="tiktok" class="form-control" value="<?= htmlspecialchars($lab['tiktok'] ?? '') ?>">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// --- require layout (sesuaikan jika nama layoutmu _layout.php atau layout.php) ---
require_once __DIR__ . "/_layout.php";
?>

<!-- Extra styles (ditambahkan ke halaman via inline agar file tetap single) -->
<style>
/* Enterprise+ polish & FUTURISTIC OWL THEME */
body { background: linear-gradient(180deg, #f4f7ff 0%, #ffffff 40%); }
.card { border-radius: 14px; }
.modal-content { border-radius: 12px; box-shadow: 0 18px 60px rgba(16,24,40,0.12); }

/* HERO polish */
.hero-box { transition: 0.28s ease; }
.hero-box:hover { transform: translateY(-4px); box-shadow: 0 18px 50px rgba(0,0,0,0.15), 0 0 28px rgba(80,60,255,0.08); }
.hero-box img { transition: 0.22s ease; }
.hero-box img:hover { transform: scale(1.03); }

/* subtle scrollbar for content areas */
main::-webkit-scrollbar { height: 8px; width: 8px; }
main::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.12); border-radius: 10px; }

/* FIX: Modal scroll tidak berfungsi karena body overflow hidden */
.modal-dialog-scrollable .modal-body {
    overflow-y: auto !important;
    max-height: calc(100vh - 220px);
}
.modal {
    overflow-y: auto !important;
}
body.modal-open main {
    overflow: hidden !important;
}

/* responsive improvements */
@media (max-width: 768px) {
    .hero-box { padding: 1rem; }
    .hero-box img { height:56px; }
    h1 { font-size:1.2rem !important; }
}
</style>

<!-- Optional: small JS helpers -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Auto-open modal if validation warning present (server returned $alert and page not redirected)
    <?php if ($alert && ($alert['type'] === 'warning' || $alert['type'] === 'danger')): ?>
    var modal = new bootstrap.Modal(document.getElementById('modalEdit'));
    modal.show();
    <?php endif; ?>

    // Prevent accidental form submit spamming: disable submit while submitting
    var form = document.querySelector('#modalEdit form');
    if(form){
        form.addEventListener('submit', function(){
            var btn = form.querySelector('button[type="submit"]');
            if(btn){
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';
            }
        });
    }
});
</script>
