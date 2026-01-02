<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';
include 'header.php';

// Get filter parameters
$selectedCategory = sanitize($_GET['category'] ?? ''); // This will likely be 'Drinks'
$selectedDrinkCategory = sanitize($_GET['drink_category'] ?? '');
$minPrice = filter_input(INPUT_GET, 'min_price', FILTER_VALIDATE_FLOAT);
$maxPrice = filter_input(INPUT_GET, 'max_price', FILTER_VALIDATE_FLOAT);

// Base query for drinks
$sql = "SELECT * FROM food_menu WHERE availability = 1 AND category = 'Drinks'";
$params = [];

if ($selectedDrinkCategory) {
    $sql .= " AND drink_category = :drink_category";
    $params[':drink_category'] = $selectedDrinkCategory;
}

if ($minPrice !== false && $minPrice !== null && $minPrice >= 0) {
    $sql .= " AND price >= :min_price";
    $params[':min_price'] = $minPrice;
}

if ($maxPrice !== false && $maxPrice !== null && $maxPrice >= 0) {
    $sql .= " AND price <= :max_price";
    $params[':max_price'] = $maxPrice;
}

$sql .= " ORDER BY drink_category, item_name";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $food_items = $stmt->fetchAll();

    // Grouping by drink_category
    $categories_grouped = []; 
    foreach ($food_items as $item) {
        $category_to_display = $item['drink_category'] ?: 'Other Drinks'; // Fallback for items without specific drink_category
        $categories_grouped[$category_to_display][] = $item;
    }

} catch (PDOException $e) {
    error_log("Error fetching food menu: " . $e->getMessage());
    $food_items = []; // Ensure $food_items is an empty array on error
    $categories_grouped = [];
}
?>

<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Our Drinks Menu</h1>

        <form method="GET" action="drink.php" class="mb-8 p-6 bg-white rounded-lg shadow-md">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Drink Category Filter -->
                <div>
                    <label for="drink_category" class="block text-sm font-medium text-gray-700">Drink Type</label>
                    <select id="drink_category" name="drink_category" class="mt-1 p-2 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-teal-500 focus:border-teal-500 sm:text-sm rounded-md">
                        <option value="">All Drink Types</option>
                        <option value="Wine" <?= ($selectedDrinkCategory === 'Wine') ? 'selected' : '' ?>>Wine</option>
                        <option value="Beer" <?= ($selectedDrinkCategory === 'Beer') ? 'selected' : '' ?>>Beer</option>
                        <option value="Spirit" <?= ($selectedDrinkCategory === 'Spirit') ? 'selected' : '' ?>>Spirit</option>
                        <option value="Cocktail" <?= ($selectedDrinkCategory === 'Cocktail') ? 'selected' : '' ?>>Cocktail</option>
                        <option value="Juice" <?= ($selectedDrinkCategory === 'Juice') ? 'selected' : '' ?>>Juice</option>
                        <option value="Soda" <?= ($selectedDrinkCategory === 'Soda') ? 'selected' : '' ?>>Soda</option>
                        <option value="Water" <?= ($selectedDrinkCategory === 'Water') ? 'selected' : '' ?>>Water</option>
                        <option value="softdrink" <?= ($selectedDrinkCategory === 'softdrink') ? 'selected' : '' ?>>Soft Drink</option>
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
                
                <!-- Hidden Category Filter (always 'Drinks' for this page) -->
                <input type="hidden" name="category" value="Drinks"> 
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                    Apply Filters
                </button>
            </div>
        </form>


        <?php if (empty($food_items)): ?>
            <p class="text-center text-gray-600 text-lg">No drink items available matching your filters. Please adjust your criteria.</p>
        <?php else: ?>
            <?php foreach ($categories_grouped as $category_name => $items_in_category): ?>
                <h2 class="text-2xl font-semibold text-gray-700 mt-10 mb-6 border-b-2 border-teal-500 pb-2"><?= htmlspecialchars($category_name) ?></h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($items_in_category as $item): ?>
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                            <img src="<?= htmlspecialchars($item['image_url'] ?? 'https://via.placeholder.com/400x300?text=No+Image') ?>"
                                 alt="<?= htmlspecialchars($item['item_name']) ?>"
                                 class="w-full h-48 object-cover object-center">
                            <div class="p-6">
                                <h3 class="text-xl font-semibold text-gray-800 mb-2"><?= htmlspecialchars($item['item_name']) ?></h3>
                                <p class="text-gray-600 text-sm mb-4"><?= htmlspecialchars($item['description'] ?? 'No description available.') ?></p>
                                <div class="flex items-center justify-between">
                                    <span class="text-2xl font-bold text-teal-600"><?= formatCurrency($item['price']) ?></span>
                                    <button class="add-to-cart-btn bg-teal-600 text-white px-5 py-2 rounded-lg hover:bg-teal-700 transition-colors focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2"
                                            data-item-id="<?= htmlspecialchars($item['menu_item_id']) ?>"
                                            data-item-name="<?= htmlspecialchars($item['item_name']) ?>"
                                            data-item-price="<?= htmlspecialchars($item['price']) ?>">
                                        Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const cartButtons = document.querySelectorAll('.add-to-cart-btn');

        cartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.dataset.itemId;
                const itemName = this.dataset.itemName;
                const itemPrice = this.dataset.itemPrice;

                // Send an AJAX request to add the item to the cart
                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `item_id=${itemId}&item_name=${itemName}&item_price=${itemPrice}&quantity=1`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`${itemName} added to cart!`);
                        // Optionally update a cart icon or counter here
                    } else {
                        alert(`Failed to add ${itemName} to cart: ${data.message}`);
                    }
                })
                .catch(error => {
                    console.error('Error adding item to cart:', error);
                    alert('An error occurred while adding to cart.');
                });
            });
        });
    });
</script>