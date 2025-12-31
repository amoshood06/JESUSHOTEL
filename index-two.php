<?php
require_once 'config/database.php';
include 'header.php';

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
    <section class="relative h-[500px] bg-cover bg-center" style="background-image: linear-gradient(rgba(0, 195, 255, 0.4), rgba(0, 0, 0, 0.4)), url('asset/image/IMG-20251129-WA0045.jpg');">
        <div class="container mx-auto px-4 h-full flex flex-col justify-center items-center text-center text-white">
            <h1 class="text-5xl md:text-6xl font-bold mb-4">Welcome to <span class="text-teal-600">Avilla Okada Hotel</span></h1>
            <p class="text-2xl mb-2">Affordable Comfort. Everyday Luxury.</p>
            <p class="text-lg mb-8 max-w-2xl">Experience the perfect blend of relaxation, style, and entertainment — designed with you in mind.</p>
            <div class="flex flex-col md:flex-row gap-4">
                <a href="room.php" class="bg-teal-600 text-white px-8 py-3 rounded hover:bg-teal-700 font-medium">Book a Room →</a>
                <a href="food.php" class="border-2 border-white text-white px-8 py-3 rounded hover:bg-white hover:text-gray-800 font-medium">View Menu</a>
                <a href="entertainment.php" class="border-2 border-white text-white px-8 py-3 rounded hover:bg-white hover:text-gray-800 font-medium">Plan Your Event</a>
            </div>
        </div>
    </section>

    <!-- Why Choose D'Villa Okada -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-4">Why Choose Avilla Okada Hotel?</h2>
            <p class="text-center text-gray-600 mb-12">We redefine hospitality for Nigeria's growing middle class and student population</p>
            
            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center p-8">
                    <div class="text-orange-500 text-5xl mb-4"><i class="fas fa-bed"></i></div>
                    <h3 class="text-xl font-bold mb-3">Modern Rooms</h3>
                    <p class="text-gray-600">Stylish comfort for rest and productivity</p>
                </div>
                <div class="text-center p-8">
                    <div class="text-orange-500 text-5xl mb-4"><i class="fas fa-utensils"></i></div>
                    <h3 class="text-xl font-bold mb-3">Delicious Food</h3>
                    <p class="text-gray-600">Tasty, fresh, and affordable meals</p>
                </div>
                <div class="text-center p-8">
                    <div class="text-orange-500 text-5xl mb-4"><i class="fas fa-music"></i></div>
                    <h3 class="text-xl font-bold mb-3">Entertainment Lounge</h3>
                    <p class="text-gray-600">Your spot for fun, music, and memories</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Where Affordability Meets Lifestyle -->
    <section class="py-16 bg-orange-50">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center gap-12">
                <div class="md:w-1/2">
                    <span class="bg-teal-600 text-white px-4 py-1 rounded-full text-sm">What Avilla Okada Hotel</span>
                    <h2 class="text-4xl font-bold mt-4 mb-6">Where Affordability Meets Lifestyle</h2>
                    <p class="text-gray-700 mb-6">At Avilla Okada Hotel, we believe that great hospitality shouldn't be reserved for the few. We're redefining what it means to stay in comfort — offering clean, secure, and beautifully designed spaces for the everyday guest.</p>
                    <p class="text-gray-700 mb-8">Our hotel is built for students, travelers, and working professionals who deserve more for less. With thoughtfully designed rooms, our delicious meals and vibrant entertainment spaces, Avilla Okada Hotel gives you a taste of luxury that fits your lifestyle.</p>
                    
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-orange-500"></i>
                            <span class="text-gray-700">Affordable Rates</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-orange-500"></i>
                            <span class="text-gray-700">24/7 Power & Security</span>
                        </div>
                        <!-- <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-orange-500"></i>
                            <span class="text-gray-700">Free Wi-Fi</span>
                        </div> -->
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-orange-500"></i>
                            <span class="text-gray-700">Professional Service</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-check-circle text-orange-500"></i>
                            <span class="text-gray-700">Relaxed Atmosphere</span>
                        </div>
                    </div>
                    
                    <a href="about.php" class="bg-teal-600 text-white px-8 py-3 rounded inline-block hover:bg-teal-700">Learn More About Us →</a>
                </div>
                <div class="md:w-1/2">
                    <img src="asset/image/room.jpg" alt="Hotel Room" class="rounded-lg shadow-xl w-full">
                </div>
            </div>
        </div>
    </section>

    <!-- Stay in Style. Rest in Comfort -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <h2 class="text-4xl font-bold text-center mb-4">Stay in Style. Rest in Comfort.</h2>
            <p class="text-center text-gray-600 mb-12">Choose from our range of cozy and modern rooms designed for both comfort and productivity</p>
            
            <div class="grid md:grid-cols-3 gap-8">
                <?php
                // Fetch rooms from the database
                $room_types = ['Standard Room', 'Deluxe Room', 'Executive Suite'];
                $placeholders = implode(',', array_fill(0, count($room_types), '?'));
                $stmt = $pdo->prepare("SELECT room_id, room_type, description, price_per_night, image_url FROM rooms WHERE room_type IN ($placeholders) ORDER BY FIELD(room_type, 'Standard Room', 'Deluxe Room', 'Executive Suite')");
                $stmt->execute($room_types);
                $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $displayed_room_types = []; // Array to keep track of displayed room types

                foreach ($rooms as $room) {
                    // Only display if this room type hasn't been displayed yet
                    if (!in_array($room['room_type'], $displayed_room_types)) {
                        // Default image if image_url is empty or null
                        $image_src = !empty($room['image_url']) ? $room['image_url'] : 'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/room-4Yk14wlnaEQee5nzqoT5DTC1EA4oSW.png';
                        $room_description = '';
                        switch ($room['room_type']) {
                            case 'Standard Room':
                                $room_description = 'Perfect for students and solo travelers';
                                break;
                            case 'Deluxe Room':
                                $room_description = 'Extra space and comfort for professionals';
                                break;
                            case 'Executive Suite':
                                $room_description = 'A touch of luxury for those who want more';
                                break;
                            default:
                                $room_description = 'A comfortable stay awaits you';
                                break;
                        }
                ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <img src="<?php echo htmlspecialchars($image_src); ?>" alt="<?php echo htmlspecialchars($room['room_type']); ?>" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($room['room_type']); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($room_description); ?></p>
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-teal-600">₦<?php echo number_format($room['price_per_night'], 0); ?><span class="text-sm text-gray-500">/night</span></span>
                            <a href="book.php?id=<?php echo htmlspecialchars($room['room_id']); ?>" class="bg-teal-600 text-white px-6 py-2 rounded hover:bg-teal-700">Book Now</a>
                        </div>
                    </div>
                </div>
                <?php
                        $displayed_room_types[] = $room['room_type']; // Add room type to displayed list
                    }
                }
                ?>
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
                <a href="food.php#food-section" class="flex items-center gap-2 text-teal-600 hover:text-teal-700 font-medium">
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
                                            <p class="text-lg font-semibold text-teal-600"><?php echo htmlspecialchars($food['price']); ?></p>
                                        </div>
                                        <div class="relative h-40 rounded-lg overflow-hidden">
                                            <img src="<?php echo htmlspecialchars($food['image_url'] ?? '/placeholder-image.png'); ?>" alt="<?php echo htmlspecialchars($food['item_name']); ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                                        </div>
                                        <form class="add-to-cart-form mt-4">
                                            <input type="hidden" name="item_id" value="<?php echo $food['menu_item_id']; ?>">
                                            <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($food['item_name']); ?>">
                                            <input type="hidden" name="item_price" value="<?php echo $food['price']; ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="w-full bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition-colors">
                                                Add to Cart
                                            </button>
                                        </form>
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
                <a href="food.php#drinks-section" class="flex items-center gap-2 text-teal-600 hover:text-teal-700 font-medium">
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
                                <p class="text-xs font-semibold text-teal-600"><?php echo htmlspecialchars($drink['price']); ?></p>
                                <form class="add-to-cart-form pt-2">
                                    <input type="hidden" name="item_id" value="<?php echo $drink['menu_item_id']; ?>">
                                    <input type="hidden" name="item_name" value="<?php echo htmlspecialchars($drink['item_name']); ?>">
                                    <input type="hidden" name="item_price" value="<?php echo $drink['price']; ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="w-full bg-teal-600 text-white px-3 py-1 rounded-lg hover:bg-teal-700 transition-colors text-xs">
                                        Add to Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
            

    <!-- Food & Entertainment Preview -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="grid md:grid-cols-2 gap-8">
                <!-- Food Section -->
                <div class="relative rounded-lg overflow-hidden shadow-xl h-80">
                    <img src="asset/image/food.jpeg" alt="Restaurant" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent flex flex-col justify-end p-8 text-white">
                        <h3 class="text-3xl font-bold mb-2">Good Food. Good Vibes. Great Prices.</h3>
                        <p class="mb-4">Fresh, affordable, and made with love — whether you dine in, take out, or prep ahead for the week.</p>
                        <a href="food.php" class="bg-teal-600 text-white px-6 py-2 rounded inline-block w-fit hover:bg-teal-700">View Menu & Meal Plans →</a>
                    </div>
                </div>

                <!-- Entertainment Section -->
                <div class="relative rounded-lg overflow-hidden shadow-xl h-80">
                    <img src="asset/image/night.jpeg" alt="Entertainment Lounge" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent flex flex-col justify-end p-8 text-white">
                        <h3 class="text-3xl font-bold mb-2">More Than a Stay — It's a Vibe.</h3>
                        <p class="mb-4">Relax, unwind, and enjoy Okada's best nightlife right here at Avilla Okada Hotel. Karaoke nights, live music, and private events.</p>
                        <a href="entertainment.php" class="bg-teal-600 text-white px-6 py-2 rounded inline-block w-fit hover:bg-teal-700">Explore Entertainment →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-gray-800 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-4">Ready to Experience Avilla Okada Hotel?</h2>
            <p class="text-lg mb-8">Book your stay today and discover where comfort meets class without the cost.</p>
            <div class="flex gap-4 justify-center">
                <a href="room.php" class="bg-teal-600 text-white px-8 py-3 rounded hover:bg-teal-700 font-medium">Book Your Stay</a>
                <a href="contact.php" class="border-2 border-white text-white px-8 py-3 rounded hover:bg-white hover:text-gray-800 font-medium">Contact Us</a>
            </div>
        </div>
    </section>

<?php include 'footer.php'; ?>