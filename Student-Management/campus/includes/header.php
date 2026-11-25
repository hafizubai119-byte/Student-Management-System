<?php
require_once __DIR__ . '/../config.php';
require_login();
$page = basename($_SERVER['PHP_SELF']);
$nav = ['index.php' => 'Dashboard', 'students.php' => 'Students', 'courses.php' => 'Courses'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($title ?? 'Campus') ?> | Campus</title>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;800&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="shell">
  <aside class="sidebar" id="sidebar">
    <a class="brand" href="index.php">Campus</a>
    <nav>
      <?php foreach ($nav as $file => $label): ?>
        <a href="<?= $file ?>" class="<?= ($page === $file || ($file === 'students.php' && $page === 'student_form.php')) ? 'on' : '' ?>"><?= $label ?></a>
      <?php endforeach; ?>
    </nav>
    <a class="logout" href="logout.php">Log out (<?= e($_SESSION['user']) ?>)</a>
  </aside>
  <div class="content">
    <header class="topbar">
      <button class="menu-btn" id="menu-btn" aria-label="Toggle menu" aria-expanded="false">Menu</button>
      <h1><?= e($title ?? '') ?></h1>
    </header>
    <main>
    <?php if ($m = flash()): ?><p class="notice" role="status"><?= e($m) ?></p><?php endif; ?>
