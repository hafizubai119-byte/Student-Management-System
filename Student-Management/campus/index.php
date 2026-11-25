<?php
$title = 'Dashboard';
require 'includes/header.php';
$total  = (int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$active = (int)$pdo->query("SELECT COUNT(*) FROM students WHERE status='active'")->fetchColumn();
$grad   = (int)$pdo->query("SELECT COUNT(*) FROM students WHERE status='graduated'")->fetchColumn();
$ncour  = (int)$pdo->query("SELECT COUNT(*) FROM courses")->fetchColumn();
$byCourse = $pdo->query("SELECT c.name, COUNT(s.id) AS n FROM courses c LEFT JOIN students s ON s.course_id=c.id GROUP BY c.id ORDER BY n DESC")->fetchAll();
$max = max(1, ...array_column($byCourse, 'n'));
$recent = $pdo->query("SELECT s.id, s.full_name, s.roll_no, c.code FROM students s JOIN courses c ON c.id=s.course_id ORDER BY s.created_at DESC, s.id DESC LIMIT 5")->fetchAll();
?>
<div class="stats">
  <div class="stat"><span><?= $total ?></span>Students enrolled</div>
  <div class="stat"><span><?= $active ?></span>Active</div>
  <div class="stat"><span><?= $grad ?></span>Graduated</div>
  <div class="stat"><span><?= $ncour ?></span>Courses</div>
</div>
<div class="two-col">
  <section class="panel">
    <h2>Students per course</h2>
    <?php foreach ($byCourse as $c): ?>
      <div class="bar-row">
        <span class="bar-label"><?= e($c['name']) ?></span>
        <div class="bar"><i style="width:<?= round($c['n'] / $max * 100) ?>%"></i></div>
        <b><?= (int)$c['n'] ?></b>
      </div>
    <?php endforeach; ?>
  </section>
  <section class="panel">
    <h2>Recently added</h2>
    <?php if (!$recent): ?><p class="muted">No students yet. <a href="student_form.php">Add the first one</a>.</p><?php endif; ?>
    <ul class="plain">
      <?php foreach ($recent as $r): ?>
        <li><a href="student_form.php?id=<?= (int)$r['id'] ?>"><?= e($r['full_name']) ?></a><span class="muted"><?= e($r['roll_no']) ?></span></li>
      <?php endforeach; ?>
    </ul>
    <a class="btn btn-primary" href="student_form.php">Add student</a>
  </section>
</div>
<?php require 'includes/footer.php'; ?>
