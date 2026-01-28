<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';
include 'header-one.php';

// Get selected category from URL
$selectedDrinkCategory = isset($_GET['drink_category']) ? $_GET['drink_category'] : 'All';

// Base query for drinks
$sql = "SELECT * FROM food_menu WHERE availability = 1 AND category = 'Drinks'";
$params = [];

$pageTitle = "Our Drinks Menu";

if ($selectedDrinkCategory && $selectedDrinkCategory !== 'All') {
    $sql .= " AND drink_category = :drink_category";
    $params[':drink_category'] = $selectedDrinkCategory;
    $pageTitle = htmlspecialchars($selectedDrinkCategory) . " Menu";
}

$sql .= " ORDER BY item_name";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $drinks = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching food menu: " . $e->getMessage());
    $drinks = [];
}
?>

<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-800"><?php echo $pageTitle; ?></h1>
            <p class="text-lg text-gray-600 mt-2">Find your perfect refreshment.</p>
        </div>
        
        <div class="text-center mb-8">
            <a href="index.php" class="text-black hover:text-teal-800 font-medium">← Back to Drink Categories</a>
        </div>

        <?php if (empty($drinks)): ?>
            <p class="text-center text-gray-600 text-lg py-12">No drinks available in this category at the moment. Please check back soon!</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach ($drinks as $item): ?>
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden group">
                        <div class="relative h-56 overflow-hidden">
                            <img src="<?= htmlspecialchars($item['image_url'] ?? 'https://via.placeholder.com/400x300?text=No+Image') ?>"
                                 alt="<?= htmlspecialchars($item['item_name']) ?>"
                                 class="w-full h-full group-hover:scale-110 transition-transform duration-300">
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-800 mb-2"><?= htmlspecialchars($item['item_name']) ?></h3>
                            <p class="text-gray-600 text-sm mb-4 h-12 overflow-hidden"><?= htmlspecialchars($item['description'] ?? 'No description available.') ?></p>
                            <div class="flex items-center justify-between">
                                <span class="text-2xl font-bold text-black">N<?= number_format($item['price'], 2) ?></span>
                                <form class="add-to-cart-form">
                                    <input type="hidden" name="item_id" value="<?= $item['menu_item_id'] ?>">
                                    <input type="hidden" name="item_name" value="<?= htmlspecialchars($item['item_name']) ?>">
                                    <input type="hidden" name="item_price" value="<?= $item['price'] ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="bg-black text-white px-5 py-2 rounded-lg hover:bg-teal-700 transition-colors focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                                        Add to Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer-one.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('.add-to-cart-form');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const itemName = formData.get('item_name');
                if (data.success) {
                    alert(`${itemName} has been added to your cart!`);
                    // Here you might want to update a cart counter in the header
                } else {
                    alert(`Error: ${data.message}`);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while adding the item to the cart.');
            });
        });
    });
});
</script>