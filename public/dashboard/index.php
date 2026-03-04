<?php
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
$title = 'My Dashboard';

$stmt = $pdo->prepare('SELECT * FROM listings WHERE user_id = :user_id ORDER BY created_at DESC');
$stmt->execute(['user_id' => $_SESSION['user']['id']]);
$listings = $stmt->fetchAll();

include __DIR__ . '/../../includes/header.php';
?>
<div class="form-card">
    <h2>My Listings</h2>
    <a class="btn" href="<?= BASE_URL ?>/create-listing.php">+ Post New Listing</a>
    <table class="table">
        <tr><th>Truck</th><th>Price</th><th>Status</th><th>Actions</th></tr>
        <?php foreach ($listings as $listing): ?>
            <tr>
                <td><?= e($listing['brand'] . ' ' . $listing['model']) ?></td>
                <td>₹<?= number_format((float)$listing['price']) ?></td>
                <td><?= e($listing['status']) ?><?= $listing['is_sold'] ? ' (Sold)' : '' ?></td>
                <td>
                    <a href="<?= BASE_URL ?>/dashboard/edit-listing.php?id=<?= (int)$listing['id'] ?>">Edit</a> |
                    <a href="<?= BASE_URL ?>/dashboard/delete-listing.php?id=<?= (int)$listing['id'] ?>" onclick="return confirm('Delete listing?')">Delete</a> |
                    <?php if (!$listing['is_sold']): ?><a href="<?= BASE_URL ?>/dashboard/mark-sold.php?id=<?= (int)$listing['id'] ?>">Mark Sold</a><?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
