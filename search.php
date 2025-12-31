<?php
require_once 'config/database.php';
require_once 'header.php';

$search_query = isset($_GET['q']) ? sanitize($_GET['q']) : '';

$rooms = [];
$food_items = [];

if (!empty($search_query)) {
    // Search rooms
    $stmt_rooms = $pdo->prepare("SELECT * FROM rooms WHERE room_type LIKE :query OR description LIKE :query OR room_number LIKE :query");
    $stmt_rooms->execute(['query' => "%$search_query%"]);
    $rooms = $stmt_rooms->fetchAll();

    // Search food and drinks
    $stmt_food = $pdo->prepare("SELECT * FROM food_menu WHERE item_name LIKE :query OR description LIKE :query OR category LIKE :query");
    $stmt_food->execute(['query' => "%$search_query%"]);
    $food_items = $stmt_food->fetchAll();
}
?>

<main class="container mx-auto px-4 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Search Results for "<?= htmlspecialchars($search_query) ?>"</h1>

    <?php if (empty($search_query)): ?>
        <div class="text-center text-gray-500">
            Please enter a search term.
        </div>
    <?php elseif (empty($rooms) && empty($food_items)): ?>
        <div class="text-center text-gray-500">
            No results found.
        </div>
    <?php else: ?>
        <!-- Room Results -->
        <?php if (!empty($rooms)): ?>
            <section class="mb-12">
                <h2 class="text-2xl font-bold text-gray-700 mb-4">Rooms</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($rooms as $room): ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <a href="room.php#room-<?= $room['room_id'] ?>">
                                <img src="<?= htmlspecialchars($room['image_url']) ?>" alt="<?= htmlspecialchars($room['room_type']) ?>" class="h-48 w-full object-cover">
                                <div class="p-4">
                                    <h3 class="font-bold text-lg text-gray-800"><?= htmlspecialchars($room['room_type']) ?></h3>
                                    <p class="text-gray-600 text-sm mb-2">Room <?= htmlspecialchars($room['room_number']) ?></p>
                                    <p class="text-gray-600 text-sm mb-4"><?= htmlspecialchars(substr($room['description'], 0, 100)) ?>...</p>
                                    <div class="text-lg font-bold text-teal-600"><?= formatCurrency($room['price_per_night']) ?> / night</div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Food & Drink Results -->
        <?php if (!empty($food_items)): ?>
            <section>
                <h2 class="text-2xl font-bold text-gray-700 mb-4">Food & Drinks</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ($food_items as $item): ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden">
                            <a href="<?= strpos(strtolower($item['category']), 'drink') !== false ? 'drink.php' : 'food.php' ?>#item-<?= $item['menu_item_id'] ?>">
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['item_name']) ?>" class="h-40 w-full object-cover">
                                <div class="p-4">
                                    <h3 class="font-bold text-md text-gray-800"><?= htmlspecialchars($item['item_name']) ?></h3>
                                    <p class="text-gray-600 text-sm mb-2"><?= htmlspecialchars($item['category']) ?></p>
                                    <div class="text-md font-bold text-teal-600"><?= formatCurrency($item['price']) ?></div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php require_once 'footer.php'; ?>
