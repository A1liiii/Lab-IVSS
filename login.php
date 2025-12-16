<?php
require_once __DIR__ . "/app/core/database.php";
require_once __DIR__ . "/app/core/auth.php";

session_start();
$conn = Database::connect();

// Jika sudah login & sudah pilih role → redirect
if (isset($_SESSION['user']) && isset($_SESSION['active_role'])) {
    redirectByRole($_SESSION['active_role']);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Ambil user by username
    $stmt = $conn->prepare("
        SELECT user_id, nip, nim, username, password
        FROM users
        WHERE username = ?
        LIMIT 1
    ");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $error = "Username atau password salah!";
    } else {

        $storedPass = $user['password'];

        // Deteksi tipe password (MD5 atau Bcrypt)
        $isMD5 = (strlen($storedPass) === 32 && ctype_xdigit($storedPass));

        // Check login
        if ($isMD5) {
            $loginSuccess = (md5($password) === $storedPass);
        } else {
            $loginSuccess = password_verify($password, $storedPass);
        }

        if ($loginSuccess) {

            // ---- AUTO MIGRATE PASSWORD KE BCRYPT ----
            if ($isMD5) {
                $newHash = password_hash($password, PASSWORD_BCRYPT);

                $update = $conn->prepare("UPDATE users SET password=? WHERE user_id=?");
                $update->execute([$newHash, $user['user_id']]);
            }

            // Ambil semua role user
            $stmt2 = $conn->prepare("
                SELECT r.role_name
                FROM user_roles ur
                JOIN roles r ON ur.role_id = r.role_id
                WHERE ur.user_id = ?
                ORDER BY r.role_name
            ");
            $stmt2->execute([$user['user_id']]);
            $roles = $stmt2->fetchAll(PDO::FETCH_COLUMN);

            // Simpan session user
            $_SESSION['user'] = [
                'user_id' => $user['user_id'],
                'username' => $user['username'],
                'nip' => $user['nip'],
                'nim' => $user['nim']
            ];

            $_SESSION['roles'] = $roles;

            // Kalau cuma punya 1 role → langsung redirect
            if (count($roles) === 1) {
                $_SESSION['active_role'] = $roles[0];
                redirectByRole($roles[0]);
            } else {
                // Kalau multi role → pilih role dulu
                header("Location: select_role.php");
            }

            exit;

        } else {
            $error = "Username atau password salah!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Sistem</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        body {
            height: 100vh;
            overflow: hidden;
        }

        .left-section {
            background: url('public/assets/img/login-bg.png') no-repeat center center/cover;
        }

        .right-section {
            background-color: #004aad;
        }

        /* Panel berada di tengah layar, bukan di dalam salah satu kolom */
        .login-wrapper {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 10;
        }

        .login-box {
            background: #ffde59;
            padding: 30px;
            border-radius: 15px;
            width: 100%;
            max-width: 350px;
            animation: fadeIn .6s ease-in-out;
        }
        

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(10px);}
            to {opacity: 1; transform: translateY(0);}
        }
    </style>
</head>

<body>

<div class="container-fluid h-100 position-relative">

    <!-- Floating Login Box in Center -->
    <div class="login-wrapper">
        <div class="login-box shadow-lg">
            <h3 class="text-center fw-bold mb-4">Login Sistem</h3>

            <?php if(isset($error)): ?>
                <div class="alert alert-danger text-center"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" name="password" id="passwordInput" class="form-control" required>
                        <span class="input-group-text" id="togglePassword" style="cursor:pointer;">
                            <i class="bi bi-eye-slash-fill"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-dark w-100">Login</button>
                <div class="text-center mt-3">
                    <small class="text-dark">
                        Belum menjadi anggota?
                        <a href="/lab-ivss/app/roles/public/home.php#contact" class="contact-link">
                            Hubungi kami
                        </a>
                    </small>
                </div>

            </form>
        </div>
    </div>

    <div class="row h-100">
        <div class="col-6 left-section d-none d-md-block"></div>
        <div class="col-6 right-section"></div>
    </div>
</div>

<script>
document.getElementById("togglePassword").addEventListener("click", function () {
    const passwordField = document.getElementById("passwordInput");
    const icon = this.querySelector("i");

    if (passwordField.type === "password") {
        passwordField.type = "text";
        icon.classList.remove("bi-eye-slash-fill");
        icon.classList.add("bi-eye-fill");
    } else {
        passwordField.type = "password";
        icon.classList.remove("bi-eye-fill");
        icon.classList.add("bi-eye-slash-fill");
    }
});
</script>

</body>
</html>
