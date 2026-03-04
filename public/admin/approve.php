<?php
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin($pdo);
$id = (int)($_GET['id'] ?? 0);

$pdo->prepare('UPDATE listings SET status = "approved" WHERE id = :id')->execute(['id' => $id]);
flash('success', 'Listing approved.');
redirect('admin/index.php');
