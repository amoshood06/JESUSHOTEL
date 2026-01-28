<?php 
include 'header-one.php'; 
// include 'config/database.php';

// Fetch 4 random food items with images
try {
    $stmt = $pdo->prepare("SELECT image_url, item_name FROM food_menu WHERE image_url IS NOT NULL AND availability = 1 ORDER BY RAND() LIMIT 4");
    $stmt->execute();
    $randomFoods = $stmt->fetchAll();
} catch(PDOException $e) {
    // Fallback to default images if database error
    $randomFoods = [];
}
?>
<!--title section-->
<section class="bg-white py-16 px-6 text-center">
  <div class="max-w-4xl mx-auto flex flex-col items-center">
    
    <h2 class="font-serif text-5xl md:text-7xl text-gray-700 italic mb-2">
      Restaurant <span class="not-italic">&</span> Bar
    </h2>
    
    <p class="text-xs md:text-sm tracking-[0.5em] uppercase text-gray-500 font-medium mb-6">
      Avilla Okada Hotel
    </p>
    
    <div class="w-48 h-[2px] bg-[#D48255] mb-8"></div>
    
    <p class="font-serif text-xl md:text-3xl text-gray-800 italic">
      Come Let's Give You A Royal Treat In Nature's Paradise!
    </p>
    
  </div>
</section>

<!--bar sub setion -->
<section class="relative w-full h-[400px] flex items-center justify-center overflow-hidden font-sans">
  
  <div class="absolute inset-0 z-0">
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=2070&auto=format&fit=crop')] bg-cover bg-center"></div>
    <div class="absolute inset-0 bg-black/40"></div>
  </div>

  <div class="relative z-10 w-[90%] max-w-2xl border border-white/40 bg-black/20 backdrop-blur-[2px] p-10 md:p-16 text-center text-white">
    
    <h2 class="font-serif text-5xl md:text-7xl italic leading-tight mb-4">
      Bars <span class="not-italic">&</span><br>Restaurants
    </h2>
    
    <p class="text-[10px] md:text-xs uppercase tracking-[0.3em] font-bold">
      - For Anyone's Culinary Taste or Preference -
    </p>

  </div>
</section>
<!--resurant content-->
<section class="bg-white py-12">
  <div class="max-w-7xl mx-auto px-6 mb-12 flex flex-col md:flex-row items-center justify-center gap-4">
    <h2 class="font-serif text-4xl md:text-5xl text-gray-700 italic">Five-Star Restaurant</h2>
    <span class="text-[10px] uppercase tracking-[0.4em] text-gray-400 font-bold border-l md:pl-4 border-gray-300">
      24 Hours Service
    </span>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2">
    <div class="relative h-[600px] overflow-hidden group">
      <img src="https://images.unsplash.com/photo-1490645935967-10de6ba17061?q=80&w=2053&auto=format&fit=crop" 
           alt="The Art of Cuisine" 
           class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
      
      <div class="absolute inset-0 bg-black/50 flex flex-col justify-center px-12 md:px-24 text-white">
        <div class="w-12 h-px bg-[#D48255] mb-6"></div>
        <h3 class="font-serif text-4xl md:text-5xl italic mb-8">The Art Of Cuisine</h3>
        
        <div class="space-y-6 max-w-md">
          <p class="text-sm">
            <span class="font-bold text-[#D48255]">Avilla</span> is our fine-dining restaurant
          </p>
          <p class="text-xs md:text-sm leading-relaxed font-light">
            A first-class restaurant with verities of local, foreign and continental cuisines prepared by well experience and highly trained chefs with years of experience
          </p>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-2 grid-rows-2 h-[600px]">
      <?php
      $fallbackImages = [
          ["url" => "https://images.unsplash.com/photo-1546069901-ba9599a7e63c?q=80&w=2080&auto=format&fit=crop", "alt" => "Salad dish"],
          ["url" => "https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=2070&auto=format&fit=crop", "alt" => "Meat dish"],
          ["url" => "https://images.unsplash.com/photo-1544025162-d76694265947?q=80&w=2069&auto=format&fit=crop", "alt" => "Wine and appetizer"],
          ["url" => "https://images.unsplash.com/photo-1567620905732-2d1ec7bb7445?q=80&w=1980&auto=format&fit=crop", "alt" => "Rice dish"]
      ];
      
      $displayFoods = !empty($randomFoods) ? $randomFoods : $fallbackImages;
      
      foreach ($displayFoods as $index => $food) {
          $imageUrl = isset($food['image_url']) ? $food['image_url'] : $food['url'];
          $altText = isset($food['item_name']) ? $food['item_name'] : $food['alt'];
      ?>
      <div class="overflow-hidden">
        <img src="<?php echo htmlspecialchars($imageUrl); ?>" 
             alt="<?php echo htmlspecialchars($altText); ?>" class="w-full h-full object-cover hover:opacity-80 transition-opacity">
      </div>
      <?php } ?>
    </div>
  </div>
