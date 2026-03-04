<?php
require_once __DIR__ . '/../../includes/functions.php';
requireLogin();
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM listings WHERE id = :id AND user_id = :user_id');
$stmt->execute(['id' => $id, 'user_id' => $_SESSION['user']['id']]);
$listing = $stmt->fetch();
if (!$listing) {
    exit('Listing not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        flash('error', 'Invalid request.');
        redirect('dashboard/edit-listing.php?id=' . $id);
    }

    $data = [
        'id' => $id,
        'brand' => trim($_POST['brand'] ?? ''),
        'model' => trim($_POST['model'] ?? ''),
        'price' => (float)($_POST['price'] ?? 0),
        'location' => trim($_POST['location'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
    ];

    $pdo->prepare('UPDATE listings SET brand=:brand, model=:model, price=:price, location=:location, description=:description, phone=:phone, status="pending" WHERE id=:id')
        ->execute($data);

    flash('success', 'Listing updated and sent for re-approval.');
    redirect('dashboard/index.php');
}

$title = 'Edit Listing';
include __DIR__ . '/../../includes/header.php';
?>
<div class="form-card">
<h2>Edit Listing</h2>
<form method="POST">
<input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
<input type="hidden" name="id" value="<?= (int)$listing['id'] ?>">
<label>Brand</label><input name="brand" value="<?= e($listing['brand']) ?>" required>
<label>Model</label><input name="model" value="<?= e($listing['model']) ?>" required>
<label>Price</label><input type="number" name="price" value="<?= e((string)$listing['price']) ?>" required>
<label>Location</label><input name="location" value="<?= e($listing['location']) ?>" required>
<label>Description</label><textarea name="description" required><?= e($listing['description']) ?></textarea>
<label>Phone</label><input name="phone" value="<?= e($listing['phone']) ?>" required>
<button class="btn" type="submit">Save</button>
</form>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
