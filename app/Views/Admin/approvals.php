<?php
// Views/Admin/approvals.php
// Menampilkan daftar pending + status approve
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar Pendaftar</title>
<style>
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    background-color: #f9f9f9;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
}

h2 {
    white-space: nowrap;
    text-align: center;
    margin: 0 0 10px 0;
    color: #333;
}

.table-container {
    overflow-x: auto;
    background-color: white;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
}

th, td {
    padding: 8px;
    border: 1px solid #ddd;
}

thead tr {
    background-color: #f2f2f2;
}

tbody tr:nth-child(even) {
    background-color: #fafafa;
}

a.approve-btn {
    padding: 4px 8px;
    background-color: #4CAF50;
    color: white;
    text-decoration: none;
    border-radius: 4px;
}

a.approve-btn:hover {
    background-color: #45a049;
}

span.approved-label {
    padding: 4px 8px;
    background-color: #ccc;
    color: #333;
    border-radius: 4px;
}

p.no-data {
    text-align: center;
    margin-top: 20px;
    color: #555;
}
</style>
</head>
<body>

<div class="container">
<h2>Daftar Pendaftar</h2>

<?php if (!empty($pendaftar)): ?>
<div class="table-container">
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>NIM</th> 
            <th>Email</th>
            <th>Prodi</th>
            <th>Angkatan</th>
            <th>Alasan</th>
            <th>Tanggal Daftar</th>
            <th>Approved By</th>
            <th>Approved At</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no=1; foreach($pendaftar as $p): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= htmlspecialchars($p['nama']) ?></td>
            <td><?= htmlspecialchars($p['nim']) ?></td>
            <td><?= htmlspecialchars($p['email']) ?></td>
            <td><?= htmlspecialchars($p['prodi']) ?></td>
            <td><?= htmlspecialchars($p['angkatan']) ?></td>
            <td><?= htmlspecialchars($p['alasan']) ?></td>
            <td><?= date('d-m-Y', strtotime($p['tanggal_daftar'])) ?></td>
            <td><?= htmlspecialchars($p['approved_by_username'] ?? '-') ?></td>
            <td><?= htmlspecialchars(isset($p['approved_at']) ? date('d-m-Y H:i', strtotime($p['approved_at'])) : '-') ?></td>
            <td>
                <?php if ($p['status'] === 'pending'): ?>
                    <a href="index.php?page=admin-approvals-approve&id=<?= $p['reg_id'] ?>" 
                       onclick="return confirm('Yakin ingin approve pendaftar ini?')" 
                       class="approve-btn">Approve</a>
                <?php else: ?>
                    <span class="approved-label">Approved</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php else: ?>
<p class="no-data">Tidak ada pendaftar pending.</p>
<?php endif; ?>
</div>

</body>
</html>
