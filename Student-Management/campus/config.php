<?php
// Edit for your local MySQL (XAMPP default shown)
const DB_HOST = 'localhost';
const DB_NAME = 'campus';
const DB_USER = 'root';
const DB_PASS = '';

session_start();
try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Database connection failed. Check config.php and import schema.sql.');
}

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function require_login() {
    if (empty($_SESSION['user'])) { header('Location: login.php'); exit; }
}
function csrf() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function check_csrf() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(400); exit('Invalid request. Go back and try again.'); }
}
function flash($msg = null) {
    if ($msg !== null) { $_SESSION['flash'] = $msg; return; }
    $m = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $m;
}
