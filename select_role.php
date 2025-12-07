<?php
session_start();
require_once __DIR__ . "/app/core/auth.php";

if (!isset($_SESSION['user']) || !isset($_SESSION['roles'])) {
    header("Location: login.php");
    exit;
}

// POST handler
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected = $_POST['role'] ?? '';
    if (in_array($selected, $_SESSION['roles'])) {
        $_SESSION['active_role'] = $selected;
        redirectByRole($selected);
        exit;
    } else {
        $error = "Role tidak valid.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pilih Role</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --blue: #004aad;
            --yellow: #ffde59;
        }

        body {
            background: linear-gradient(135deg, var(--blue), var(--yellow));
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }

        .role-card {
            background: #ffffffcc;
            backdrop-filter: blur(8px);
            border-radius: 18px;
            padding: 35px 40px;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 12px 32px rgba(0,0,0,0.15);
            animation: fadeUp .6s ease-out;
        }

        .role-card h2 {
            font-weight: 700;
            color: var(--blue);
        }

        .role-btn {
            background: var(--blue);
            border: none;
            width: 100%;
            padding: 14px;
            font-size: 16px;
            color: #fff;
            border-radius: 12px;
            font-weight: 600;
            transition: .2s;
        }

        .role-btn:hover {
            background: #003b86;
            transform: scale(1.02);
        }

        .logout-link {
            color: var(--blue);
            font-weight: 500;
        }

        .logout-link:hover {
            text-decoration: underline;
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(25px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>

<body>

    <div class="role-card shadow-lg">
        <h2 class="mb-2">
            <i class="bi bi-people-fill"></i>
            Halo, <?= htmlentities($_SESSION['user']['username'] ?? 'User') ?>
        </h2>

        <p class="text-secondary">Pilih mode akses yang ingin Anda gunakan:</p>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger py-2">
                <?= htmlentities($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="mt-3">
            <?php foreach ($_SESSION['roles'] as $r): ?>
                <button 
                    type="submit" 
                    name="role" 
                    value="<?= htmlentities($r) ?>" 
                    class="role-btn mb-3 d-flex align-items-center justify-content-center gap-2"
                >
                    <i class="bi bi-person-badge"></i>
                    Masuk sebagai <?= ucfirst(htmlentities($r)) ?>
                </button>
            <?php endforeach; ?>
        </form>

        <div class="text-center mt-3">
            <a href="logout.php" class="logout-link">
                <i class="bi bi-box-arrow-right"></i> Bukan Anda? Logout
            </a>
        </div>
    </div>

</body>
</html>
