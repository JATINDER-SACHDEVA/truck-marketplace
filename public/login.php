<?php
require_once __DIR__ . '/../includes/functions.php';
$title = 'Login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        flash('error', 'Invalid request.');
        redirect('login.php');
    }

    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, name, email, password_hash, role FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        flash('error', 'Invalid credentials.');
        redirect('login.php');
    }

    $_SESSION['user'] = [
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
    ];

    redirect('dashboard/index.php');
}

include __DIR__ . '/../includes/header.php';
?>
<div class="form-card">
<h2>Login</h2>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
<label>Email</label><input type="email" name="email" required>
<label>Password</label><input type="password" name="password" required>
<button class="btn" type="submit">Login</button>
</form>
<p><a href="<?= BASE_URL ?>/forgot-password.php">Forgot password?</a></p>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
