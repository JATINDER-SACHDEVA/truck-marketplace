<?php
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin($pdo);
$id = (int)($_GET['id'] ?? 0);

$pdo->prepare('DELETE FROM listings WHERE id = :id')->execute(['id' => $id]);
flash('success', 'Spam/listing deleted.');
redirect('admin/index.php');
