<?php
$active = "approvals";
$title = "Approval Requests";

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

// fetch registration list for ka_lab
$stmt = $conn->query("
    SELECT reg_id, nama, nim, email, status, alasan, tanggal_daftar
    FROM registrations
    ORDER BY 
        CASE 
            WHEN status='pending' THEN 1
            ELSE 2
        END,
        tanggal_daftar ASC
");

$regs = $stmt->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>

<h2 class="fw-bold mb-4 text-primary">
    <i class="bi bi-ui-checks"></i> Approval Requests
</h2>

<div class="card shadow-sm p-4 border-0">

<table class="table table-bordered align-middle">
    <thead class="table-light">
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>NIM</th>
            <th>Status</th>
            <th>Alasan</th>
            <th>Waktu Daftar</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>

        <?php if(empty($regs)): ?>
            <tr><td colspan="7" class="text-center text-muted">Belum ada permohonan.</td></tr>
        <?php endif; ?>

        <?php foreach($regs as $i => $r): ?>
        <tr>
            <td><?= $i+1 ?></td>
            <td><?= htmlspecialchars($r['nama']) ?></td>
            <td><?= htmlspecialchars($r['nim']) ?></td>
            
            <td>
                <?php if($r['status']=="pending"): ?>
                    <span class="badge bg-warning">Menunggu Persetujuan</span>
                <?php elseif($r['status']=="approved"): ?>
                    <span class="badge bg-success">Approved</span>
                <?php else: ?>
                    <span class="badge bg-danger">Rejected</span>
                <?php endif; ?>
            </td>

            <td><?= htmlspecialchars($r['alasan']) ?></td>

            <td><?= date("d M Y", strtotime($r['tanggal_daftar'])) ?></td>

            <td>
                <a href="approval_detail.php?id=<?= $r['reg_id'] ?>"
                   class="btn btn-sm btn-outline-primary">
                    Detail
                </a>

                <?php if($r['status']=="pending"): ?>
                    <a href="approve_action.php?id=<?= $r['reg_id'] ?>&action=approve"
                       class="btn btn-sm btn-success">
                       <i class="bi bi-check2"></i> Approve
                    </a>

                    <a href="approve_action.php?id=<?= $r['reg_id'] ?>&action=reject"
                       class="btn btn-sm btn-danger">
                       <i class="bi bi-x"></i> Reject
                    </a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>

    </tbody>
</table>

</div>

<style>
.table td { vertical-align: middle; }
</style>

<?php
$content = ob_get_clean();
include "_layout.php";
?>
