<?php
require_once 'config/database.php';
include 'header.php';

// Fetch available rooms for display
try {
    $stmt = $pdo->query("SELECT * FROM rooms WHERE status = 'available' ORDER BY room_type ASC LIMIT 5");
    $displayRooms = $stmt->fetchAll();
} catch(PDOException $e) {
    $displayRooms = [];
    error_log('Error fetching display rooms: ' . $e->getMessage());
}

// Fetch featured food items for display
try {
    $stmt = $pdo->query("SELECT * FROM food_menu WHERE is_featured = 1 AND availability = 1 ORDER BY last_updated DESC");
    $featuredFood = $stmt->fetchAll();
} catch(PDOException $e) {
    $featuredFood = [];
    error_log('Error fetching featured food: ' . $e->getMessage());
}

// Fetch drinks for display
try {
    $stmt = $pdo->query("SELECT * FROM food_menu WHERE category = 'Drinks' AND availability = 1 ORDER BY last_updated DESC LIMIT 6");
    $drinks = $stmt->fetchAll();
} catch(PDOException $e) {
    $drinks = [];
    error_log('Error fetching drinks: ' . $e->getMessage());
}
?>

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-teal-50 to-amber-50">
        <div class="container mx-auto px-4 lg:px-8 py-12 md:py-16">
            <div id="hero-slider" class="bg-white/50 backdrop-blur-sm rounded-2xl overflow-hidden border border-teal-100 relative">
                <!-- Slides -->
                <div class="slide active">
                    <div class="grid md:grid-cols-2 gap-8 items-center p-8 md:p-12">
                        <div class="space-y-6">
                            <span class="inline-block bg-teal-600 text-white px-4 py-1 rounded-full text-sm font-semibold">Limited Time Offer</span>
                            <h1 class="text-4xl md:text-5xl font-bold leading-tight text-gray-900">
                                Your Perfect Stay Awaits at <span class="text-teal-600">AVILLA OKADA HOTEL</span>
                            </h1>
                            <p class="text-xl text-gray-600">UP to 30% OFF on weekend bookings</p>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <a href="room.php">
                                    <button class="bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 transition-colors font-semibold">
                                        Book Your Room
                                    </button>
                                </a>
                                <button class="border-2 border-teal-600 text-teal-600 px-6 py-3 rounded-lg hover:bg-teal-50 transition-colors font-semibold">
                                    View Offers
                                </button>
                            </div>
                        </div>
                        <div class="relative h-80 md:h-96">
                            <img src="asset/image/IMG-20251129-WA0045.jpg" alt="Hotel Room" class="w-full h-full object-cover rounded-xl shadow-lg">
                        </div>
                    </div>
                </div>
                <div class="slide">
                    <div class="grid md:grid-cols-2 gap-8 items-center p-8 md:p-12">
                        <div class="space-y-6">
                            <span class="inline-block bg-purple-600 text-white px-4 py-1 rounded-full text-sm font-semibold">New Menu</span>
                            <h1 class="text-4xl md:text-5xl font-bold leading-tight text-gray-900">
                                Delicious Cuisine at our <span class="text-purple-600">Restaurant</span>
                            </h1>
                            <p class="text-xl text-gray-600">Explore a variety of local and continental dishes.</p>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <button class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors font-semibold">
                                    View Menu
                                </button>
                            </div>
                        </div>
                        <div class="relative h-80 md:h-96">
                            <img src="asset/image/foods.jpeg" alt="Restaurant" class="w-full h-full object-cover rounded-xl shadow-lg">
                        </div>
                    </div>
                </div>
                <div class="slide">
                    <div class="grid md:grid-cols-2 gap-8 items-center p-8 md:p-12">
                        <div class="space-y-6">
                            <span class="inline-block bg-pink-600 text-white px-4 py-1 rounded-full text-sm font-semibold">Special Events</span>
                            <h1 class="text-4xl md:text-5xl font-bold leading-tight text-gray-900">
                                Host Your Events With <span class="text-pink-600">Us</span>
                            </h1>
                            <p class="text-xl text-gray-600">Perfect venues for weddings, conferences, and parties.</p>
                            <div class="flex flex-col sm:flex-row gap-3">
                                <a href="contact.php">
                                    <button class="bg-pink-600 text-white px-6 py-3 rounded-lg hover:bg-pink-700 transition-colors font-semibold">
                                        Enquire Now
                                    </button>
                                </a>
                            </div>
                        </div>
                        <div class="relative h-80 md:h-96">
                            <img src="asset/image/IMG-20251129-WA0045.jpg" alt="Event" class="w-full h-full object-cover rounded-xl shadow-lg">
                        </div>
                    </div>
                </div>

                <!-- Pagination dots -->
                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex gap-2">
                    <div class="dot active h-2 w-2 rounded-full bg-teal-600 cursor-pointer"></div>
                    <div class="dot h-2 w-2 rounded-full bg-gray-300 cursor-pointer"></div>
                    <div class="dot h-2 w-2 rounded-full bg-gray-300 cursor-pointer"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rooms Section -->
    <section id="rooms" class="py-16 bg-white">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl md:text-3xl font-bold mb-2 text-gray-900">
                        Grab the best deal on <span class="text-teal-600">Rooms</span>
                    </h2>
                </div>
                <a href="room.php" class="flex items-center gap-2 text-teal-600 hover:text-teal-700 font-medium">
                    View All
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-6">
                <?php if (empty($displayRooms)): ?>
                    <!-- Fallback room cards when no rooms in database -->
                    <div class="bg-white rounded-lg border border-gray-200 hover:shadow-lg transition-all cursor-pointer group overflow-hidden">
                        <div class="relative aspect-square overflow-hidden bg-gray-100 flex items-center justify-center">
                            <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div class="p-4 space-y-2">
                            <h3 class="font-semibold text-sm md:text-base">No Rooms Available</h3>
                            <p class="text-xs text-gray-500">Check back later</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($displayRooms as $room): ?>
                        <div class="bg-white rounded-lg border border-gray-200 hover:shadow-lg transition-all cursor-pointer group overflow-hidden">
                            <div class="relative aspect-square overflow-hidden bg-gray-100">
                                <?php if ($room['image_url']): ?>
                                    <img src="<?php echo htmlspecialchars($room['image_url']); ?>" alt="<?php echo htmlspecialchars($room['room_type']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                <?php endif; ?>
                                <span class="absolute top-3 right-3 bg-teal-600 text-white px-2 py-1 rounded text-xs font-semibold">
                                    <?php
                                    // Calculate discount percentage (example: 25% off for Deluxe, 30% for Executive)
                                    $discount = 0;
                                    if ($room['room_type'] === 'Deluxe Room') $discount = 25;
                                    elseif ($room['room_type'] === 'Executive Suite') $discount = 30;
                                    elseif ($room['room_type'] === 'Standard Room') $discount = 20;
                                    echo $discount > 0 ? $discount . '% OFF' : 'Available';
                                    ?>
                                </span>
                            </div>
                            <div class="p-4 space-y-2">
                                <h3 class="font-semibold text-sm md:text-base line-clamp-2"><?php echo htmlspecialchars($room['room_type']); ?></h3>
                                <div class="flex items-center gap-1 text-sm">
                                    <svg class="h-4 w-4 fill-amber-400 text-amber-400" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                    <span class="font-medium">
                                        <?php
                                        // Generate random rating between 4.0 and 5.0
                                        echo number_format(mt_rand(40, 50) / 10, 1);
                                        ?>
                                    </span>
                                </div>
                                <div class="space-y-1">
                                    <?php if ($discount > 0): ?>
                                        <div class="flex items-baseline gap-2">
                                            <span class="text-lg font-bold text-gray-900"><?php echo formatCurrency($room['price_per_night'] * (1 - $discount/100)); ?></span>
                                            <span class="text-sm text-gray-500 line-through"><?php echo formatCurrency($room['price_per_night']); ?></span>
                                        </div>
                                        <p class="text-xs text-green-600 font-medium">Save: <?php echo formatCurrency($room['price_per_night'] * $discount/100); ?></p>
                                    <?php else: ?>
                                        <span class="text-lg font-bold text-gray-900"><?php echo formatCurrency($room['price_per_night']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                    Explore From <span class="text-teal-600">Top Categories</span>
                </h2>
            </div>

            <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6 place-items-center">
                <!-- Category icons -->
                <a href="room.php">
                    <div class="flex flex-col items-center gap-3 group cursor-pointer">
                        <div class="h-20 w-20 md:h-24 md:w-24 rounded-full bg-blue-50 flex items-center justify-center group-hover:scale-110 transition-transform border-2 border-transparent group-hover:border-teal-500">
                            <svg class="h-8 w-8 md:h-10 md:w-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </div>
                    <span class="text-sm font-medium text-center">Rooms</span>
                    </div>
                </a>
                <a href="food.php#food-section">
                    <div class="flex flex-col items-center gap-3 group cursor-pointer">
                        <div class="h-20 w-20 md:h-24 md:w-24 rounded-full bg-green-50 flex items-center justify-center group-hover:scale-110 transition-transform border-2 border-transparent group-hover:border-teal-500">
                            <svg class="h-8 w-8 md:h-10 md:w-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-center">Food</span>
                    </div>
                </a>
                <a href="food.php#drinks-section">
                    <div class="flex flex-col items-center gap-3 group cursor-pointer">
                        
                        <div class="h-20 w-20 md:h-24 md:w-24 rounded-full bg-purple-50 flex items-center justify-center group-hover:scale-110 transition-transform border-2 border-transparent group-hover:border-teal-500">
                            <svg class="h-8 w-8 md:h-10 md:w-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-center">Drinks</span>
                    </div>
                </a>
                <!-- <div class="flex flex-col items-center gap-3 group cursor-pointer">
                    <div class="h-20 w-20 md:h-24 md:w-24 rounded-full bg-orange-50 flex items-center justify-center group-hover:scale-110 transition-transform border-2 border-transparent group-hover:border-teal-500">
                        <svg class="h-8 w-8 md:h-10 md:w-10 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-medium text-center">Café</span>
                </div> -->
                <a href="contact.php">
                    <div class="flex flex-col items-center gap-3 group cursor-pointer">
                        <div class="h-20 w-20 md:h-24 md:w-24 rounded-full bg-pink-50 flex items-center justify-center group-hover:scale-110 transition-transform border-2 border-transparent group-hover:border-teal-500">
                            <svg class="h-8 w-8 md:h-10 md:w-10 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-center">Events</span>
                    </div>
                </a>
                <a href="contact.php">
                    <div class="flex flex-col items-center gap-3 group cursor-pointer">
                        <div class="h-20 w-20 md:h-24 md:w-24 rounded-full bg-teal-50 flex items-center justify-center group-hover:scale-110 transition-transform border-2 border-transparent group-hover:border-teal-500">
                            <svg class="h-8 w-8 md:h-10 md:w-10 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-center">Services</span>
                    </div>
                </a>
                <a href="contact.php">
                    <div class="flex flex-col items-center gap-3 group cursor-pointer">
                        <div class="h-20 w-20 md:h-24 md:w-24 rounded-full bg-amber-50 flex items-center justify-center group-hover:scale-110 transition-transform border-2 border-transparent group-hover:border-teal-500">
                            <svg class="h-8 w-8 md:h-10 md:w-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-center">Offers</span>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Food Section -->
    <section id="food" class="py-16 bg-white">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                    Popular <span class="text-teal-600">Food & Dishes</span>
                </h2>
                <a href="food-menu.php" class="flex items-center gap-2 text-teal-600 hover:text-teal-700 font-medium">
                    View All
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <?php if (empty($featuredFood)): ?>
                <div class="text-center py-12 text-gray-500">
                    <p>No featured food items available at the moment. Please check back later.</p>
                </div>
            <?php else: ?>
                <div id="food-carousel">
                <?php
                $foodChunks = array_chunk($featuredFood, 4);
                foreach ($foodChunks as $index => $chunk):
                ?>
                    <div class="food-page <?php echo $index === 0 ? 'active' : ''; ?>" data-page="<?php echo $index; ?>" style="<?php echo $index === 0 ? '' : 'display: none;'; ?>">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <?php 
                            $colorClasses = [
                                'from-orange-100 to-orange-50',
                                'from-green-100 to-green-50',
                                'from-red-100 to-red-50',
                                'from-yellow-100 to-yellow-50',
                            ];
                            $i = $index * 4;
                            foreach ($chunk as $food): 
                                $color = $colorClasses[$i % count($colorClasses)];
                                $i++;
                            ?>
                                <div class="bg-gradient-to-br <?php echo $color; ?> border-2 border-transparent hover:border-teal-500 transition-all cursor-pointer group overflow-hidden rounded-lg">
                                    <div class="p-6 space-y-4">
                                        <div class="space-y-2">
                                            <span class="inline-block bg-orange-200 text-orange-800 px-3 py-1 rounded text-xs font-semibold">
                                                <?php echo htmlspecialchars(strtoupper($food['category'])); ?>
                                            </span>
                                            <h3 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($food['item_name']); ?></h3>
                                            <p class="text-lg font-semibold text-teal-600"><?php echo formatCurrency($food['price']); ?></p>
                                        </div>
                                        <div class="relative h-40 rounded-lg overflow-hidden">
                                            <img src="<?php echo htmlspecialchars($food['image_url'] ?? '/placeholder-image.png'); ?>" alt="<?php echo htmlspecialchars($food['item_name']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($featuredFood) && count($featuredFood) > 4): ?>
            <!-- Pagination dots -->
            <div id="food-pagination-dots" class="flex justify-center gap-2 mt-8">
                <?php
                $foodChunks = array_chunk($featuredFood, 4);
                foreach ($foodChunks as $index => $chunk): 
                ?>
                <div class="food-dot h-2 w-2 rounded-full cursor-pointer <?php echo $index === 0 ? 'bg-teal-600' : 'bg-gray-300'; ?>" data-page="<?php echo $index; ?>"></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Drinks Section -->
    <section id="drinks" class="py-16 bg-gray-50">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900">
                    Drinks & <span class="text-teal-600">Beverages</span>
                </h2>
                <a href="#" class="flex items-center gap-2 text-teal-600 hover:text-teal-700 font-medium">
                    View All
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 md:gap-6">
                <!-- Drink cards -->
                <?php if (empty($drinks)): ?>
                    <div class="col-span-full text-center py-12 text-gray-500">
                        <p>No drinks available at the moment. Please check back later.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($drinks as $drink): ?>
                        <div class="bg-white rounded-lg border border-gray-200 hover:shadow-lg transition-all cursor-pointer group overflow-hidden">
                            <div class="relative aspect-square overflow-hidden bg-white">
                                <img src="<?php echo htmlspecialchars($drink['image_url'] ?? '/placeholder-image.png'); ?>" alt="<?php echo htmlspecialchars($drink['item_name']); ?>" class="w-full h-full object-cover p-4 group-hover:scale-110 transition-transform duration-300">
                            </div>
                            <div class="p-4 text-center space-y-2">
                                <h3 class="font-semibold text-sm"><?php echo htmlspecialchars($drink['item_name']); ?></h3>
                                <p class="text-xs font-semibold text-teal-600"><?php echo formatCurrency($drink['price']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-3 gap-6">
                <!-- <div class="bg-white rounded-lg border border-gray-200 text-center hover:shadow-lg transition-shadow p-6 space-y-3">
                    <div class="h-12 w-12 rounded-full bg-teal-50 flex items-center justify-center mx-auto">
                        <svg class="h-6 w-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-base text-gray-900">Free WiFi</h3>
                    <p class="text-sm text-gray-600">High-speed internet</p>
                </div> -->

                <div class="bg-white rounded-lg border border-gray-200 text-center hover:shadow-lg transition-shadow p-6 space-y-3">
                    <div class="h-12 w-12 rounded-full bg-teal-50 flex items-center justify-center mx-auto">
                        <svg class="h-6 w-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-base text-gray-900">24/7 Service</h3>
                    <p class="text-sm text-gray-600">Always available</p>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 text-center hover:shadow-lg transition-shadow p-6 space-y-3">
                    <div class="h-12 w-12 rounded-full bg-teal-50 flex items-center justify-center mx-auto">
                        <svg class="h-6 w-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-base text-gray-900">Secure & Safe</h3>
                    <p class="text-sm text-gray-600">Your safety first</p>
                </div>

                <div class="bg-white rounded-lg border border-gray-200 text-center hover:shadow-lg transition-shadow p-6 space-y-3">
                    <div class="h-12 w-12 rounded-full bg-teal-50 flex items-center justify-center mx-auto">
                        <svg class="h-6 w-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-base text-gray-900">Room Service</h3>
                    <p class="text-sm text-gray-600">On-demand delivery</p>
                </div>
            </div>
        </div>
    </section>

<?php include 'footer.php'; ?>
