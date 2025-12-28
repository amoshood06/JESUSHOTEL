<?php
require_once 'config/database.php';
include 'header.php';
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
                    
                    <a href="about.html" class="bg-teal-600 text-white px-8 py-3 rounded inline-block hover:bg-teal-700">Learn More About Us →</a>
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
                            <a href="room.php?id=<?php echo htmlspecialchars($room['room_id']); ?>" class="bg-teal-600 text-white px-6 py-2 rounded hover:bg-teal-700">Book Now</a>
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