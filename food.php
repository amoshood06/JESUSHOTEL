<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';
include 'header-one.php';

// Get filter parameters
$selectedCategory = sanitize($_GET['category'] ?? '');
$minPrice = filter_input(INPUT_GET, 'min_price', FILTER_VALIDATE_FLOAT);
$maxPrice = filter_input(INPUT_GET, 'max_price', FILTER_VALIDATE_FLOAT);

$sql = "SELECT * FROM food_menu WHERE availability = 1 AND LOWER(category) NOT LIKE '%drink%' AND LOWER(category) NOT LIKE '%local brew%'";
$params = [];

if ($selectedCategory) {
    $sql .= " AND category = :category";
    $params[':category'] = $selectedCategory;
}

if ($minPrice !== false && $minPrice !== null && $minPrice >= 0) {
    $sql .= " AND price >= :min_price";
    $params[':min_price'] = $minPrice;
}

if ($maxPrice !== false && $maxPrice !== null && $maxPrice >= 0) {
    $sql .= " AND price <= :max_price";
    $params[':max_price'] = $maxPrice;
}

$sql .= " ORDER BY category, item_name";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $food_items = $stmt->fetchAll();

    // Re-fetch all distinct categories for the filter dropdown
    $allCategoriesStmt = $pdo->query("SELECT DISTINCT category FROM food_menu WHERE availability = 1 ORDER BY category");
    $allCategories = $allCategoriesStmt->fetchAll(PDO::FETCH_COLUMN);

    $categories_grouped = [];
    foreach ($food_items as $item) {
        $categories_grouped[$item['category']][] = $item;
    }

} catch (PDOException $e) {
    error_log("Error fetching food menu: " . $e->getMessage());
    $food_items = []; // Ensure $food_items is an empty array on error
    $allCategories = [];
    $categories_grouped = [];
}
?>

<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Our Food Menu</h1>

        <form method="GET" action="food.php" class="mb-8 p-6 bg-white rounded-lg shadow-md">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Category Filter -->
                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                    <select id="category" name="category" class="mt-1 p-2 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm rounded-md">
                        <option value="">All Categories</option>
                        <?php foreach ($allCategories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= ($selectedCategory === $cat) ? 'selected' : '' ?>><?= htmlspecialchars($cat) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Min Price Filter -->
                <div>
                    <label for="min_price" class="block text-sm font-medium text-gray-700">Min Price</label>
                    <input type="number" id="min_price" name="min_price" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>"
                           class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500" placeholder="e.g., 1000">
                </div>

                <!-- Max Price Filter -->
                <div>
                    <label for="max_price" class="block text-sm font-medium text-gray-700">Max Price</label>
                    <input type="number" id="max_price" name="max_price" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>"
                           class="mt-1 p-2 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-teal-500 focus:border-teal-500" placeholder="e.g., 5000">
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-black hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                    Apply Filters
                </button>
            </div>
        </form>


        <?php if (empty($food_items)): ?>
            <p class="text-center text-gray-600 text-lg">No food items available matching your filters. Please adjust your criteria.</p>
        <?php else: ?>
            <?php foreach ($categories_grouped as $category_name => $items_in_category): ?>
                <h2 class="text-2xl font-semibold text-gray-700 mt-10 mb-6 border-b-2 border-teal-500 pb-2"><?= htmlspecialchars($category_name) ?></h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($items_in_category as $item): ?>
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                            <img src="<?= htmlspecialchars($item['image_url'] ?? 'https://via.placeholder.com/400x300?text=No+Image') ?>"
                                 alt="<?= htmlspecialchars($item['item_name']) ?>"
                                 class="w-full h-48 object-center">
                            <div class="p-6">
                                <h3 class="text-xl font-semibold text-gray-800 mb-2"><?= htmlspecialchars($item['item_name']) ?></h3>
                                <p class="text-gray-600 text-sm mb-4"><?= htmlspecialchars($item['description'] ?? 'No description available.') ?></p>
                                <div class="flex items-center justify-between">
                                    <span class="text-2xl font-bold text-black"><?= formatCurrency($item['price']) ?></span>
                                    <form class="add-to-cart-form">
                                        <input type="hidden" name="item_id" value="<?= $item['menu_item_id'] ?>">
                                        <input type="hidden" name="item_name" value="<?= htmlspecialchars($item['item_name']) ?>">
                                        <input type="hidden" name="item_price" value="<?= $item['price'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit" class="bg-black text-white px-5 py-2 rounded-lg hover:bg-black transition-colors focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                                            Add to Cart
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer-one.php'; ?>