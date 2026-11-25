<?php
$title = 'Students';
require 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    check_csrf();
    $pdo->prepare("DELETE FROM students WHERE id = ?")->execute([(int)$_POST['id']]);
    flash('Student deleted.');
    header('Location: students.php'); exit;
}

$q = trim($_GET['q'] ?? '');
$course = (int)($_GET['course'] ?? 0);
$status = $_GET['status'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$per = 6;

$where = ' WHERE 1=1'; $p = [];
if ($q !== '')     { $where .= " AND (s.full_name LIKE ? OR s.roll_no LIKE ? OR s.email LIKE ?)"; array_push($p, "%$q%", "%$q%", "%$q%"); }
if ($course)       { $where .= " AND s.course_id = ?"; $p[] = $course; }
if (in_array($status, ['active','graduated','suspended'], true)) { $where .= " AND s.status = ?"; $p[] = $status; }

$cnt = $pdo->prepare("SELECT COUNT(*) FROM students s" . $where);
$cnt->execute($p);
$total = (int)$cnt->fetchColumn();
$pages = max(1, (int)ceil($total / $per));
$page = min($page, $pages);

$st = $pdo->prepare("SELECT s.*, c.code FROM students s JOIN courses c ON c.id=s.course_id $where ORDER BY s.roll_no LIMIT :l OFFSET :o");
foreach ($p as $i => $v) $st->bindValue($i + 1, $v);
$st->bindValue(':l', $per, PDO::PARAM_INT);
$st->bindValue(':o', ($page - 1) * $per, PDO::PARAM_INT);
$st->execute();
$rows = $st->fetchAll();
$courses = $pdo->query("SELECT * FROM courses ORDER BY name")->fetchAll();
$qs = fn($pg) => '?' . http_build_query(['q' => $q, 'course' => $course ?: '', 'status' => $status, 'page' => $pg]);
?>
<form class="filters" method="get">
  <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search name, roll no or email" aria-label="Search students">
  <select name="course" aria-label="Filter by course">
    <option value="">All courses</option>
    <?php foreach ($courses as $c): ?><option value="<?= (int)$c['id'] ?>" <?= $course === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
  </select>
  <select name="status" aria-label="Filter by status">
    <option value="">Any status</option>
    <?php foreach (['active','graduated','suspended'] as $s): ?><option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach; ?>
  </select>
  <button class="btn" type="submit">Apply</button>
  <a class="btn btn-primary" href="student_form.php">Add student</a>
</form>

<?php if (!$rows): ?>
  <p class="empty">No students match these filters. <a href="students.php">Clear filters</a></p>
<?php else: ?>
<table class="table">
  <thead><tr><th>Roll no</th><th>Name</th><th>Course</th><th>Year</th><th>Status</th><th></th></tr></thead>
  <tbody>
  <?php foreach ($rows as $r): ?>
    <tr>
      <td data-label="Roll no"><?= e($r['roll_no']) ?></td>
      <td data-label="Name"><strong><?= e($r['full_name']) ?></strong><br><span class="muted small"><?= e($r['email']) ?></span></td>
      <td data-label="Course"><?= e($r['code']) ?></td>
      <td data-label="Year"><?= (int)$r['year'] ?></td>
      <td data-label="Status"><span class="pill <?= e($r['status']) ?>"><?= e($r['status']) ?></span></td>
      <td class="actions">
        <a class="btn btn-sm" href="student_form.php?id=<?= (int)$r['id'] ?>">Edit</a>
        <form method="post" class="inline" data-confirm="Delete <?= e($r['full_name']) ?>? This cannot be undone.">
          <input type="hidden" name="csrf" value="<?= csrf() ?>">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
          <button class="btn btn-sm btn-danger" type="submit">Delete</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<nav class="pager" aria-label="Pages">
  <span class="muted"><?= $total ?> student<?= $total === 1 ? '' : 's' ?></span>
  <?php if ($page > 1): ?><a class="btn btn-sm" href="<?= $qs($page - 1) ?>">Previous</a><?php endif; ?>
  <span>Page <?= $page ?> of <?= $pages ?></span>
  <?php if ($page < $pages): ?><a class="btn btn-sm" href="<?= $qs($page + 1) ?>">Next</a><?php endif; ?>
</nav>
<?php endif; ?>
<?php require 'includes/footer.php'; ?>
