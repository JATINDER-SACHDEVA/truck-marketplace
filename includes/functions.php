<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(): bool
{
    return isset($_POST['csrf_token'], $_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

function redirect(string $path): void
{
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    if (!empty($_SESSION['flash'][$key])) {
        $msg = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $msg;
    }

    return null;
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['user']['id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        flash('error', 'Please login to continue.');
        redirect('login.php');
    }
}

function requireAdmin(PDO $pdo): void
{
    requireLogin();
    $stmt = $pdo->prepare('SELECT role FROM users WHERE id = :id');
    $stmt->execute(['id' => $_SESSION['user']['id']]);
    $user = $stmt->fetch();

    if (!$user || $user['role'] !== 'admin') {
        http_response_code(403);
        exit('Forbidden: Admin access required.');
    }
}

function currentUser(PDO $pdo): ?array
{
    if (!isLoggedIn()) {
        return null;
    }

    $stmt = $pdo->prepare('SELECT id, name, email, role, location FROM users WHERE id = :id');
    $stmt->execute(['id' => $_SESSION['user']['id']]);
    return $stmt->fetch() ?: null;
}

function uploadImages(array $files): array
{
    if (!is_dir(UPLOAD_PATH)) {
        mkdir(UPLOAD_PATH, 0775, true);
    }

    $savedFiles = [];
    if (empty($files['name'][0])) {
        return $savedFiles;
    }

    $count = min(count($files['name']), MAX_IMAGES);
    $finfo = new finfo(FILEINFO_MIME_TYPE);

    for ($i = 0; $i < $count; $i++) {
        if ($files['error'][$i] !== UPLOAD_ERR_OK) {
            continue;
        }
        if ($files['size'][$i] > MAX_IMAGE_SIZE) {
            continue;
        }

        $tmpName = $files['tmp_name'][$i];
        $mime = $finfo->file($tmpName);
        if (!in_array($mime, ALLOWED_IMAGE_MIME, true)) {
            continue;
        }

        $ext = match ($mime) {
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => null,
        };

        if (!$ext) {
            continue;
        }

        $fileName = bin2hex(random_bytes(16)) . '.' . $ext;
        $destination = UPLOAD_PATH . '/' . $fileName;

        if (move_uploaded_file($tmpName, $destination)) {
            $savedFiles[] = $fileName;
        }
    }

    return $savedFiles;
}

function postingRateLimit(): bool
{
    $window = 60;
    $maxPosts = 5;

    if (!isset($_SESSION['post_attempts'])) {
        $_SESSION['post_attempts'] = [];
    }

    $now = time();
    $_SESSION['post_attempts'] = array_filter(
        $_SESSION['post_attempts'],
        static fn($timestamp) => ($now - $timestamp) <= $window
    );

    if (count($_SESSION['post_attempts']) >= $maxPosts) {
        return false;
    }

    $_SESSION['post_attempts'][] = $now;
    return true;
}

function listingCategories(): array
{
    return ['Pickup', 'Mini Truck', 'Heavy Truck', 'Tipper'];
}

function fuelTypes(): array
{
    return ['Diesel', 'Petrol', 'CNG', 'Electric', 'LPG'];
}
