<?php
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
$id = (int)($_GET['id'] ?? 0);

$pdo->prepare('DELETE FROM listings WHERE id = :id AND user_id = :user_id')
    ->execute(['id' => $id, 'user_id' => $_SESSION['user']['id']]);

flash('success', 'Listing deleted.');
redirect('dashboard/index.php');
