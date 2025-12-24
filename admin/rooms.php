<?php
require_once '../config/database.php';

// Check if user is logged in and is admin/staff
if (!isLoggedIn() || !isStaff()) {
    redirect('../login.php');
}

// Helper functions for image handling
function uploadRoomImage($file) {
    $uploadDir = '../asset/image/rooms/';
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
    $filename = 'room_' . time() . '_' . uniqid() . '.' . $extension;
    $filepath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return 'asset/image/rooms/' . $filename;
    } else {
        throw new Exception('Failed to upload image.');
    }
}

function deleteRoomImage($imageUrl) {
    if ($imageUrl && file_exists('../' . $imageUrl)) {
        unlink('../' . $imageUrl);
    }
}

// Handle room operations
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_room'])) {
        // Add new room
        $room_number = sanitize($_POST['room_number']);
        $room_type = sanitize($_POST['room_type']);
        $capacity = (int)$_POST['capacity'];
        $price_per_night = (float)$_POST['price_per_night'];
        $description = sanitize($_POST['description']);
        $status = sanitize($_POST['status']);
        $amenities = $_POST['amenities'] ?? '[]';
        $features = $_POST['features'] ?? '[]';

        // Handle image upload
        $image_url = null;
        if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] === UPLOAD_ERR_OK) {
            $image_url = uploadRoomImage($_FILES['room_image']);
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO rooms (room_number, room_type, capacity, price_per_night, description, status, amenities, features, image_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$room_number, $room_type, $capacity, $price_per_night, $description, $status, $amenities, $features, $image_url]);
            $message = 'Room added successfully!';
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = 'Error adding room: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif (isset($_POST['delete_room'])) {
        // Hard delete room
        $room_id = (int)$_POST['room_id'];

        try {
            // First, get the image URL to delete the file
            $stmt = $pdo->prepare("SELECT image_url FROM rooms WHERE room_id = ?");
            $stmt->execute([$room_id]);
            $room = $stmt->fetch();

            if ($room && !empty($room['image_url'])) {
                deleteRoomImage($room['image_url']);
            }

            // Now, delete the room record
            $stmt = $pdo->prepare("DELETE FROM rooms WHERE room_id = ?");
            $stmt->execute([$room_id]);
            
            $message = 'Room deleted successfully!';
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = 'Error deleting room: ' . $e->getMessage();
            $messageType = 'error';
        }
}

// Get all rooms
try {
    $stmt = $pdo->query("SELECT * FROM rooms WHERE status != 'unavailable' ORDER BY room_number ASC");
    $rooms = $stmt->fetchAll();
} catch(PDOException $e) {
    $rooms = [];
    error_log('Error fetching rooms: ' . $e->getMessage());
}

$pageTitle = 'Room Management';
$currentPage = 'rooms';
require_once 'admin-header.php';
?>

