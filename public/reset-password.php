<?php
require_once __DIR__ . '/../includes/functions.php';
$title = 'Reset Password';
$token = $_GET['token'] ?? $_POST['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        flash('error', 'Invalid request.');
        redirect('forgot-password.php');
    }

    $password = $_POST['password'] ?? '';
    if (strlen($password) < 6) {
        flash('error', 'Password must be at least 6 characters.');
        redirect('reset-password.php?token=' . urlencode($token));
    }

    $stmt = $pdo->prepare('SELECT * FROM password_resets WHERE token = :token AND expires_at > NOW() ORDER BY id DESC LIMIT 1');
    $stmt->execute(['token' => $token]);
    $reset = $stmt->fetch();

    if (!$reset) {
        flash('error', 'Invalid or expired token.');
        redirect('forgot-password.php');
    }

    $pdo->prepare('UPDATE users SET password_hash = :password_hash WHERE id = :id')
        ->execute(['password_hash' => password_hash($password, PASSWORD_DEFAULT), 'id' => $reset['user_id']]);
    $pdo->prepare('DELETE FROM password_resets WHERE user_id = :user_id')->execute(['user_id' => $reset['user_id']]);

    flash('success', 'Password reset successful. Please login.');
    redirect('login.php');
}

include __DIR__ . '/../includes/header.php';
?>
<div class="form-card">
<h2>Reset Password</h2>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
<input type="hidden" name="token" value="<?= e($token) ?>">
<label>New Password</label><input type="password" name="password" required>
<button class="btn" type="submit">Reset Password</button>
</form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
