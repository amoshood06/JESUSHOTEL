<?php
require_once '../config/database.php';

// Check if user is logged in and is admin/staff
if (!isLoggedIn() || !isStaff()) {
    redirect('../login.php');
}

// Helper functions for image handling
function uploadFoodImage($file) {
    $uploadDir = '../asset/image/food/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, PNG, and WebP are allowed.');
    }

    if ($file['size'] > $maxSize) {
        throw new Exception('File size too large. Maximum size is 5MB.');
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'food_' . time() . '_' . uniqid() . '.' . $extension;
    $filepath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return 'asset/image/food/' . $filename;
    } else {
        throw new Exception('Failed to upload image.');
    }
}

function deleteFoodImage($imageUrl) {
    if ($imageUrl && file_exists('../' . $imageUrl)) {
        unlink('../' . $imageUrl);
    }
}

// Handle food menu operations
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_item'])) {
        // Add new menu item
        $item_name = sanitize($_POST['item_name']);
        $category = sanitize($_POST['category']);
        $description = sanitize($_POST['description']);
        $price = (float)$_POST['price'];
        $availability = isset($_POST['availability']) ? 1 : 0;
        $preparation_time = (int)$_POST['preparation_time'];
        $is_vegetarian = isset($_POST['is_vegetarian']) ? 1 : 0;
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        
        $image_url = null;
        if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
            try {
                $image_url = uploadFoodImage($_FILES['item_image']);
            } catch (Exception $e) {
                $message = 'Image upload failed: ' . $e->getMessage();
                $messageType = 'error';
                // Proceed without image or return if image is mandatory
            }
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO food_menu (item_name, category, description, price, availability, preparation_time, is_vegetarian, is_featured, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$item_name, $category, $description, $price, $availability, $preparation_time, $is_vegetarian, $is_featured, $image_url]);
            $message = 'Menu item added successfully!';
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = 'Error adding menu item: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif (isset($_POST['update_item'])) {
        // Update menu item
        $menu_item_id = (int)$_POST['menu_item_id'];
        $item_name = sanitize($_POST['item_name']);
        $category = sanitize($_POST['category']);
        $description = sanitize($_POST['description']);
        $price = (float)$_POST['price'];
        $availability = isset($_POST['availability']) ? 1 : 0;
        $preparation_time = (int)$_POST['preparation_time'];
        $is_vegetarian = isset($_POST['is_vegetarian']) ? 1 : 0;
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;

        $current_image_url = sanitize($_POST['existing_image'] ?? null);
        $image_url = $current_image_url; // Default to existing image

        if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
            try {
                $new_image_url = uploadFoodImage($_FILES['item_image']);
                if ($new_image_url) {
                    deleteFoodImage($current_image_url); // Delete old image
                    $image_url = $new_image_url;
                }
            } catch (Exception $e) {
                $message = 'Image upload failed: ' . $e->getMessage();
                $messageType = 'error';
            }
        }

        try {
            $stmt = $pdo->prepare("UPDATE food_menu SET item_name = ?, category = ?, description = ?, price = ?, availability = ?, preparation_time = ?, is_vegetarian = ?, is_featured = ?, image_url = ? WHERE menu_item_id = ?");
            $stmt->execute([$item_name, $category, $description, $price, $availability, $preparation_time, $is_vegetarian, $is_featured, $image_url, $menu_item_id]);
            $message = 'Menu item updated successfully!';
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = 'Error updating menu item: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif (isset($_POST['delete_item'])) {
        // Delete menu item
        $menu_item_id = (int)$_POST['menu_item_id'];

        // Get image URL before deleting item
        $stmt = $pdo->prepare("SELECT image_url FROM food_menu WHERE menu_item_id = ?");
        $stmt->execute([$menu_item_id]);
        $item = $stmt->fetch();
        $image_to_delete = $item['image_url'] ?? null;

        try {
            $stmt = $pdo->prepare("DELETE FROM food_menu WHERE menu_item_id = ?");
            $stmt->execute([$menu_item_id]);
            deleteFoodImage($image_to_delete); // Delete associated image file
            $message = 'Menu item deleted successfully!';
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = 'Error deleting menu item: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Get filter parameters
$category_filter = $_GET['category'] ?? '';
$availability_filter = $_GET['availability'] ?? '';

// Build query with filters
$query = "SELECT * FROM food_menu";
$conditions = [];
$params = [];

if ($category_filter && $category_filter !== 'all') {
    $conditions[] = "category = ?";
    $params[] = $category_filter;
}

if ($availability_filter !== '') {
    $conditions[] = "availability = ?";
    $params[] = (int)$availability_filter;
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY category ASC, item_name ASC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $menu_items = $stmt->fetchAll();
} catch(PDOException $e) {
    $menu_items = [];
    error_log('Error fetching menu items: ' . $e->getMessage());
}

// Get menu statistics
try {
    $stmt = $pdo->query("SELECT
        COUNT(*) as total,
        SUM(CASE WHEN availability = 1 THEN 1 ELSE 0 END) as available,
        SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured,
        SUM(CASE WHEN is_vegetarian = 1 THEN 1 ELSE 0 END) as vegetarian
        FROM food_menu");
    $stats = $stmt->fetch();
} catch(PDOException $e) {
    $stats = ['total' => 0, 'available' => 0, 'featured' => 0, 'vegetarian' => 0];
}

$pageTitle = 'Food Menu Management';
$currentPage = 'food';
require_once 'admin-header.php';
?>

<!-- Food Menu Management -->
<div id="food" class="tab-content p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Food Menu Management</h1>
            <p class="text-gray-600">Manage restaurant menu items and pricing</p>
        </div>
        <button onclick="openAddItemModal()" class="btn-primary outline-none flex items-center gap-2 bg-teal-600 hover:bg-teal-700 p-[12px_20px] rounded-lg text-white font-medium shadow-md hover:shadow-lg transition">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Add Menu Item
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-gray-900"><?php echo $stats['total']; ?></div>
            <div class="text-sm text-gray-600">Total Items</div>
        </div>
        <div class="bg-green-50 rounded-lg shadow p-4 text-center border-l-4 border-green-400">
            <div class="text-2xl font-bold text-green-700"><?php echo $stats['available']; ?></div>
            <div class="text-sm text-green-600">Available</div>
        </div>
        <div class="bg-purple-50 rounded-lg shadow p-4 text-center border-l-4 border-purple-400">
            <div class="text-2xl font-bold text-purple-700"><?php echo $stats['featured']; ?></div>
            <div class="text-sm text-purple-600">Featured</div>
        </div>
        <div class="bg-blue-50 rounded-lg shadow p-4 text-center border-l-4 border-blue-400">
            <div class="text-2xl font-bold text-blue-700"><?php echo $stats['vegetarian']; ?></div>
            <div class="text-sm text-blue-600">Vegetarian</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-wrap gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select id="categoryFilter" class="form-select">
                    <option value="all" <?php echo $category_filter === '' || $category_filter === 'all' ? 'selected' : ''; ?>>All Categories</option>
                    <option value="Nigerian Dishes" <?php echo $category_filter === 'Nigerian Dishes' ? 'selected' : ''; ?>>Nigerian Dishes</option>
                    <option value="Continental Plates" <?php echo $category_filter === 'Continental Plates' ? 'selected' : ''; ?>>Continental Plates</option>
                    <option value="Breakfast Specials" <?php echo $category_filter === 'Breakfast Specials' ? 'selected' : ''; ?>>Breakfast Specials</option>
                    <option value="Drinks & Beverages" <?php echo $category_filter === 'Drinks & Beverages' ? 'selected' : ''; ?>>Drinks & Beverages</option>
                    <option value="Appetizers" <?php echo $category_filter === 'Appetizers' ? 'selected' : ''; ?>>Appetizers</option>
                    <option value="Desserts" <?php echo $category_filter === 'Desserts' ? 'selected' : ''; ?>>Desserts</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Availability</label>
                <select id="availabilityFilter" class="form-select">
                    <option value="" <?php echo $availability_filter === '' ? 'selected' : ''; ?>>All Items</option>
                    <option value="1" <?php echo $availability_filter === '1' ? 'selected' : ''; ?>>Available</option>
                    <option value="0" <?php echo $availability_filter === '0' ? 'selected' : ''; ?>>Unavailable</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Message Display -->
    <?php if ($message): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Menu Items Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

    <!-- Header -->
    <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-800">
            Menu Items
            <span class="ml-2 text-sm font-medium text-gray-500">
                (<?php echo count($menu_items); ?>)
            </span>
        </h3>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wide">
                <tr>
                    <th class="px-6 py-3 text-left">Image</th>
                    <th class="px-6 py-3 text-left">Item</th>
                    <th class="px-6 py-3 text-left">Category</th>
                    <th class="px-6 py-3 text-left">Price</th>
                    <th class="px-6 py-3 text-left">Prep Time</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Features</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
            <?php if (empty($menu_items)): ?>
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                        <svg class="h-14 w-14 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <p class="font-medium">No menu items found</p>
                        <p class="text-sm text-gray-400">
                            Add your first menu item to get started
                        </p>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($menu_items as $item): ?>
                    <tr class="hover:bg-gray-50 transition">

                        <!-- Image -->
                        <td class="px-6 py-4 w-24">
                            <?php if ($item['image_url']): ?>
                                <img src="../<?php echo htmlspecialchars($item['image_url']); ?>"
                                     alt="<?php echo htmlspecialchars($item['item_name']); ?>"
                                     class="w-20 h-14 rounded-lg object-cover border">
                            <?php else: ?>
                                <div class="w-20 h-14 rounded-lg border bg-gray-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </td>

                        <!-- Item Name -->
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-800">
                                <?php echo htmlspecialchars($item['item_name']); ?>
                            </div>
                            <?php if ($item['description']): ?>
                                <div class="text-xs text-gray-500 mt-1">
                                    <?php echo htmlspecialchars(substr($item['description'], 0, 60)) . (strlen($item['description']) > 60 ? '…' : ''); ?>
                                </div>
                            <?php endif; ?>
                        </td>

                        <!-- Category -->
                        <td class="px-6 py-4 text-gray-600">
                            <?php echo htmlspecialchars($item['category']); ?>
                        </td>

                        <!-- Price -->
                        <td class="px-6 py-4 font-semibold text-gray-800">
                            <?php echo formatCurrency($item['price']); ?>
                        </td>

                        <!-- Prep Time -->
                        <td class="px-6 py-4 text-gray-600">
                            <?php echo $item['preparation_time']; ?> min
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                <?php echo $item['availability']
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700'; ?>">
                                <?php echo $item['availability'] ? 'Available' : 'Unavailable'; ?>
                            </span>
                        </td>

                        <!-- Features -->
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap gap-1">
                                <?php if ($item['is_featured']): ?>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                        Featured
                                    </span>
                                <?php endif; ?>
                                <?php if ($item['is_vegetarian']): ?>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        Vegetarian
                                    </span>
                                <?php endif; ?>
                            </div>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <button
                                    onclick="editItem(<?php echo htmlspecialchars(json_encode($item)); ?>)"
                                    class="px-3 py-1.5 rounded-lg border border-blue-500
                                           text-blue-600 text-xs font-medium
                                           hover:bg-blue-500 hover:text-white transition">
                                    Edit
                                </button>

                                <button
                                    onclick="deleteItem(<?php echo $item['menu_item_id']; ?>, '<?php echo htmlspecialchars($item['item_name']); ?>')"
                                    class="px-3 py-1.5 rounded-lg border border-red-500
                                           text-red-600 text-xs font-medium
                                           hover:bg-red-500 hover:text-white transition">
                                    Delete
                                </button>
                            </div>
                        </td>

                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</div>

<!-- Add Item Modal -->
<div id="addItemModal"
     class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm hidden z-50 flex items-start justify-center overflow-y-auto">

    <div class="relative mt-20 w-full max-w-lg bg-white rounded-xl shadow-xl overflow-hidden">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">
                ➕ Add Menu Item
            </h3>
            <button onclick="closeAddItemModal()"
                    class="text-gray-400 hover:text-gray-600 transition">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Body -->
        <form method="POST" enctype="multipart/form-data" class="p-6 space-y-5">

            <!-- Item Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Item Name
                </label>
                <input type="text" name="item_name" required
                       class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500"
                       placeholder="e.g. Jollof Rice">
            </div>

            <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category" required class="form-select">
                        <option value="Nigerian Dishes">Nigerian Dishes</option>
                        <option value="Continental Plates">Continental Plates</option>
                        <option value="Breakfast Specials">Breakfast Specials</option>
                        <option value="Drinks">Drinks & Beverages</option>
                        <option value="Appetizers">Appetizers</option>
                        <option value="Desserts">Desserts</option>
                    </select>
                </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>
                <textarea name="description" rows="3"
                          class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500"
                          placeholder="Describe the dish..."></textarea>
            </div>

            <!-- Price & Prep Time -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Price (₦)
                    </label>
                    <input type="number" name="price" required step="0.01" min="0"
                           class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500"
                           placeholder="2500.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Prep Time (min)
                    </label>
                    <input type="number" name="preparation_time" required min="1"
                           class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500"
                           placeholder="30">
                </div>
            </div>

            <!-- Image Upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Item Image
                </label>
                <input type="file" name="item_image" accept="image/*"
                       class="w-full text-sm border-gray-300 rounded-lg cursor-pointer
                              focus:outline-none focus:ring-2 focus:ring-teal-500"
                       onchange="previewImage(this, 'image_preview_add')">

                <div id="image_preview_add"
                     class="hidden mt-3 border rounded-lg overflow-hidden">
                    <img id="preview_img_add"
                         class="h-36 w-full object-cover">
                </div>

                <p class="text-xs text-gray-500 mt-1">
                    JPG or PNG · Max size 5MB
                </p>
            </div>

            <!-- Options -->
            <div class="space-y-3 pt-2">
                <label class="flex items-center gap-2 text-sm text-gray-800">
                    <input type="checkbox" name="availability" checked
                           class="h-4 w-4 text-teal-600 focus:ring-teal-500 rounded">
                    Available
                </label>

                <label class="flex items-center gap-2 text-sm text-gray-800">
                    <input type="checkbox" name="is_vegetarian"
                           class="h-4 w-4 text-teal-600 focus:ring-teal-500 rounded">
                    Vegetarian
                </label>

                <label class="flex items-center gap-2 text-sm text-gray-800">
                    <input type="checkbox" name="is_featured"
                           class="h-4 w-4 text-teal-600 focus:ring-teal-500 rounded">
                    Featured Item
                </label>
            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-3 pt-6 border-t">
                <button type="button" onclick="closeAddItemModal()"
                        class="px-4 py-2 rounded-lg border text-gray-700 hover:bg-gray-100 transition">
                    Cancel
                </button>
                <button type="submit" name="add_item"
                        class="px-5 py-2 rounded-lg bg-teal-600 text-white hover:bg-teal-700 transition">
                    Add Item
                </button>
            </div>

        </form>
    </div>
</div>


<!-- Edit Item Modal -->
<div id="editItemModal"
     class="fixed inset-0 bg-black bg-opacity-40 backdrop-blur-sm hidden z-50 flex items-start justify-center overflow-y-auto">

    <div class="relative mt-20 w-full max-w-lg bg-white rounded-xl shadow-xl overflow-hidden">

        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900">
                ✏️ Edit Menu Item
            </h3>
            <button onclick="closeEditItemModal()"
                    class="text-gray-400 hover:text-gray-600 transition">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Body -->
        <form method="POST" enctype="multipart/form-data" class="p-6 space-y-5">

            <!-- Hidden Inputs -->
            <input type="hidden" name="menu_item_id" id="edit_menu_item_id">
            <input type="hidden" name="existing_image" id="edit_existing_image">

            <!-- Item Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Item Name
                </label>
                <input type="text" name="item_name" id="edit_item_name" required
                       class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500">
            </div>

            <!-- Category -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category" id="edit_category" required class="form-select">
                        <option value="Nigerian Dishes">Nigerian Dishes</option>
                        <option value="Continental Plates">Continental Plates</option>
                        <option value="Breakfast Specials">Breakfast Specials</option>
                        <option value="Drinks">Drinks & Beverages</option>
                        <option value="Appetizers">Appetizers</option>
                        <option value="Desserts">Desserts</option>
                    </select>
                </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>
                <textarea name="description" id="edit_description" rows="3"
                          class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500"></textarea>
            </div>

            <!-- Price & Prep Time -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Price (₦)
                    </label>
                    <input type="number" name="price" id="edit_price" required step="0.01" min="0"
                           class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Prep Time (min)
                    </label>
                    <input type="number" name="preparation_time" id="edit_preparation_time" required min="1"
                           class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500">
                </div>
            </div>

            <!-- Current Image -->
            <div id="current_image_container_edit" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Current Image
                </label>
                <img id="current_image_edit"
                     class="h-36 w-full object-cover rounded-lg border border-gray-200">
            </div>

            <!-- New Image Upload -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    New Image (optional)
                </label>
                <input type="file" name="item_image" accept="image/*"
                       class="w-full text-sm border-gray-300 rounded-lg cursor-pointer
                              focus:outline-none focus:ring-2 focus:ring-teal-500"
                       onchange="previewImage(this, 'image_preview_edit')">

                <div id="image_preview_edit"
                     class="hidden mt-3 border rounded-lg overflow-hidden">
                    <img id="preview_img_edit"
                         class="h-36 w-full object-cover">
                </div>

                <p class="text-xs text-gray-500 mt-1">
                    Upload a new image to replace the current one (JPG or PNG · Max 5MB)
                </p>
            </div>

            <!-- Options -->
            <div class="space-y-3 pt-2">
                <label class="flex items-center gap-2 text-sm text-gray-800">
                    <input type="checkbox" name="availability" id="edit_availability"
                           class="h-4 w-4 text-teal-600 focus:ring-teal-500 rounded">
                    Available
                </label>

                <label class="flex items-center gap-2 text-sm text-gray-800">
                    <input type="checkbox" name="is_vegetarian" id="edit_vegetarian"
                           class="h-4 w-4 text-teal-600 focus:ring-teal-500 rounded">
                    Vegetarian
                </label>

                <label class="flex items-center gap-2 text-sm text-gray-800">
                    <input type="checkbox" name="is_featured" id="edit_featured"
                           class="h-4 w-4 text-teal-600 focus:ring-teal-500 rounded">
                    Featured Item
                </label>
            </div>

            <!-- Footer -->
            <div class="flex justify-end gap-3 pt-6 border-t">
                <button type="button" onclick="closeEditItemModal()"
                        class="px-4 py-2 rounded-lg border text-gray-700 hover:bg-gray-100 transition">
                    Cancel
                </button>
                <button type="submit" name="update_item"
                        class="px-5 py-2 rounded-lg bg-teal-600 text-white hover:bg-teal-700 transition">
                    Update Item
                </button>
            </div>

        </form>
    </div>
</div>


<script>
function openAddItemModal() {
    document.getElementById('addItemModal').classList.remove('hidden');
}

function closeAddItemModal() {
    document.getElementById('addItemModal').classList.add('hidden');
}

function editItem(item) {
    document.getElementById('edit_menu_item_id').value = item.menu_item_id;
    document.getElementById('edit_item_name').value = item.item_name;
    // Category handling - explicitly set selected option
    const categorySelect = document.getElementById('edit_category');
    for (let i = 0; i < categorySelect.options.length; i++) {
        if (categorySelect.options[i].value === item.category) {
            categorySelect.options[i].selected = true;
            break;
        }
    }
    document.getElementById('edit_description').value = item.description || '';
    document.getElementById('edit_price').value = item.price;
    document.getElementById('edit_preparation_time').value = item.preparation_time;
    document.getElementById('edit_availability').checked = item.availability == 1;
    document.getElementById('edit_vegetarian').checked = item.is_vegetarian == 1;
    document.getElementById('edit_featured').checked = item.is_featured == 1;
    
    // Image handling
    document.getElementById('edit_existing_image').value = item.image_url || '';
    const currentImageContainer = document.getElementById('current_image_container_edit');
    const currentImage = document.getElementById('current_image_edit');
    if (item.image_url) {
        currentImage.src = '../' + item.image_url; // Adjust path if necessary
        currentImageContainer.classList.remove('hidden');
    } else {
        currentImageContainer.classList.add('hidden');
    }
    // Clear new image preview
    document.getElementById('image_preview_edit').classList.add('hidden');
    document.getElementById('preview_img_edit').src = '';

    document.getElementById('editItemModal').classList.remove('hidden');
}

function closeEditItemModal() {
    document.getElementById('editItemModal').classList.add('hidden');
}

function deleteItem(id, name) {
    if (confirm(`Are you sure you want to delete "${name}"? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="menu_item_id" value="${id}">
            <input type="hidden" name="delete_item" value="1">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Filter functionality
document.getElementById('categoryFilter').addEventListener('change', function() {
    const category = this.value;
    const availability = document.getElementById('availabilityFilter').value;
    const url = new URL(window.location);
    if (category === 'all') {
        url.searchParams.delete('category');
    } else {
        url.searchParams.set('category', category);
    }
    if (availability !== '') {
        url.searchParams.set('availability', availability);
    } else {
        url.searchParams.delete('availability');
    }
    window.location.href = url.toString();
});

document.getElementById('availabilityFilter').addEventListener('change', function() {
    const availability = this.value;
    const category = document.getElementById('categoryFilter').value;
    const url = new URL(window.location);
    if (category !== 'all') {
        url.searchParams.set('category', category);
    } else {
        url.searchParams.delete('category');
    }
    if (availability !== '') {
        url.searchParams.set('availability', availability);
    } else {
        url.searchParams.delete('availability');
    }
    window.location.href = url.toString();
});

// Image preview function
function previewImage(input, previewContainerId) {
    const previewContainer = document.getElementById(previewContainerId);
    const previewImg = document.getElementById('preview_img_' + previewContainerId.split('_')[2]);

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewContainer.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.classList.add('hidden');
    }
}
</script>

<?php require_once 'admin-footer.php'; ?>