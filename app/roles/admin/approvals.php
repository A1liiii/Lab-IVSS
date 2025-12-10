<?php
$active = "approvals";
$title  = "Approval Management";

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

// ==============================
// 1) STATISTIK STATUS REGISTRASI
// ==============================
$sqlStat = "
    SELECT 
        SUM(CASE WHEN status = 'pending'  THEN 1 ELSE 0 END) AS pending,
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected
    FROM registrations
";
$statRow = $conn->query($sqlStat)->fetch(PDO::FETCH_ASSOC);

$statPending  = (int)($statRow['pending']  ?? 0);
$statApproved = (int)($statRow['approved'] ?? 0);
$statRejected = (int)($statRow['rejected'] ?? 0);

// ==============================
// 2) DATA REGISTRATIONS DETAIL
// ==============================
$stmt = $conn->query("
    SELECT 
        reg_id, 
        nama, 
        nim, 
        email, 
        status, 
        account_created, 
        tanggal_daftar
    FROM registrations
    ORDER BY 
        CASE 
            WHEN status = 'pending' THEN 1
            WHEN status = 'approved' THEN 2
            ELSE 3
        END,
        tanggal_daftar ASC
");
$regs = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<h2 class="fw-bold mb-4 text-primary">
    <i class="bi bi-person-check"></i> Registrasi Pengguna
</h2>

<!-- STAT CARDS -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3 shadow-sm border-0 stat-card bg-warning-subtle text-warning d-flex justify-content-between align-items-center">
            <span>Pending</span>
            <strong class="fs-5"><?= $statPending ?></strong>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 shadow-sm border-0 stat-card bg-success-subtle text-success d-flex justify-content-between align-items-center">
            <span>Approved</span>
            <strong class="fs-5"><?= $statApproved ?></strong>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 shadow-sm border-0 stat-card bg-danger-subtle text-danger d-flex justify-content-between align-items-center">
            <span>Rejected</span>
            <strong class="fs-5"><?= $statRejected ?></strong>
        </div>
    </div>
</div>

<div class="card shadow-sm p-4 border-0">

    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th style="width: 50px;">No</th>
                <th>Nama</th>
                <th>NIM</th>
                <th>Status</th>
                <th>Akun</th>
                <th style="width: 220px;">Aksi</th>
            </tr>
        </thead>
        <tbody>

            <?php if(empty($regs)): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        Belum ada data.
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach($regs as $i => $r): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><?= htmlspecialchars($r['nama']) ?></td>
                <td><?= htmlspecialchars($r['nim']) ?></td>
                
                <td>
                    <?php if($r['status'] === "pending"): ?>
                        <span class="badge bg-warning text-dark">Pending Ketua Lab</span>
                    <?php elseif($r['status'] === "approved"): ?>
                        <span class="badge bg-success">Approved</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Rejected</span>
                    <?php endif; ?>
                </td>

                <td>
                    <?php if($r['status'] === "approved" && !$r['account_created']): ?>
                        <span class="badge bg-secondary">Akun Belum Dibuat</span>
                    <?php elseif(!empty($r['account_created'])): ?>
                        <span class="badge bg-success">Akun Sudah Dibuat</span>
                    <?php else: ?>
                        <span class="badge bg-light text-muted">-</span>
                    <?php endif; ?>
                </td>

                <td>
                    <a href="approval_detail.php?id=<?= $r['reg_id'] ?>" 
                       class="btn btn-sm btn-outline-primary">
                        Detail
                    </a>

                    <?php if($r['status'] === "approved" && !$r['account_created']): ?>
                        <a href="generate_user.php?reg_id=<?= $r['reg_id'] ?>"
                           class="btn btn-sm btn-success">
                            Buat Akun Login
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>

        </tbody>
    </table>

</div>

<style>
.stat-card {
    border-radius: 12px;
}
.table td {
    vertical-align: middle;
}
</style>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
