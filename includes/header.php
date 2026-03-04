<?php
require_once __DIR__ . '/functions.php';
$user = currentUser($pdo);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Truck Bazaar India') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container nav-wrap">
        <a href="<?= BASE_URL ?>/index.php" class="brand">TruckBazaar</a>
        <nav>
            <a href="<?= BASE_URL ?>/index.php">Home</a>
            <?php if ($user): ?>
                <a href="<?= BASE_URL ?>/create-listing.php">Post Ad</a>
                <a href="<?= BASE_URL ?>/dashboard/index.php">Dashboard</a>
                <?php if ($user['role'] === 'admin'): ?>
                    <a href="<?= BASE_URL ?>/admin/index.php">Admin</a>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>/logout.php">Logout</a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>/login.php">Login</a>
                <a href="<?= BASE_URL ?>/register.php" class="btn">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">
    <?php if ($msg = flash('success')): ?><div class="alert success"><?= e($msg) ?></div><?php endif; ?>
    <?php if ($msg = flash('error')): ?><div class="alert error"><?= e($msg) ?></div><?php endif; ?>
