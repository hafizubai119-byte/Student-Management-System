<?php
$title = 'Courses';
require 'includes/header.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    if (($_POST['action'] ?? '') === 'add') {
        $code = strtoupper(trim($_POST['code'] ?? '')); $name = trim($_POST['name'] ?? '');
        if (!preg_match('/^[A-Z0-9]{2,12}$/', $code)) $errors[] = 'Course code needs 2 to 12 letters or numbers.';
        if (strlen($name) < 3) $errors[] = 'Enter the course name.';
        $d = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE code=?"); $d->execute([$code]);
        if ($d->fetchColumn()) $errors[] = 'That course code already exists.';
        if (!$errors) {
            $pdo->prepare("INSERT INTO courses (code, name) VALUES (?,?)")->execute([$code, $name]);
            flash('Course added.'); header('Location: courses.php'); exit;
        }
    } elseif (($_POST['action'] ?? '') === 'delete') {
        $id = (int)$_POST['id'];
        $n = $pdo->prepare("SELECT COUNT(*) FROM students WHERE course_id=?"); $n->execute([$id]);
        if ($n->fetchColumn()) { $errors[] = 'Move or delete the students in this course before removing it.'; }
        else { $pdo->prepare("DELETE FROM courses WHERE id=?")->execute([$id]); flash('Course deleted.'); header('Location: courses.php'); exit; }
    }
}
$list = $pdo->query("SELECT c.*, COUNT(s.id) AS n FROM courses c LEFT JOIN students s ON s.course_id=c.id GROUP BY c.id ORDER BY c.name")->fetchAll();
?>
<?php foreach ($errors as $er): ?><p class="alert"><?= e($er) ?></p><?php endforeach; ?>
<div class="two-col">
  <section class="panel">
    <h2>All courses</h2>
    <table class="table">
      <thead><tr><th>Code</th><th>Name</th><th>Students</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($list as $c): ?>
        <tr>
          <td data-label="Code"><?= e($c['code']) ?></td>
          <td data-label="Name"><?= e($c['name']) ?></td>
          <td data-label="Students"><?= (int)$c['n'] ?></td>
          <td class="actions">
            <form method="post" class="inline" data-confirm="Delete <?= e($c['name']) ?>?">
              <input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
              <button class="btn btn-sm btn-danger" type="submit">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </section>
  <form method="post" class="form panel">
    <h2>Add a course</h2>
    <input type="hidden" name="csrf" value="<?= csrf() ?>"><input type="hidden" name="action" value="add">
    <label>Code <input name="code" placeholder="e.g. DCS" required></label>
    <label>Name <input name="name" placeholder="e.g. Diploma in Cyber Security" required></label>
    <button class="btn btn-primary" type="submit">Add course</button>
  </form>
</div>
<?php require 'includes/footer.php'; ?>
