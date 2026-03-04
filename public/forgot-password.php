<?php
require_once __DIR__ . '/../includes/functions.php';
$title = 'Forgot Password';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        flash('error', 'Invalid request.');
        redirect('forgot-password.php');
    }

    $email = strtolower(trim($_POST['email'] ?? ''));
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        $token = bin2hex(random_bytes(24));
        $expiresAt = date('Y-m-d H:i:s', time() + 3600);

        $pdo->prepare('INSERT INTO password_resets (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)')
            ->execute(['user_id' => $user['id'], 'token' => $token, 'expires_at' => $expiresAt]);

        flash('success', 'Reset link generated (dev mode): ' . BASE_URL . '/reset-password.php?token=' . $token);
    } else {
        flash('success', 'If email exists, reset instructions have been sent.');
    }

    redirect('forgot-password.php');
}

include __DIR__ . '/../includes/header.php';
?>
<div class="form-card">
<h2>Forgot Password</h2>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
<label>Email</label><input type="email" name="email" required>
<button class="btn" type="submit">Send Reset Link</button>
</form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
