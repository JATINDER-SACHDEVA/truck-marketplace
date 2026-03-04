<?php
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin($pdo);
$title = 'Admin Panel';

$pending = $pdo->query('SELECT l.*, u.email FROM listings l JOIN users u ON u.id = l.user_id WHERE l.status = "pending" ORDER BY l.created_at DESC')->fetchAll();
$users = $pdo->query('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC')->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>
<div class="form-card">
    <h2>Pending Listings Approval</h2>
    <table class="table">
        <tr><th>Truck</th><th>User</th><th>Price</th><th>Action</th></tr>
        <?php foreach ($pending as $item): ?>
            <tr>
                <td><?= e($item['brand'] . ' ' . $item['model']) ?></td>
                <td><?= e($item['email']) ?></td>
                <td>₹<?= number_format((float)$item['price']) ?></td>
                <td>
                    <a href="<?= BASE_URL ?>/admin/approve.php?id=<?= (int)$item['id'] ?>">Approve</a> |
                    <a href="<?= BASE_URL ?>/admin/delete-listing.php?id=<?= (int)$item['id'] ?>">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<div class="form-card">
    <h2>Manage Users</h2>
    <table class="table">
        <tr><th>Name</th><th>Email</th><th>Role</th><th>Action</th></tr>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= e($u['name']) ?></td>
                <td><?= e($u['email']) ?></td>
                <td><?= e($u['role']) ?></td>
                <td><?php if ((int)$u['id'] !== (int)$_SESSION['user']['id']): ?><a href="<?= BASE_URL ?>/admin/delete-user.php?id=<?= (int)$u['id'] ?>" onclick="return confirm('Delete user?')">Delete</a><?php endif; ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
