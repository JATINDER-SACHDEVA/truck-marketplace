<?php
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
$id = (int)($_GET['id'] ?? 0);

$pdo->prepare('UPDATE listings SET is_sold = 1 WHERE id = :id AND user_id = :user_id')
    ->execute(['id' => $id, 'user_id' => $_SESSION['user']['id']]);

flash('success', 'Listing marked as sold.');
redirect('dashboard/index.php');
