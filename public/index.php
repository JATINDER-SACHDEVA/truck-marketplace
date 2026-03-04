<?php
require_once __DIR__ . '/../includes/functions.php';
$title = 'Buy & Sell Used Trucks in India';

$conditions = ['l.status = "approved"'];
$params = [];

if (!empty($_GET['q'])) {
    $conditions[] = '(l.brand LIKE :q OR l.model LIKE :q OR l.description LIKE :q)';
    $params['q'] = '%' . $_GET['q'] . '%';
}
if (!empty($_GET['location'])) {
    $conditions[] = 'l.location LIKE :location';
    $params['location'] = '%' . $_GET['location'] . '%';
}
if (!empty($_GET['min_price'])) {
    $conditions[] = 'l.price >= :min_price';
    $params['min_price'] = (int)$_GET['min_price'];
}
if (!empty($_GET['max_price'])) {
    $conditions[] = 'l.price <= :max_price';
    $params['max_price'] = (int)$_GET['max_price'];
}
if (!empty($_GET['category'])) {
    $conditions[] = 'l.category = :category';
    $params['category'] = $_GET['category'];
}

$sql = 'SELECT l.*, (SELECT image_path FROM listing_images WHERE listing_id = l.id LIMIT 1) AS cover FROM listings l WHERE ' . implode(' AND ', $conditions) . ' ORDER BY l.created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$listings = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<section class="form-card">
    <form method="GET" class="filters">
        <input type="text" name="q" placeholder="Search brand, model, keyword" value="<?= e($_GET['q'] ?? '') ?>">
        <input type="text" name="location" placeholder="Location" value="<?= e($_GET['location'] ?? '') ?>">
        <input type="number" name="min_price" placeholder="Min ₹" value="<?= e($_GET['min_price'] ?? '') ?>">
        <input type="number" name="max_price" placeholder="Max ₹" value="<?= e($_GET['max_price'] ?? '') ?>">
        <select name="category">
            <option value="">All Categories</option>
            <?php foreach (listingCategories() as $cat): ?>
                <option <?= (($_GET['category'] ?? '') === $cat) ? 'selected' : '' ?>><?= e($cat) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn" type="submit">Search</button>
    </form>
</section>

<section class="grid">
<?php foreach ($listings as $listing): ?>
    <article class="card">
        <img src="<?= $listing['cover'] ? BASE_URL . '/../uploads/' . e($listing['cover']) : 'https://via.placeholder.com/400x300?text=Truck' ?>" alt="Truck image">
        <div class="card-body">
            <div class="price">₹<?= number_format((float)$listing['price']) ?></div>
            <h3><?= e($listing['brand'] . ' ' . $listing['model']) ?></h3>
            <p class="muted"><?= e($listing['year']) ?> • <?= e($listing['fuel_type']) ?> • <?= e($listing['location']) ?></p>
            <a class="btn" href="<?= BASE_URL ?>/listing.php?id=<?= (int)$listing['id'] ?>">View Details</a>
        </div>
    </article>
<?php endforeach; ?>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
