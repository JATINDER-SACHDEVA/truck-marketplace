<?php
require_once __DIR__ . '/../includes/functions.php';
requireLogin();
$title = 'Post Truck Ad';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        flash('error', 'Invalid request.');
        redirect('create-listing.php');
    }

    if (!empty($_POST['website'])) {
        flash('error', 'Spam detected.');
        redirect('create-listing.php');
    }

    if (!postingRateLimit()) {
        flash('error', 'Too many attempts. Please wait a minute.');
        redirect('create-listing.php');
    }

    $data = [
        'user_id' => $_SESSION['user']['id'],
        'brand' => trim($_POST['brand'] ?? ''),
        'model' => trim($_POST['model'] ?? ''),
        'year' => (int)($_POST['year'] ?? 0),
        'fuel_type' => $_POST['fuel_type'] ?? '',
        'price' => (float)($_POST['price'] ?? 0),
        'location' => trim($_POST['location'] ?? ''),
        'description' => trim($_POST['description'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'category' => $_POST['category'] ?? '',
    ];

    if (!$data['brand'] || !$data['model'] || !$data['year'] || !$data['price'] || !$data['location'] || !$data['phone']) {
        flash('error', 'Please fill all required fields.');
        redirect('create-listing.php');
    }

    $stmt = $pdo->prepare('INSERT INTO listings (user_id, category, brand, model, year, fuel_type, price, location, description, phone, status) VALUES (:user_id,:category,:brand,:model,:year,:fuel_type,:price,:location,:description,:phone,"pending")');
    $stmt->execute($data);
    $listingId = (int)$pdo->lastInsertId();

    $files = uploadImages($_FILES['images'] ?? []);
    $imgStmt = $pdo->prepare('INSERT INTO listing_images (listing_id, image_path) VALUES (:listing_id, :image_path)');
    foreach ($files as $file) {
        $imgStmt->execute(['listing_id' => $listingId, 'image_path' => $file]);
    }

    flash('success', 'Listing submitted and awaiting admin approval.');
    redirect('dashboard/index.php');
}

include __DIR__ . '/../includes/header.php';
?>
<div class="form-card">
    <h2>Post Your Truck Ad</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input type="text" name="website" style="display:none" tabindex="-1" autocomplete="off">
        <label>Category</label>
        <select name="category" required><?php foreach (listingCategories() as $category): ?><option><?= e($category) ?></option><?php endforeach; ?></select>
        <label>Truck Brand</label><input name="brand" required>
        <label>Model</label><input name="model" required>
        <label>Year</label><input type="number" name="year" min="1990" max="<?= date('Y') ?>" required>
        <label>Fuel Type</label><select name="fuel_type" required><?php foreach (fuelTypes() as $fuel): ?><option><?= e($fuel) ?></option><?php endforeach; ?></select>
        <label>Price (₹)</label><input type="number" name="price" required>
        <label>Location</label><input name="location" data-location-input required>
        <label>Description</label><textarea name="description" rows="5" required></textarea>
        <label>Phone Number</label><input name="phone" required>
        <label>Upload Photos (max <?= MAX_IMAGES ?>)</label><input type="file" name="images[]" accept="image/*" multiple>
        <button class="btn" type="submit">Submit Listing</button>
    </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
