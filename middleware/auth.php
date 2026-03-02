<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    $depth = substr_count($_SERVER['PHP_SELF'], '/') - 2;
    $prefix = str_repeat('../', $depth);
    header("Location: " . $prefix . "auth/login.php");
    exit;
}
