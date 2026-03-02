<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']             ?? '');
    $email   = trim($_POST['email']            ?? '');
    $pass    = $_POST['password']              ?? '';
    $confirm = $_POST['confirm_password']      ?? '';

    if (empty($name) || empty($email) || empty($pass)) {
        $error = "Semua field wajib diisi.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } elseif ($pass !== $confirm) {
        $error = "Password tidak cocok.";
    } elseif (strlen($pass) < 6) {
        $error = "Password minimal 6 karakter.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Email sudah terdaftar.";
        } else {
            $hashed = password_hash($pass, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed);
            if ($stmt->execute()) {
                $success = "Registrasi berhasil!";
            } else {
                $error = "Terjadi kesalahan. Coba lagi.";
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register — Personal CRM</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-sm" style="width:420px">
        <div class="card-body p-4">
            <h4 class="mb-4 text-center fw-bold">📇 Daftar Akun</h4>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($success) ?>
                    <a href="login.php" class="alert-link">Login sekarang →</a>
                </div>
            <?php endif; ?>

            <form method="POST" id="registerForm" novalidate>
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                    <div class="form-text">Minimal 6 karakter.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Daftar</button>
            </form>
            <p class="text-center mt-3 mb-0 text-muted">Sudah punya akun? <a href="login.php">Login</a></p>
        </div>
    </div>
</div>
<script src="../assets/js/validation.js"></script>
</body>
</html>
