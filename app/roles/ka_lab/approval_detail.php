<?php
session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole(["admin","ketua lab"]); // dua role bisa akses

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: approvals.php");
    exit;
}

// ambil data registrasi
$stmt = $conn->prepare("
    SELECT r.*, 
           CASE WHEN status='pending' THEN 'Menunggu Persetujuan Ketua Lab'
                WHEN status='approved' THEN 'Sudah Disetujui'
                WHEN status='rejected' THEN 'Ditolak'
           END AS status_label,
           u.username AS approver_username,
           COALESCE(d.nama, u.username) AS approver_name
    FROM registrations r
    LEFT JOIN users u ON u.user_id = r.approved_by
    LEFT JOIN dosen d ON d.nip = u.nip
    WHERE reg_id = ?
");
$stmt->execute([$id]);
$reg = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reg) {
    die("Data tidak ditemukan.");
}
function formatTanggal($time) {
    if (!$time) return "-";

    $bulan = [
        1=>"Januari",2=>"Februari",3=>"Maret",4=>"April",5=>"Mei",6=>"Juni",
        7=>"Juli",8=>"Agustus",9=>"September",10=>"Oktober",11=>"November",12=>"Desember"
    ];

    $dt = new DateTime($time);

    $d = $dt->format("d");
    $m = (int)$dt->format("m");
    $y = $dt->format("Y");
    $h = $dt->format("H:i");

    return "$d {$bulan[$m]} $y $h";
}

ob_start();
?>

<h2 class="fw-bold mb-4 text-primary">
    <i class="bi bi-person-lines-fill"></i> Data Pendaftar
</h2>

<div class="card shadow-sm p-4 border-0">


    <table class="table table-bordered">
        <tr>
            <th width="30%">Nama</th>
            <td><?= htmlspecialchars($reg['nama']) ?></td>
        </tr>
        <tr>
            <th>NIM</th>
            <td><?= htmlspecialchars($reg['nim']) ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?= htmlspecialchars($reg['email']) ?></td>
        </tr>
        <tr>
            <th>Program Studi</th>
            <td><?= htmlspecialchars($reg['prodi']) ?></td>
        </tr>
        <tr>
            <th>Angkatan</th>
            <td><?= htmlspecialchars($reg['angkatan']) ?></td>
        </tr>
        <tr>
            <th>Alasan Mendaftar</th>
            <td><?= nl2br(htmlspecialchars($reg['alasan'])) ?></td>
        </tr>
        <tr>
            <th>Tanggal Daftar</th>
            <td><?= formatTanggal($reg['tanggal_daftar']) ?></td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
                <?php if ($reg['status']=="pending"): ?>
                    <span class="badge bg-warning">Menunggu Persetujuan Ketua Lab</span>
                <?php elseif($reg['status']=="approved"): ?>
                    <span class="badge bg-success">Disetujui</span>
                <?php else: ?>
                    <span class="badge bg-danger">Ditolak</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php if ($reg['status']=="approved"): ?>
        <tr>
            <th>Disetujui Oleh</th>
            <td><?= htmlspecialchars($reg['approver_name']) ?></td>        </tr>
        <tr>
            <th>Tanggal Approve</th>
            <td><?= formatTanggal($reg['tanggal_daftar']) ?></td>
        </tr>
        <?php endif; ?>
        <?php if ($reg['account_created']): ?>
        <tr>
            <th>Status Akun</th>
            <td><span class="badge bg-success">Akun Sudah Dibuat</span></td>
        </tr>
        <?php endif; ?>
    </table>

    <div class="d-flex justify-content-between mt-4">

        <a href="approvals.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>

        <div>
            <?php if ($reg['status']=="pending" && $_SESSION['user']['role']=="ketua lab"): ?>
                <a href="approve_action.php?id=<?= $reg['reg_id'] ?>" 
                   class="btn btn-success">
                    <i class="bi bi-check2-circle"></i> Setujui
                </a>
                <a href="reject_action.php?id=<?= $reg['reg_id'] ?>" 
                   class="btn btn-danger">
                    <i class="bi bi-x-circle"></i> Tolak
                </a>
            <?php endif; ?>
        </div>

    </div>

</div>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
