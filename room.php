<?php
require_once 'config/database.php';
include 'header.php';

$room_type_filter = $_GET['room_type'] ?? 'all';
$min_price_filter = $_GET['min_price'] ?? '';
$max_price_filter = $_GET['max_price'] ?? '';

$sql = "SELECT * FROM rooms WHERE status = 'available'";
$params = [];

if ($room_type_filter !== 'all') {
    $sql .= " AND room_type = :room_type";
    $params[':room_type'] = $room_type_filter;
}

if (!empty($min_price_filter) && is_numeric($min_price_filter)) {
    $sql .= " AND price_per_night >= :min_price";
    $params[':min_price'] = $min_price_filter;
}

if (!empty($max_price_filter) && is_numeric($max_price_filter)) {
    $sql .= " AND price_per_night <= :max_price";
    $params[':max_price'] = $max_price_filter;
}

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rooms = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Error fetching rooms: " . $e->getMessage());
    $rooms = [];
    // Optionally, display an error message to the user
    echo "<p class='text-red-500 text-center mt-4'>Error loading rooms. Please try again later.</p>";
}

// Get unique room types for the filter dropdown
$room_types_sql = "SELECT DISTINCT room_type FROM rooms";
$room_types_stmt = $pdo->query($room_types_sql);
$available_room_types = $room_types_stmt->fetchAll(PDO::FETCH_COLUMN);

?>

<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Our Rooms</h1>

        <!-- Filter Form -->
        <form action="room.php" method="GET" class="bg-white p-6 rounded-lg shadow-md mb-8 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="room_type" class="block text-sm font-medium text-gray-700 mb-1">Room Type</label>
                <select id="room_type" name="room_type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm">
                    <option value="all" <?= $room_type_filter === 'all' ? 'selected' : '' ?>>All Room Types</option>
                    <?php foreach ($available_room_types as $type): ?>
                        <option value="<?= htmlspecialchars($type) ?>" <?= $room_type_filter === $type ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="min_price" class="block text-sm font-medium text-gray-700 mb-1">Min Price (₦)</label>
                <input type="number" id="min_price" name="min_price" value="<?= htmlspecialchars($min_price_filter) ?>"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" placeholder="e.g., 10000">
            </div>
            <div>
                <label for="max_price" class="block text-sm font-medium text-gray-700 mb-1">Max Price (₦)</label>
                <input type="number" id="max_price" name="max_price" value="<?= htmlspecialchars($max_price_filter) ?>"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 sm:text-sm" placeholder="e.g., 50000">
            </div>
            <div>
                <button type="submit" class="w-full bg-teal-600 text-white py-2 px-4 rounded-md hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                    Apply Filters
                </button>
            </div>
        </form>

        <?php if (empty($rooms)): ?>
            <p class="text-center text-gray-600 text-lg">No rooms found matching your criteria.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($rooms as $room): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <img src="<?= htmlspecialchars($room['image_url'] ?? 'https://via.placeholder.com/400x250?text=Room+Image') ?>"
                             alt="Room <?= htmlspecialchars($room['room_number']) ?>"
                             class="w-full h-48 object-cover">
                        <div class="p-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">
                                <?= htmlspecialchars($room['room_type']) ?> - Room <?= htmlspecialchars($room['room_number']) ?>
                            </h2>
                            <p class="text-gray-600 mb-4 text-sm">
                                <?= htmlspecialchars($room['description'] ?? 'No description available.') ?>
                            </p>
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-2xl font-bold text-teal-600">
                                    <?= formatCurrency($room['price_per_night']) ?>
                                    <span class="text-base text-gray-500">/ night</span>
                                </span>
                                <span class="text-gray-500 text-sm">Capacity: <?= htmlspecialchars($room['capacity']) ?></span>
                            </div>
                            <a href="book.php?room_id=<?= htmlspecialchars($room['room_id']) ?>"
                               class="block w-full text-center bg-teal-600 text-white py-2 rounded-md hover:bg-teal-700 transition-colors">
                                Book Now
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>