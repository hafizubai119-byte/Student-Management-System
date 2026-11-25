# Campus: Student Management System (PHP + MySQL)

## Run it
1. Put the `campus` folder in `htdocs` (XAMPP) or `www` (WAMP).
2. Start Apache and MySQL, then import `schema.sql` in phpMyAdmin (creates the `campus` database with sample data).
3. Edit `config.php` if your MySQL user has a password.
4. Open http://localhost/campus/ and log in with **admin / admin123**.

(Or run `php -S localhost:8000` inside the folder. PHP 7.4+ required.)

## Features
- Login with hashed password and session; all pages are protected.
- Dashboard: totals and a students-per-course bar chart from SQL JOIN + GROUP BY.
- Students: add, edit, delete; search, course/status filters, pagination.
- Courses: add and delete (blocked while students are enrolled).
- Security: PDO prepared statements, CSRF tokens on every write, output escaping, server-side validation.
- UI: sidebar layout that becomes a slide-out menu on tablets, table that turns into cards on phones.
