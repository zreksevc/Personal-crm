<?php
require_once '../middleware/auth.php';
require_once '../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']     ?? '');
    $no_hp    = trim($_POST['no_hp']    ?? '');
    $email    = trim($_POST['email']    ?? '');
    $kategori = $_POST['kategori']      ?? 'Lainnya';
    $alamat   = trim($_POST['alamat']   ?? '');
    $user_id  = $_SESSION['user_id'];

    if (empty($nama)) {
        $error = "Nama wajib diisi.";
    } elseif (empty($no_hp)) {
        $error = "No HP wajib diisi.";
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } else {
        $stmt = $conn->prepare(
            "INSERT INTO contacts (user_id, nama, no_hp, email, kategori, alamat)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param("isssss", $user_id, $nama, $no_hp, $email, $kategori, $alamat);

        if ($stmt->execute()) {
            header("Location: index.php?success=1");
            exit;
        } else {
            $error = "Gagal menyimpan kontak. Silakan coba lagi.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kontak — Personal CRM</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">← Kembali ke Dashboard</a>
    </div>
</nav>

<div class="container">
    <div class="card shadow-sm mx-auto" style="max-width:560px">
        <div class="card-body p-4">
            <h5 class="mb-4 fw-bold">➕ Tambah Kontak Baru</h5>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" id="contactForm" novalidate>
                <div class="mb-3">
                    <label class="form-label">Nama <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control"
                           value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">No HP <span class="text-danger">*</span></label>
                    <input type="text" name="no_hp" class="form-control" placeholder="08xx-xxxx-xxxx"
                           value="<?= htmlspecialchars($_POST['no_hp'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select name="kategori" class="form-select">
                        <?php foreach (['Lainnya','Client','Prospect','Partner','Vendor'] as $opt): ?>
                            <option value="<?= $opt ?>" <?= (($_POST['kategori'] ?? '') === $opt) ? 'selected' : '' ?>>
                                <?= $opt ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($_POST['alamat'] ?? '') ?></textarea>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4">Simpan</button>
                    <a href="index.php" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="../assets/js/validation.js"></script>
</body>
</html>
