<?php
session_start();

// Kalau user sudah login, arahkan ke dashboard sesuai role
if (isset($_SESSION['users'])) {
    header("Location: index.php?page=admin-dashboard");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login | LAB IVSS</title>
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
</head>

<body class="bg-light d-flex justify-content-center align-items-center" style="height: 100vh;">

    <div class="card shadow-lg p-4" style="width: 380px;">
        <h3 class="text-center mb-4">Login Sistem</h3>

        <!-- Menampilkan error bila ada -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <form action="/lab-ivss/index.php?page=login-action" method="POST">

            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>

            <button class="btn btn-primary w-100 mt-2" type="submit">
                Login
            </button>
        </form>
    </div>

</body>

</html>