<?php
require_once '../middleware/auth.php';
require_once '../config/database.php';

$user_id = $_SESSION['user_id'];
$keyword = trim($_GET['q']    ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));
$limit   = 10;
$offset  = ($page - 1) * $limit;
$search  = "%$keyword%";

$count_stmt = $conn->prepare(
    "SELECT COUNT(*) FROM contacts
     WHERE user_id = ? AND (nama LIKE ? OR kategori LIKE ? OR email LIKE ?)"
);
$count_stmt->bind_param("isss", $user_id, $search, $search, $search);
$count_stmt->execute();
$count_stmt->bind_result($total_data);
$count_stmt->fetch();
$count_stmt->close();

$total_page = ceil($total_data / $limit);

$stmt = $conn->prepare(
    "SELECT * FROM contacts
     WHERE user_id = ? AND (nama LIKE ? OR kategori LIKE ? OR email LIKE ?)
     ORDER BY created_at DESC LIMIT ? OFFSET ?"
);
$stmt->bind_param("isssii", $user_id, $search, $search, $search, $limit, $offset);
$stmt->execute();
$contacts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$flash = '';
if (isset($_GET['success']))  $flash = '<div class="alert alert-success">✅ Kontak berhasil ditambahkan.</div>';
if (isset($_GET['updated']))  $flash = '<div class="alert alert-info">✏️ Kontak berhasil diupdate.</div>';
if (isset($_GET['deleted']))  $flash = '<div class="alert alert-warning">🗑️ Kontak berhasil dihapus.</div>';

$kategori_badge = [
    'Client'   => 'primary',
    'Prospect' => 'warning text-dark',
    'Partner'  => 'success',
    'Vendor'   => 'secondary',
    'Lainnya'  => 'light text-dark border',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard — Personal CRM</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">📇 Personal CRM</a>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white small">👤 <?= htmlspecialchars($_SESSION['user_name']) ?></span>
            <a href="../auth/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <?= $flash ?>

    <div class="d-flex justify-content-between align-items-center mb-3 gap-2 flex-wrap">
        <form method="GET" class="d-flex gap-2 flex-grow-1">
            <input type="text" name="q" class="form-control"
                   placeholder="🔍 Cari nama, email, kategori..."
                   value="<?= htmlspecialchars($keyword) ?>">
            <button class="btn btn-outline-primary px-3">Cari</button>
            <?php if ($keyword): ?>
                <a href="index.php" class="btn btn-outline-secondary">Reset</a>
            <?php endif; ?>
        </form>
        <a href="create.php" class="btn btn-success fw-semibold">+ Tambah</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Nama</th>
                        <th>No HP</th>
                        <th>Email</th>
                        <th>Kategori</th>
                        <th style="width:130px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($contacts)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <?= $keyword
                                ? 'Tidak ada hasil untuk "' . htmlspecialchars($keyword) . '".'
                                : 'Belum ada kontak. <a href="create.php">Tambah sekarang →</a>' ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($contacts as $i => $c): ?>
                    <tr>
                        <td class="text-muted"><?= $offset + $i + 1 ?></td>
                        <td class="fw-semibold"><?= htmlspecialchars($c['nama']) ?></td>
                        <td><?= htmlspecialchars($c['no_hp']) ?></td>
                        <td class="text-muted"><?= htmlspecialchars($c['email']) ?></td>
                        <td>
                            <span class="badge bg-<?= $kategori_badge[$c['kategori']] ?? 'secondary' ?>">
                                <?= htmlspecialchars($c['kategori']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="edit.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-danger"
                               onclick="return confirm('Hapus kontak ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($total_page > 1): ?>
    <nav class="mt-3">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page-1 ?>&q=<?= urlencode($keyword) ?>">‹ Prev</a>
                </li>
            <?php endif; ?>
            <?php for ($p = max(1, $page-2); $p <= min($total_page, $page+2); $p++): ?>
                <li class="page-item <?= $p === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $p ?>&q=<?= urlencode($keyword) ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>
            <?php if ($page < $total_page): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page+1 ?>&q=<?= urlencode($keyword) ?>">Next ›</a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
    <?php endif; ?>

    <p class="text-muted small">
        Menampilkan <?= count($contacts) ?> dari <?= $total_data ?> kontak
        <?= $keyword ? '— pencarian "' . htmlspecialchars($keyword) . '"' : '' ?>
    </p>
</div>
</body>
</html>
