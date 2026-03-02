<?php
require_once '../middleware/auth.php';
require_once '../config/database.php';

$user_id = $_SESSION['user_id'];
$id      = (int)($_GET['id'] ?? 0);

// Hanya hapus jika kontak milik user ini
$stmt = $conn->prepare("DELETE FROM contacts WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$stmt->close();

header("Location: index.php?deleted=1");
exit;
