<?php
session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("admin");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$error = "";
$allowedRoles = ['dosen', 'ketua_lab', 'operator'];
$as = $_GET['as'] ?? 'dosen';

if (!in_array($as, $allowedRoles, true)) {
    $as = 'dosen';
}
// Jika form submit
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nip = trim($_POST['nip']);
    $nama = trim($_POST['nama']);
    $as   = $_POST['as'] ?? 'dosen';
    if (!in_array($as, ['dosen','ketua_lab','operator'], true)) {
        $as = 'dosen';
    }

    if ($nip === "" || $nama === "") {
        $error = "NIP dan Nama wajib diisi.";
    } else {

        // cek apakah NIP sudah terdaftar
        $stmt = $conn->prepare("SELECT nip FROM dosen WHERE nip = ?");
        $stmt->execute([$nip]);

        if ($stmt->fetch()) {
            $error = "NIP sudah digunakan!";
        } else {
            try {
                // Insert minimal data dulu saja
                $stmt = $conn->prepare("
                    INSERT INTO dosen (nip, nama)
                    VALUES (?, ?)
                ");
                $stmt->execute([$nip, $nama]);

                // redirect ke step 2
                header("Location: add_dosen_step2.php?nip=" . urlencode($nip) . "&as=" . urlencode($as));
                exit;

            } catch (Exception $e) {
                $error = "Gagal menyimpan data: " . $e->getMessage();
            }
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Dosen</title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

<div class="container mt-5" style="max-width: 500px;">

    <div class="card p-4 shadow-sm">
        <h5 class="mb-3">Step 1 — Input Data Dosen</h5>
        <small class="text-muted">Masukkan NIP dan Nama dosen sebelum membuat akun login.</small>

        <?php if($error): ?>
            <div class="alert alert-danger mt-3"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="mt-3">

            <input type="hidden" name="as" value="<?= htmlspecialchars($as, ENT_QUOTES) ?>">

            <div class="mb-3">
                <label class="form-label">NIP *</label>
                <input type="text" name="nip" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Nama Lengkap *</label>
                <input type="text" name="nama" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Lanjut Buat Akun Login →
            </button>
        </form>

    </div>

</div>

</body>
</html>
