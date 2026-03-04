<?php
require_once __DIR__ . '/../includes/functions.php';
$id = (int)($_GET['id'] ?? 0);

$stmt = $pdo->prepare('SELECT l.*, u.name AS seller_name FROM listings l JOIN users u ON u.id = l.user_id WHERE l.id = :id AND l.status = "approved"');
$stmt->execute(['id' => $id]);
$listing = $stmt->fetch();

if (!$listing) {
    http_response_code(404);
    exit('Listing not found.');
}

$imgStmt = $pdo->prepare('SELECT image_path FROM listing_images WHERE listing_id = :id');
$imgStmt->execute(['id' => $id]);
$images = $imgStmt->fetchAll();

$title = $listing['brand'] . ' ' . $listing['model'];
include __DIR__ . '/../includes/header.php';
?>
<div class="gallery">
    <div class="card">
        <img src="<?= !empty($images) ? BASE_URL . '/../uploads/' . e($images[0]['image_path']) : 'https://via.placeholder.com/600x400?text=Truck' ?>" alt="Main image" style="height:350px">
    </div>
    <div class="thumbs">
        <?php foreach ($images as $img): ?>
            <img src="<?= BASE_URL . '/../uploads/' . e($img['image_path']) ?>" alt="Truck photo" style="width:100%;height:100px;object-fit:cover;border-radius:8px">
        <?php endforeach; ?>
    </div>
</div>
<div class="form-card">
    <h2><?= e($listing['brand'] . ' ' . $listing['model']) ?></h2>
    <p class="price">₹<?= number_format((float)$listing['price']) ?></p>
    <p><?= e($listing['description']) ?></p>
    <p><strong>Category:</strong> <?= e($listing['category']) ?> | <strong>Year:</strong> <?= e($listing['year']) ?> | <strong>Fuel:</strong> <?= e($listing['fuel_type']) ?></p>
    <p><strong>Location:</strong> <?= e($listing['location']) ?></p>
    <p><strong>Seller:</strong> <?= e($listing['seller_name']) ?> | <strong>Phone:</strong> <a href="tel:<?= e($listing['phone']) ?>"><?= e($listing['phone']) ?></a></p>
    <a class="btn" target="_blank" href="https://wa.me/91<?= preg_replace('/\D/', '', $listing['phone']) ?>?text=Hi,%20I%20am%20interested%20in%20your%20truck%20listing%20(<?= urlencode($listing['brand'] . ' ' . $listing['model']) ?>)">Contact on WhatsApp</a>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