</section>

<!--featured food items-->
<section class="bg-gray-50 py-16">
  <div class="max-w-7xl mx-auto px-6">
    <div class="text-center mb-12">
      <h2 class="font-serif text-4xl md:text-5xl text-gray-700 italic mb-4">Featured Dishes</h2>
      <p class="text-gray-600 max-w-2xl mx-auto">Discover our most popular and delicious dishes, prepared with the finest ingredients and served with exceptional care.</p>
    </div>

    <?php
    // Fetch featured food items
    try {
      $stmt = $pdo->prepare("SELECT * FROM food_menu WHERE availability = 1 AND is_featured = 1 ORDER BY RAND() LIMIT 6");
      $stmt->execute();
      $featuredFoods = $stmt->fetchAll();
    } catch(PDOException $e) {
      $featuredFoods = [];
    }

    if (!empty($featuredFoods)):
    ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php foreach ($featuredFoods as $item): ?>
        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
          <img src="<?= htmlspecialchars($item['image_url'] ?? 'https://via.placeholder.com/400x300?text=No+Image') ?>"
               alt="<?= htmlspecialchars($item['item_name']) ?>"
               class="w-full h-48 object-center">
          <div class="p-6">
            <h3 class="text-xl font-semibold text-gray-800 mb-2"><?= htmlspecialchars($item['item_name']) ?></h3>
            <p class="text-gray-600 text-sm mb-4 line-clamp-2"><?= htmlspecialchars($item['description'] ?? 'No description available.') ?></p>
            <div class="flex items-center justify-between">
              <span class="text-2xl font-bold text-black"><?= formatCurrency($item['price']) ?></span>
              <form class="add-to-cart-form">
                <input type="hidden" name="item_id" value="<?= $item['menu_item_id'] ?>">
                <input type="hidden" name="item_name" value="<?= htmlspecialchars($item['item_name']) ?>">
                <input type="hidden" name="item_price" value="<?= $item['price'] ?>">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="bg-black text-white px-5 py-2 rounded-lg hover:bg-gray-800 transition-colors focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
                  Add to Cart
                </button>
              </form>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center mt-12">
      <a href="food.php" class="inline-flex items-center px-6 py-3 border border-black text-black hover:bg-black hover:text-white transition-colors rounded-lg font-medium">
        View Full Menu
        <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
      </a>
    </div>
    <?php else: ?>
    <div class="text-center py-12">
      <p class="text-gray-600 text-lg">Featured dishes will be available soon. Check out our <a href="food.php" class="text-black hover:underline">full menu</a>.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<!--bar content-->
