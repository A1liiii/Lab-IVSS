<?php
session_start();
require_once __DIR__ . "/../../core/auth.php";
requireRole("admin");

require_once __DIR__ . "/../../core/database.php";
$conn = Database::connect();

$error = "";

// ambil nip dari url
if (!isset($_GET['nip'])) {
    die("Akses tidak valid!");
}

$nip = $_GET['nip'];
$as = $_GET['as'] ?? 'dosen';

// ambil data dosen berdasarkan nip
$stmt = $conn->prepare("SELECT * FROM dosen WHERE nip = ?");
$stmt->execute([$nip]);
$dosen = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dosen) {
    die("Data dosen tidak ditemukan!");
}

// handle submit form
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === "" || $password === "") {
        $error = "Username dan password wajib diisi.";
    } else {

        // cek username terpakai?
        $check = $conn->prepare("SELECT username FROM users WHERE username = ?");
        $check->execute([$username]);

        if ($check->fetch()) {
            $error = "Username sudah digunakan!";
        } else {

            $hash = password_hash($password, PASSWORD_BCRYPT);

            try {
                $conn->beginTransaction();

                // generate user_id baru
                $userId = $conn->query("
                    SELECT COALESCE(MAX(user_id),0)+1 FROM users
                ")->fetchColumn();

                // insert user
                $stmt = $conn->prepare("
                    INSERT INTO users (user_id, nip, username, password)
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$userId, $nip, $username, $hash]);

                // update dosen.user_id
                $updateDosen = $conn->prepare("
                    UPDATE dosen SET user_id = ? WHERE nip = ?
                ");
                $updateDosen->execute([$userId, $nip]);

                // ===============================
                // ASSIGN ROLE (DINAMIS)
                // ===============================
                $rolesToAssign = ['dosen'];

                if ($as === 'ketua_lab') {
                    $rolesToAssign[] = 'ketua lab';
                }

                if ($as === 'operator') {
                    $rolesToAssign[] = 'operator';
                }

                $roleInsert = $conn->prepare("
                    INSERT INTO user_roles (user_id, role_id)
                    SELECT ?, role_id FROM roles WHERE role_name = ?
                ");

                foreach ($rolesToAssign as $roleName) {
                    $roleInsert->execute([$userId, $roleName]);
                }

                $conn->commit();

                header("Location: user.php");
                exit;

            } catch (Exception $e) {
                $conn->rollBack();
                $error = "Gagal menyimpan akun login: " . $e->getMessage();
            }

        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Buat Akun Login Dosen</title>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>

<body class="bg-light">

<div class="container mt-5" style="max-width: 520px;">

    <div class="card p-4 shadow-sm">

        <h5 class="mb-2">Step 2 — Buat Akun Login</h5>
        <small class="text-muted">Data dosen sudah disimpan, sekarang buat akses login.</small>

        <div class="alert alert-info mt-3">
            <b>NIP:</b> <?= htmlspecialchars($dosen['nip']) ?><br>
            <b>Nama:</b> <?= htmlspecialchars($dosen['nama']) ?>
        </div>

        <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" class="mt-3">

            <div class="mb-3">
                <label>Username *</label>
                <input type="text" name="username" class="form-control"
                       required placeholder="Masukkan username">
            </div>

            <div class="mb-3">
                <label>Password *</label>
                <input type="password" name="password" class="form-control"
                       required placeholder="Masukkan password">
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Buat Akun Login →
            </button>

        </form>

        <a href="user.php" class="btn btn-light mt-3 w-100">Kembali ke Manajemen User</a>

    </div>

</div>

</body>
</html>
