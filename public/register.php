<?php
require_once __DIR__ . '/../includes/functions.php';
$title = 'Register';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        flash('error', 'Invalid request.');
        redirect('register.php');
    }

    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $location = trim($_POST['location'] ?? '');

    if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 6) {
        flash('error', 'Please provide valid name, email, and password (min 6).');
        redirect('register.php');
    }

    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        flash('error', 'Email already exists.');
        redirect('register.php');
    }

    $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, location) VALUES (:name, :email, :password_hash, :location)');
    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        'location' => $location,
    ]);

    flash('success', 'Registration successful. Please login.');
    redirect('login.php');
}

include __DIR__ . '/../includes/header.php';
?>
<div class="form-card">
<h2>Create Account</h2>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
<label>Name</label><input name="name" required>
<label>Email</label><input type="email" name="email" required>
<label>Password</label><input type="password" name="password" required>
<label>Location</label><input name="location" data-location-input placeholder="e.g., Pune, Maharashtra">
<button class="btn" type="submit">Register</button>
</form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