<section class="bg-white py-16">
  <div class="max-w-7xl mx-auto px-6 mb-12 flex flex-col md:flex-row items-center justify-between">
    <div class="text-center md:text-left mb-6 md:mb-0">
      <p class="text-[10px] uppercase tracking-[0.4em] text-gray-400 font-bold mb-2">24 Hours Service</p>
      <p class="text-sm text-gray-500 italic">Full selection of drinks, wine & spirits...</p>
    </div>
    <h2 class="font-serif text-5xl md:text-6xl text-gray-700 italic">Our <span class="not-italic text-gray-800">Bar</span></h2>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-0 items-stretch">
    <div class="lg:col-span-3 grid grid-rows-2 h-[600px]">
      <div class="overflow-hidden border-r border-b border-white">
        <img src="https://images.unsplash.com/photo-1574096079513-d8259312b785?q=80&w=1000&auto=format&fit=crop" 
             alt="Bar Seating 1" class="w-full h-full object-cover">
      </div>
      <div class="overflow-hidden border-r border-white">
        <img src="https://images.unsplash.com/photo-1560624052-449f5ddf0c31?q=80&w=1000&auto=format&fit=crop" 
             alt="Bar Seating 2" class="w-full h-full object-cover">
      </div>
    </div>

    <div class="lg:col-span-6 relative h-[600px] group overflow-hidden">
      <img src="https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?q=80&w=2070&auto=format&fit=crop" 
           alt="Main Bar" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
      
      <div class="absolute inset-0 bg-black/60 flex flex-col justify-center items-center text-center px-12 text-white">
        <h3 class="font-serif text-4xl italic mb-4">Snappy And Tasty</h3>
        <div class="w-16 h-px bg-[#D48255] mb-6"></div>
        <p class="text-sm md:text-base leading-relaxed font-light max-w-md">
          Our <span class="font-bold text-[#D48255]">Avilla bar</span> is well stocked with exquisite wins, spirit, beer and assorted soft drinks for the comfort and relaxation of our guest.
        </p>
      </div>
    </div>

    <div class="lg:col-span-3 grid grid-rows-2 h-[600px]">
      <div class="overflow-hidden border-l border-b border-white">
        <img src="https://images.unsplash.com/photo-1572116469696-31de0f17cc34?q=80&w=1000&auto=format&fit=crop" 
             alt="Lounge area 1" class="w-full h-full object-cover">
      </div>
      <div class="overflow-hidden border-l border-white">
        <img src="https://images.unsplash.com/photo-1544145945-f904253d0c71?q=80&w=1000&auto=format&fit=crop" 
             alt="Lounge area 2" class="w-full h-full object-cover">
      </div>
    </div>
  </div>

  <div class="mt-12 flex flex-col md:flex-row items-center justify-center gap-8">
    <h3 class="font-serif text-3xl text-gray-700 italic">"Avilla" <span class="not-italic">Bar</span></h3>
    <div class="hidden md:block w-px h-8 bg-gray-300"></div>
    <span class="text-[10px] uppercase tracking-[0.4em] text-gray-400 font-bold">
      24 Hours Service
    </span>
  </div>
</section>
<!--other services-->
<section class="relative w-full min-h-[600px] flex items-center justify-center overflow-hidden font-sans">
  
  <div class="absolute inset-0 z-0">
    <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?q=80&w=2070&auto=format&fit=crop')] bg-cover bg-center"></div>
    <div class="absolute inset-0 bg-black/50"></div>
  </div>

  <div class="relative z-10 w-full max-w-6xl px-6 flex flex-col items-center">
    
    <div class="bg-black/80 backdrop-blur-sm p-10 md:p-14 text-center mb-24 border border-white/10 w-full max-w-4xl">
      <h2 class="font-serif text-3xl md:text-4xl text-white italic mb-6">
        The "Avilla" Bar Will Give You The Best Time
      </h2>
      <p class="text-[10px] md:text-xs uppercase tracking-[0.4em] text-[#D48255] font-bold">
        No Reservations. Just Come. Enjoy.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-12 items-center w-full">
      
      <div class="text-white text-center md:text-left">
        <p class="text-xs md:text-sm leading-relaxed font-light">
          We have a live band at our outdoor bar (gazebo) were all kind of music is being played during the weekend in the evenings for the comfort of our gust as they wine and dine.
        </p>
      </div>

      <div class="flex flex-col items-center gap-6">
        <div class="text-[#D48255]">
          <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24"><path d="M11 9H9V2H7v7H5V2H3v7c0 2.12 1.66 3.84 3.75 3.97V22h2.5v-9.03C11.34 12.84 13 11.12 13 9V2h-2v7zm5-3v8h2.5v8H21V2c-2.76 0-5 2.24-5 4z"/></svg>
        </div>
        <div class="w-12 h-px bg-[#D48255]"></div>
        <div class="text-[#D48255]">
          <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24"><path d="M4 19h16v2H4zM20 3H4v10c0 2.21 1.79 4 4 4h8c2.21 0 4-1.79 4-4V3zm-2 10c0 1.1-.9 2-2 2H8c-1.1 0-2-.9-2-2V5h12v8z"/></svg>
        </div>
      </div>

      <div class="text-white text-center md:text-left space-y-4">
        <h3 class="font-serif text-2xl md:text-3xl italic">
          Everything you can taste, you can enjoy with us
        </h3>
        <p class="text-[10px] uppercase tracking-widest text-[#D48255] font-bold">
          Avilla Restaurants
        </p>
      </div>

    </div>
  </div>
</section>


<?php include 'footer-one.php'; ?>