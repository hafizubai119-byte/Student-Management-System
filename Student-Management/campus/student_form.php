<?php
require_once 'config.php';
require_login();
$id = (int)($_GET['id'] ?? 0);
$v = ['roll_no'=>'','full_name'=>'','email'=>'','phone'=>'','course_id'=>'','year'=>1,'status'=>'active'];
if ($id) {
    $st = $pdo->prepare("SELECT * FROM students WHERE id=?"); $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) { flash('That student no longer exists.'); header('Location: students.php'); exit; }
    $v = $row;
}
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    foreach (['roll_no','full_name','email','phone'] as $k) $v[$k] = trim($_POST[$k] ?? '');
    $v['course_id'] = (int)($_POST['course_id'] ?? 0);
    $v['year'] = (int)($_POST['year'] ?? 1);
    $v['status'] = $_POST['status'] ?? 'active';

    if (!preg_match('/^[A-Za-z0-9-]{3,20}$/', $v['roll_no'])) $errors['roll_no'] = 'Use 3 to 20 letters, numbers or dashes.';
    if (strlen($v['full_name']) < 2) $errors['full_name'] = 'Enter the full name.';
    if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Enter a valid email address.';
    if ($v['phone'] !== '' && !preg_match('/^[0-9+\- ]{7,20}$/', $v['phone'])) $errors['phone'] = 'Use digits, spaces, + or - only.';
    if ($v['year'] < 1 || $v['year'] > 3) $errors['year'] = 'Choose year 1, 2 or 3.';
    if (!in_array($v['status'], ['active','graduated','suspended'], true)) $errors['status'] = 'Choose a status.';
    $c = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE id=?"); $c->execute([$v['course_id']]);
    if (!$c->fetchColumn()) $errors['course_id'] = 'Choose a course.';
    $d = $pdo->prepare("SELECT COUNT(*) FROM students WHERE roll_no=? AND id<>?"); $d->execute([$v['roll_no'], $id]);
    if ($d->fetchColumn()) $errors['roll_no'] = 'That roll number is already used.';

    if (!$errors) {
        $args = [$v['roll_no'], $v['full_name'], $v['email'], $v['phone'], $v['course_id'], $v['year'], $v['status']];
        if ($id) {
            $pdo->prepare("UPDATE students SET roll_no=?, full_name=?, email=?, phone=?, course_id=?, year=?, status=? WHERE id=?")->execute([...$args, $id]);
            flash('Student updated.');
        } else {
            $pdo->prepare("INSERT INTO students (roll_no, full_name, email, phone, course_id, year, status) VALUES (?,?,?,?,?,?,?)")->execute($args);
            flash('Student added.');
        }
        header('Location: students.php'); exit;
    }
}
$title = $id ? 'Edit student' : 'Add student';
require 'includes/header.php';
$courses = $pdo->query("SELECT * FROM courses ORDER BY name")->fetchAll();
$field = fn($k) => isset($errors[$k]) ? '<span class="err">' . e($errors[$k]) . '</span>' : '';
?>
<form method="post" class="form panel narrow" novalidate>
  <input type="hidden" name="csrf" value="<?= csrf() ?>">
  <div class="grid-2">
    <label>Roll number <input name="roll_no" value="<?= e($v['roll_no']) ?>" required><?= $field('roll_no') ?></label>
    <label>Full name <input name="full_name" value="<?= e($v['full_name']) ?>" required><?= $field('full_name') ?></label>
    <label>Email <input name="email" type="email" value="<?= e($v['email']) ?>" required><?= $field('email') ?></label>
    <label>Phone <input name="phone" value="<?= e($v['phone']) ?>"><?= $field('phone') ?></label>
    <label>Course
      <select name="course_id" required>
        <option value="">Choose a course</option>
        <?php foreach ($courses as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (int)$v['course_id'] === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?>
      </select><?= $field('course_id') ?>
    </label>
    <label>Year
      <select name="year"><?php for ($y = 1; $y <= 3; $y++): ?><option value="<?= $y ?>" <?= (int)$v['year'] === $y ? 'selected' : '' ?>>Year <?= $y ?></option><?php endfor; ?></select><?= $field('year') ?>
    </label>
    <label>Status
      <select name="status"><?php foreach (['active','graduated','suspended'] as $s): ?><option value="<?= $s ?>" <?= $v['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach; ?></select><?= $field('status') ?>
    </label>
  </div>
  <div class="form-actions">
    <button class="btn btn-primary" type="submit"><?= $id ? 'Save changes' : 'Add student' ?></button>
    <a class="btn" href="students.php">Cancel</a>
  </div>
</form>
<?php require 'includes/footer.php'; ?>