<!-- Rooms Management -->
<div id="rooms" class="tab-content p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Room Management</h1>
            <p class="text-gray-600">Manage hotel rooms and their availability</p>
        </div>
        <button onclick="openAddRoomModal()" class="btn-primary outline-none flex items-center gap-2 bg-teal-600 hover:bg-teal-700 p-[12px_20px] rounded-lg text-white font-medium shadow-md hover:shadow-lg transition">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Add New Room
        </button>
    </div>

    <!-- Message Display -->
    <?php if ($message): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Rooms Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <!-- Header -->
    <div class="px-6 py-4 flex items-center justify-between border-b border-gray-200 bg-gray-50">
        <h3 class="text-lg font-semibold text-gray-800">
            All Rooms
            <span class="ml-2 text-sm font-medium text-gray-500">
                (<?php echo count($rooms); ?>)
            </span>
        </h3>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100 text-gray-700 uppercase text-xs tracking-wide">
                <tr>
                    <th class="px-6 py-3 text-left">Image</th>
                    <th class="px-6 py-3 text-left">Room #</th>
                    <th class="px-6 py-3 text-left">Type</th>
                    <th class="px-6 py-3 text-left">Capacity</th>
                    <th class="px-6 py-3 text-left">Price / Night</th>
                    <th class="px-6 py-3 text-left">Status</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200">
            <?php if (empty($rooms)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <svg class="h-14 w-14 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/>
                        </svg>
                        <p class="font-medium">No rooms found</p>
                        <p class="text-sm text-gray-400">Add your first room to get started</p>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($rooms as $room): ?>
                    <tr class="hover:bg-gray-50 transition">
                        <!-- Image -->
                        <td class="px-6 py-4">
                            <?php if ($room['image_url']): ?>
                                <img src="../<?php echo htmlspecialchars($room['image_url']); ?>"
                                     alt="Room <?php echo htmlspecialchars($room['room_number']); ?>"
                                     class="w-20 h-14 rounded-lg object-cover border">
                            <?php else: ?>
                                <div class="w-20 h-14 flex items-center justify-center rounded-lg bg-gray-100 border">
                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16"/>
                                    </svg>
                                </div>
                            <?php endif; ?>
                        </td>

                        <!-- Room Info -->
                        <td class="px-6 py-4 font-semibold text-gray-800">
                            <?php echo htmlspecialchars($room['room_number']); ?>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            <?php echo htmlspecialchars($room['room_type']); ?>
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            <?php echo $room['capacity']; ?> guests
                        </td>
                        <td class="px-6 py-4 font-medium text-gray-800">
                            <?php echo formatCurrency($room['price_per_night']); ?>
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                <?php echo $room['status'] === 'available'
                                    ? 'bg-green-100 text-green-700'
                                    : ($room['status'] === 'occupied'
                                        ? 'bg-red-100 text-red-700'
                                        : 'bg-yellow-100 text-yellow-700'); ?>">
                                <?php echo ucfirst($room['status']); ?>
                            </span>
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="rooms-edit.php?id=<?php echo $room['room_id']; ?>"
                                   class="px-3 py-1.5 text-xs rounded-lg border border-blue-500 text-blue-600
                                          hover:bg-blue-500 hover:text-white transition">
                                    Edit
                                </a>

                                <button
                                    onclick="deleteRoom(<?php echo $room['room_id']; ?>, '<?php echo htmlspecialchars($room['room_number']); ?>')"
                                    class="px-3 py-1.5 text-xs rounded-lg border border-red-500 text-red-600
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

<!-- Add Room Modal -->
<div id="addRoomModal"
     class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-start justify-center overflow-y-auto">

    <div class="bg-white w-full max-w-lg mt-20 rounded-2xl shadow-xl border border-gray-200">
        
        <!-- Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 rounded-t-2xl">
            <h3 class="text-lg font-semibold text-gray-800">Add New Room</h3>
            <button onclick="closeAddRoomModal()"
                    class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Form -->
        <form method="POST" enctype="multipart/form-data" class="p-6 space-y-5">

            <!-- Room Number -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Room Number</label>
                <input type="text" name="room_number" required placeholder="e.g. 101"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2
                              focus:outline-none focus:ring-2 focus:ring-orange-500
                              focus:border-orange-500">
            </div>

            <!-- Room Type -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Room Type</label>
                <select name="room_type" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2
                               focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option>Standard Room</option>
                    <option>Deluxe Room</option>
                    <option>Executive Suite</option>
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                <select name="status" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2
                               focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="available">Available</option>
                    <option value="occupied">Occupied</option>
                    <option value="maintenance">Maintenance</option>
                    <option value="unavailable">Unavailable</option>
                </select>
            </div>

            <!-- Capacity & Price -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Capacity</label>
                    <input type="number" name="capacity" min="1" max="10" required
                           class="w-full rounded-lg border border-gray-300 px-3 py-2
                                  focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Price / Night (₦)
                    </label>
                    <input type="number" name="price_per_night" step="0.01" min="0" required
                           placeholder="25000.00"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2
                                  focus:ring-2 focus:ring-orange-500">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2
                                 focus:ring-2 focus:ring-orange-500"
                          placeholder="Room description..."></textarea>
            </div>

            <!-- Image Upload -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Room Image</label>
                <input type="file" name="room_image" accept="image/*"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2
                              focus:ring-2 focus:ring-orange-500"
                       onchange="previewImage(this, 'image_preview_add')">

                <div id="image_preview_add" class="hidden mt-3">
                    <img id="preview_img_add"
                         class="h-36 w-full object-cover rounded-lg border">
                </div>

                <p class="text-xs text-gray-500 mt-1">
                    JPG or PNG · Max size 5MB
                </p>
            </div>

            <!-- Amenities -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Amenities</label>
                <div class="space-y-2">
                    <div class="flex flex-wrap gap-2" id="amenities_tags_add"></div>
                    <div class="flex gap-2">
                        <select id="amenities_select_add"
                                class="flex-1 rounded-lg border border-gray-300 px-3 py-2
                                       focus:ring-2 focus:ring-orange-500">
                            <option value="">Select amenity...</option>
                            <option>WiFi</option>
                            <option>Air Conditioning</option>
                            <option>TV</option>
                            <option>Mini Bar</option>
                            <option>Balcony</option>
                            <option>Jacuzzi</option>
                        </select>
                        <button type="button" onclick="addAmenity('add')"
                                class="px-4 py-2 rounded-lg border border-orange-500
                                       text-orange-600 text-sm font-medium
                                       hover:bg-orange-500 hover:text-white transition">
                            Add
                        </button>
                    </div>
                </div>
                <input type="hidden" name="amenities" id="amenities_input_add" value="[]">
            </div>

            <!-- Features -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Features</label>
                <div class="space-y-2">
                    <div class="flex flex-wrap gap-2" id="features_tags_add"></div>
                    <div class="flex gap-2">
                        <select id="features_select_add"
                                class="flex-1 rounded-lg border border-gray-300 px-3 py-2
                                       focus:ring-2 focus:ring-orange-500">
                            <option value="">Select feature...</option>
                            <option>King Size Bed</option>
                            <option>Work Desk</option>
                            <option>Sofa</option>
                            <option>Kitchen</option>
                        </select>
                        <button type="button" onclick="addFeature('add')"
                                class="px-4 py-2 rounded-lg border border-orange-500
                                       text-orange-600 text-sm font-medium
                                       hover:bg-orange-500 hover:text-white transition">
                            Add
                        </button>
                    </div>
                </div>
                <input type="hidden" name="features" id="features_input_add" value="[]">
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t">
                <button type="button" onclick="closeAddRoomModal()"
                        class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700
                               hover:bg-gray-100 transition">
                    Cancel
                </button>
                <button type="submit" name="add_room"
                        class="px-6 py-2 rounded-lg bg-orange-500 text-white font-semibold
                               hover:bg-orange-600 transition">
                    Add Room
                </button>
            </div>
        </form>
    </div>
</div>





<script>
function openAddRoomModal() {
    document.getElementById('addRoomModal').classList.remove('hidden');
}

function closeAddRoomModal() {
    document.getElementById('addRoomModal').classList.add('hidden');
}


function deleteRoom(id, number) {
    if (confirm(`Are you sure you want to delete Room ${number}? This action cannot be undone.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="room_id" value="${id}">
            <input type="hidden" name="delete_room" value="1">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}



// Amenities and Features Management Functions
function addAmenity(mode) {
    const selectId = `amenities_select_${mode}`;
    const tagsId = `amenities_tags_${mode}`;
    const select = document.getElementById(selectId);
    const value = select.value;

    if (!value) return;

    const currentAmenities = getCurrentAmenities(mode);
    if (!currentAmenities.includes(value)) {
        currentAmenities.push(value);
        updateAmenitiesDisplay(mode, currentAmenities);
        updateAmenitiesInput(mode, currentAmenities);
    }
    select.value = '';
}

function removeAmenity(mode, amenity) {
    const currentAmenities = getCurrentAmenities(mode);
    const filtered = currentAmenities.filter(a => a !== amenity);
    updateAmenitiesDisplay(mode, filtered);
    updateAmenitiesInput(mode, filtered);
}

function addFeature(mode) {
    const selectId = `features_select_${mode}`;
    const tagsId = `features_tags_${mode}`;
    const select = document.getElementById(selectId);
    const value = select.value;

    if (!value) return;

    const currentFeatures = getCurrentFeatures(mode);
    if (!currentFeatures.includes(value)) {
        currentFeatures.push(value);
        updateFeaturesDisplay(mode, currentFeatures);
        updateFeaturesInput(mode, currentFeatures);
    }
    select.value = '';
}

function removeFeature(mode, feature) {
    const currentFeatures = getCurrentFeatures(mode);
    const filtered = currentFeatures.filter(f => f !== feature);
    updateFeaturesDisplay(mode, filtered);
    updateFeaturesInput(mode, filtered);
}

function getCurrentAmenities(mode) {
    const inputId = `amenities_input_${mode}`;
    const input = document.getElementById(inputId);
    return input.value ? JSON.parse(input.value) : [];
}

function getCurrentFeatures(mode) {
    const inputId = `features_input_${mode}`;
    const input = document.getElementById(inputId);
    return input.value ? JSON.parse(input.value) : [];
}

function updateAmenitiesDisplay(mode, amenities) {
    const tagsId = `amenities_tags_${mode}`;
    const tagsContainer = document.getElementById(tagsId);
    tagsContainer.innerHTML = amenities.map(amenity => `
        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
            ${amenity}
            <button type="button" onclick="removeAmenity('${mode}', '${amenity}')" class="ml-1 text-blue-600 hover:text-blue-800">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </span>
    `).join('');
}

function updateFeaturesDisplay(mode, features) {
    const tagsId = `features_tags_${mode}`;
    const tagsContainer = document.getElementById(tagsId);
    tagsContainer.innerHTML = features.map(feature => `
        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
            ${feature}
            <button type="button" onclick="removeFeature('${mode}', '${feature}')" class="ml-1 text-green-600 hover:text-green-800">
                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </span>
    `).join('');
}

function updateAmenitiesInput(mode, amenities) {
    const inputId = `amenities_input_${mode}`;
    document.getElementById(inputId).value = JSON.stringify(amenities);
}

function updateFeaturesInput(mode, features) {
    const inputId = `features_input_${mode}`;
    document.getElementById(inputId).value = JSON.stringify(features);
}

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