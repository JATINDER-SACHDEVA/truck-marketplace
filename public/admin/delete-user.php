<?php
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin($pdo);
$id = (int)($_GET['id'] ?? 0);

$pdo->prepare('DELETE FROM users WHERE id = :id AND role != "admin"')->execute(['id' => $id]);
flash('success', 'User removed.');
redirect('admin/index.php');
