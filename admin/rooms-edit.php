<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Room</h1>
            <p class="text-gray-600">Update the details for Room <?php echo htmlspecialchars($room['room_number']); ?></p>
        </div>
        <a href="rooms.php" class="btn-secondary">
            &larr; Back to All Rooms
        </a>
    </div>

    <?php if ($message): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="bg-white w-full max-w-lg mx-auto rounded-2xl shadow-xl border border-gray-200">

        <!-- Form -->
        <form method="POST" enctype="multipart/form-data" class="p-6 space-y-5">

            <!-- Hidden Fields -->
            <input type="hidden" name="room_id" id="edit_room_id" value="<?php echo $room['room_id']; ?>">
            <input type="hidden" name="existing_image" id="edit_existing_image" value="<?php echo htmlspecialchars($room['image_url']); ?>">

            <!-- Room Number -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Room Number</label>
                <input type="text" name="room_number" id="edit_room_number" required
                       value="<?php echo htmlspecialchars($room['room_number']); ?>"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2
                              focus:outline-none focus:ring-2 focus:ring-orange-500
                              focus:border-orange-500">
            </div>

            <!-- Room Type -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Room Type</label>
                <select name="room_type" id="edit_room_type" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2
                               focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="Standard Room" <?php echo $room['room_type'] === 'Standard Room' ? 'selected' : ''; ?>>Standard Room</option>
                    <option value="Deluxe Room" <?php echo $room['room_type'] === 'Deluxe Room' ? 'selected' : ''; ?>>Deluxe Room</option>
                    <option value="Executive Suite" <?php echo $room['room_type'] === 'Executive Suite' ? 'selected' : ''; ?>>Executive Suite</option>
                </select>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                <select name="status" id="edit_status" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2
                               focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="available" <?php echo $room['status'] === 'available' ? 'selected' : ''; ?>>Available</option>
                    <option value="occupied" <?php echo $room['status'] === 'occupied' ? 'selected' : ''; ?>>Occupied</option>
                    <option value="maintenance" <?php echo $room['status'] === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                    <option value="unavailable" <?php echo $room['status'] === 'unavailable' ? 'selected' : ''; ?>>Unavailable</option>
                </select>
            </div>

            <!-- Capacity & Price -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Capacity</label>
                    <input type="number" name="capacity" id="edit_capacity" min="1" max="10" required
                           value="<?php echo $room['capacity']; ?>"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2
                                  focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Price / Night (₦)
                    </label>
                    <input type="number" name="price_per_night" id="edit_price_per_night"
                           step="0.01" min="0" required
                           value="<?php echo $room['price_per_night']; ?>"
                           class="w-full rounded-lg border border-gray-300 px-3 py-2
                                  focus:ring-2 focus:ring-orange-500">
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                <textarea name="description" id="edit_description" rows="3"
                          class="w-full rounded-lg border border-gray-300 px-3 py-2
                                 focus:ring-2 focus:ring-orange-500"><?php echo htmlspecialchars($room['description']); ?></textarea>
            </div>

            <!-- Image -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Room Image</label>

                <!-- Current Image -->
                <div id="current_image_container"
                     class="<?php echo $room['image_url'] ? '' : 'hidden'; ?> mb-3 p-2 rounded-lg border bg-gray-50">
                    <p class="text-xs text-gray-600 mb-1">Current Image</p>
                    <img id="current_image" src="../<?php echo htmlspecialchars($room['image_url']); ?>"
                         class="h-36 w-full object-cover rounded-lg border">
                </div>

                <!-- Upload -->
                <input type="file" name="room_image" accept="image/*"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2
                              focus:ring-2 focus:ring-orange-500"
                       onchange="previewImage(this, 'image_preview_edit')">

                <!-- Preview -->
                <div id="image_preview_edit" class="hidden mt-3">
                    <p class="text-xs text-gray-600 mb-1">New Image Preview</p>
                    <img id="preview_img_edit"
                         class="h-36 w-full object-cover rounded-lg border">
                </div>

                <p class="text-xs text-gray-500 mt-1">
                    Upload a new image to replace the current one (JPG, PNG · max 5MB)
                </p>
            </div>

            <!-- Amenities -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Amenities</label>
                <div class="space-y-2">
                    <div class="flex flex-wrap gap-2" id="amenities_tags_edit"></div>
                    <div class="flex gap-2">
                        <select id="amenities_select_edit"
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
                        <button type="button" onclick="addAmenity('edit')"
                                class="px-4 py-2 rounded-lg border border-orange-500
                                       text-orange-600 text-sm font-medium
                                       hover:bg-orange-500 hover:text-white transition">
                            Add
                        </button>
                    </div>
                </div>
                <input type="hidden" name="amenities" id="amenities_input_edit" value='<?php echo htmlspecialchars($room['amenities'] ?? '[]'); ?>'>
            </div>

            <!-- Features -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Features</label>
                <div class="space-y-2">
                    <div class="flex flex-wrap gap-2" id="features_tags_edit"></div>
                    <div class="flex gap-2">
                        <select id="features_select_edit"
                                class="flex-1 rounded-lg border border-gray-300 px-3 py-2
                                       focus:ring-2 focus:ring-orange-500">
                            <option value="">Select feature...</option>
                            <option>King Size Bed</option>
                            <option>Work Desk</option>
                            <option>Sofa</option>
                            <option>Kitchen</option>
                        </select>
                        <button type="button" onclick="addFeature('edit')"
                                class="px-4 py-2 rounded-lg border border-orange-500
                                       text-orange-600 text-sm font-medium
                                       hover:bg-orange-500 hover:text-white transition">
                            Add
                        </button>
                    </div>
                </div>
                <input type="hidden" name="features" id="features_input_edit" value='<?php echo htmlspecialchars($room['features'] ?? '[]'); ?>'>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="rooms.php"
                        class="px-5 py-2 rounded-lg border border-gray-300 text-gray-700
                               hover:bg-gray-100 transition">
                    Cancel
                </a>
                <button type="submit" name="update_room"
                        class="px-6 py-2 rounded-lg bg-orange-500 text-white font-semibold
                               hover:bg-orange-600 transition">
                    Update Room
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize amenities
    let amenitiesArray = [];
    try {
        const amenitiesValue = document.getElementById('amenities_input_edit').value;
        const parsedAmenities = JSON.parse(amenitiesValue || '[]');
        if (Array.isArray(parsedAmenities)) {
            if (parsedAmenities.length > 0 && typeof parsedAmenities[0] === 'string' && parsedAmenities[0].startsWith('[')) {
                amenitiesArray = JSON.parse(parsedAmenities[0]);
            } else {
                amenitiesArray = parsedAmenities;
            }
        }
    } catch (e) {
        // Did not parse, so probably not a JSON string.
    }
    updateAmenitiesDisplay('edit', amenitiesArray);
    updateAmenitiesInput('edit', amenitiesArray);
    
    // Initialize features
    let featuresArray = [];
    try {
        const featuresValue = document.getElementById('features_input_edit').value;
        const parsedFeatures = JSON.parse(featuresValue || '[]');
        if (Array.isArray(parsedFeatures)) {
            if (parsedFeatures.length > 0 && typeof parsedFeatures[0] === 'string' && parsedFeatures[0].startsWith('[')) {
                featuresArray = JSON.parse(parsedFeatures[0]);
            } else {
                featuresArray = parsedFeatures;
            }
        }
    } catch (e) {
        // Did not parse, so probably not a JSON string.
    }
    updateFeaturesDisplay('edit', featuresArray);
    updateFeaturesInput('edit', featuresArray);
});


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
    try {
        return input.value ? JSON.parse(input.value) : [];
    } catch (e) {
        // Handle cases where the value is not valid JSON
        return [];
    }
}

function getCurrentFeatures(mode) {
    const inputId = `features_input_${mode}`;
    const input = document.getElementById(inputId);
    try {
        return input.value ? JSON.parse(input.value) : [];
    } catch(e) {
        return [];
    }
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