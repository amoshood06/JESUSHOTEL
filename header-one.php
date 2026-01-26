<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="asset/image/mylogo.jpg" type="image/x-icon">
    <title>Avilla Okada Hotel</title>
</head>
<body>
    <!--headers-->
 <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<?php
require_once 'config/database.php';

try {
    // Fetch food categories
    $foodCategoriesStmt = $pdo->query("SELECT DISTINCT category FROM food_menu WHERE category != 'Drinks' ORDER BY category");
    $foodCategories = $foodCategoriesStmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Fetch drink categories
    $drinkCategoriesStmt = $pdo->query("SELECT DISTINCT drink_category FROM food_menu WHERE drink_category IS NOT NULL ORDER BY drink_category");
    $drinkCategories = $drinkCategoriesStmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $foodCategories = [];
    $drinkCategories = [];
}
?>

<header class="sticky top-0 z-50 w-full font-sans shadow-md" x-data="{ mobileMenuOpen: false }">
  
  <div class="bg-[#E6B49A] text-[#1E293B] py-2 px-4 sm:px-6 flex flex-col sm:flex-row justify-between items-center text-[10px] sm:text-xs md:text-sm gap-2">
    <div class="flex items-center gap-2 text-center sm:text-left">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
      <span>Okada Town, Edo State, Nigeria.</span>
    </div>
    
    <div class="flex items-center gap-3">
      <a href="#" class="bg-[#4267B2] text-white p-1 rounded-full hover:opacity-80"><svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg></a>
      <a href="#" class="bg-[#262626] text-white p-1 rounded-full hover:opacity-80"><svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 1.366.062 2.633.332 3.608 1.308.975.975 1.245 2.242 1.308 3.608.058 1.266.07 1.646.07 4.85s-.012 3.584-.07 4.85c-.063 1.366-.333 2.633-1.308 3.608-.975.975-2.242 1.245-3.608 1.308-1.266.058-1.646.07-4.85.07s-3.584-.012-4.85-.07c-1.366-.063-2.633-.333-3.608-1.308-.975-.975-1.245-2.242-1.308-3.608-.058-1.266-.07-1.646-.07-4.85s.012-3.584.07-4.85c.062-1.366.332-2.633 1.308-3.608.975-.975 2.242-1.245 3.608-1.308 1.266-.058 1.646-.07 4.85-.07zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948s.014 3.667.072 4.947c.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072s3.667-.014 4.947-.072c4.358-.2 6.78-2.618 6.98-6.98.058-1.281.072-1.689.072-4.948s-.014-3.667-.072-4.947c-.2-4.358-2.618-6.78-6.98-6.98-1.28-.058-1.689-.072-4.948-.072zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.162 6.162 6.162 6.162-2.759 6.162-6.162-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.791-4-4s1.791-4 4-4 4 1.791 4 4-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
    </div>
  </div>

  <div class="hidden md:flex bg-[#D1E9DB] text-[#2C4E5E] py-3 px-6 justify-between items-center text-sm border-b border-gray-100">
    <div class="flex gap-8">
      <span>(+234) 707 198 4117</span>
      <a href="mailto:avillaokadahotel@gmail.com" class="hover:underline">avillaokadahotel@gmail.com</a>
    </div>
    <a href="#" class="hover:underline font-medium">Download our COVID 19 Guide Line</a>
  </div>

  <nav class="bg-white py-4 px-4 sm:px-6 flex justify-between items-center">
    <button @click="mobileMenuOpen = true" class="lg:hidden text-gray-600 p-2">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" /></svg>
    </button>

    <div class="flex items-center">
      <img src="asset/image/mylogo.jpg" alt="Logo" class="h-12 md:h-16">
    </div>

    <div class="hidden lg:flex items-center gap-8 font-bold text-[#4A4A4A] text-sm tracking-wide">
      <a href="index.php" class="hover:text-[#D48255]">HOME</a>
      <a href="about.php" class="text-[#D48255]">ABOUT US</a>
      <div class="group relative py-2 cursor-pointer">
        <div class="flex items-center gap-1 hover:text-[#D48255]">
          <span>ROOMS</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" /></svg>
        </div>
        <div class="absolute top-full left-0 w-48 bg-white shadow-xl hidden group-hover:block border-t-2 border-[#D48255]">
          <a href="standard-rooms.php" class="block px-4 py-3 hover:bg-gray-50 text-xs">Standard Room</a>
          <a href="deluxe-rooms.php" class="block px-4 py-3 hover:bg-gray-50 text-xs">Deluxe Room</a>
          <a href="executive-rooms.php" class="block px-4 py-3 hover:bg-gray-50 text-xs">Executive Suite</a>
        </div>
      </div>
      <!--desktop restaurant menu-->
      <div class="group relative py-2 cursor-pointer">
        <div class="flex items-center gap-1 hover:text-[#D48255]">
          <span>RESTAURANT AND BAR</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" /></svg>
        </div>
        <div class="absolute top-full left-0 w-48 bg-white shadow-xl hidden group-hover:block border-t-2 border-[#D48255]">
          <div class="group/subfood relative">
            <a href="#" class="flex justify-between items-center px-4 py-3 hover:bg-gray-50 text-xs">
              <span>Food</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" /></svg>
            </a>
            <div class="absolute left-full top-0 w-48 bg-white shadow-xl hidden group-hover/subfood:block border-t-2 border-[#D48255]">
              <?php foreach ($foodCategories as $category): ?>
                <a href="food.php?category=<?= urlencode($category) ?>" class="block px-4 py-2 hover:bg-gray-50 text-xs hover:text-[#D48255]"><?= htmlspecialchars($category) ?></a>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="group/subdrink relative">
            <a href="#" class="flex justify-between items-center px-4 py-3 hover:bg-gray-50 text-xs">
              <span>Drink</span>
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" /></svg>
            </a>
            <div class="absolute left-full top-0 w-48 bg-white shadow-xl hidden group-hover/subdrink:block border-t-2 border-[#D48255]">
              <?php foreach ($drinkCategories as $category): ?>
                <a href="drink.php?drink_category=<?= urlencode($category) ?>" class="block px-4 py-2 hover:bg-gray-50 text-xs hover:text-[#D48255]"><?= htmlspecialchars($category) ?></a>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
      <div class="group relative py-2 cursor-pointer">
        <div class="flex items-center gap-1 hover:text-[#D48255]">
          <span>OUR SERVICES</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" /></svg>
        </div>
        <div class="absolute top-full left-0 w-48 bg-white shadow-xl hidden group-hover:block border-t-2 border-[#D48255]">
          <a href="restaurant-and-bar.php" class="block px-4 py-3 hover:bg-gray-50 text-xs">Coursine</a>
          <!-- <a href="#" class="block px-4 py-3 hover:bg-gray-50 text-xs">Pool and Fitness Center</a>
          <a href="#" class="block px-4 py-3 hover:bg-gray-50 text-xs">Spa and Beauty Saloon</a> -->
          <a href="event-and-conference-halls.php" class="block px-4 py-3 hover:bg-gray-50 text-xs">Event and Conference Halls</a>
        </div>
      </div>
      <a href="contacts.php" class="hover:text-[#D48255]">CONTACT US</a>
    </div>

    <div class="flex items-center gap-2 sm:gap-6">
      <a href="cart.php" class="hidden sm:block relative text-gray-400 border p-2 rounded hover:text-gray-600 transition-colors">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
        <?php
        $cartCount = 0;
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $cartCount += $item['quantity'] ?? 0;
            }
        }
        if ($cartCount > 0):
        ?>
        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center">
            <?= $cartCount > 99 ? '99+' : $cartCount ?>
        </span>
        <?php endif; ?>
      </a>
      <a href="bookings.php">
      <button class="bg-[#D48255] text-white px-4 sm:px-8 py-2 sm:py-3 rounded-full text-xs sm:text-sm font-semibold shadow-lg">
        Book Online
      </button>
      </a>
    </div>
  </nav>

  <div x-show="mobileMenuOpen" 
       class="fixed inset-0 z-[60] lg:hidden" 
       x-transition:enter="transition ease-out duration-300" 
       x-transition:enter-start="opacity-0" 
       x-transition:enter-end="opacity-100">
    
    <div class="fixed inset-0 bg-black/50" @click="mobileMenuOpen = false"></div>

    <div class="fixed inset-y-0 left-0 w-3/4 max-w-xs bg-white shadow-xl p-6"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0">
      
      <div class="flex justify-between items-center mb-8">
        <span class="font-bold text-lg text-[#D48255]">MENU</span>
        <button @click="mobileMenuOpen = false" class="text-gray-500">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
        </button>
      </div>

      <div class="flex flex-col gap-6 font-bold text-gray-700">
        <a href="index.php" class="hover:text-[#D48255] border-b pb-2">HOME</a>
        <a href="about.php" class="hover:text-[#D48255] border-b pb-2">ABOUT US</a>
                <div x-data="{ open: false }">
          <button @click="open = !open" class="w-full flex justify-between items-center hover:text-[#D48255] border-b pb-2">
            <span>ROOMS</span>
            <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </button>
          <div x-show="open" x-transition class="pl-4 pt-2 flex flex-col gap-4 text-sm text-gray-600">
            <a href="standard-rooms.php" class="hover:text-[#D48255]">Standard Room</a>
            <a href="deluxe-rooms.php" class="hover:text-[#D48255]">Deluxe Room</a>
            <a href="executive-rooms.php" class="hover:text-[#D48255]">Executive Suite</a>
          </div>
        </div>
        <div x-data="{ open: false, foodOpen: false, drinkOpen: false }">
          <button @click="open = !open" class="w-full flex justify-between items-center hover:text-[#D48255] border-b pb-2">
            <span>RESTAURANT</span>
            <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </button>
          <div x-show="open" x-transition class="pl-4 pt-2 flex flex-col gap-4 text-sm text-gray-600">
            <div>
              <button @click="foodOpen = !foodOpen" class="w-full flex justify-between items-center hover:text-[#D48255]">
                <span>Food</span>
                <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': foodOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
              </button>
              <div x-show="foodOpen" x-transition class="pl-4 pt-2 flex flex-col gap-2 text-xs text-gray-500">
                <?php foreach ($foodCategories as $category): ?>
                  <a href="food.php?category=<?= urlencode($category) ?>" class="hover:text-[#D48255]"><?= htmlspecialchars($category) ?></a>
                <?php endforeach; ?>
              </div>
            </div>
            <div>
              <button @click="drinkOpen = !drinkOpen" class="w-full flex justify-between items-center hover:text-[#D48255]">
                <span>Drink</span>
                <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': drinkOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
              </button>
              <div x-show="drinkOpen" x-transition class="pl-4 pt-2 flex flex-col gap-2 text-xs text-gray-500">
                <?php foreach ($drinkCategories as $category): ?>
                  <a href="drink.php?drink_category=<?= urlencode($category) ?>" class="hover:text-[#D48255]"><?= htmlspecialchars($category) ?></a>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
                <div x-data="{ open: false }">
          <button @click="open = !open" class="w-full flex justify-between items-center hover:text-[#D48255] border-b pb-2">
            <span>OUR SERVICE</span>
            <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
          </button>
          <div x-show="open" x-transition class="pl-4 pt-2 flex flex-col gap-4 text-sm text-gray-600">
            <a href="restaurant-and-bar.php" class="hover:text-[#D48255]">Restaurant and Bar</a>
            <!-- <a href="#" class="hover:text-[#D48255]">Pool and Fitness Center</a>
            <a href="#" class="hover:text-[#D48255]">Spa and Beauty Saloon</a> -->
            <a href="event-and-conference-halls.php" class="hover:text-[#D48255]">Event and Conference Halls</a>
          </div>
        </div>
        <a href="contacts.php" class="hover:text-[#D48255] border-b pb-2">CONTACT US</a>
        <a href="cart.php" class="flex items-center gap-2 hover:text-[#D48255] border-b pb-2">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
          </svg>
          <span>Cart
            <?php if ($cartCount > 0): ?>
              (<?= $cartCount > 99 ? '99+' : $cartCount ?>)
            <?php endif; ?>
          </span>
        </a>
      </div>

      <div class="mt-10 text-xs text-gray-500 flex flex-col gap-2">
        <span>(+234) 707 198 4117</span>
        <span>avillaokadahotel@gmail.com</span>
      </div>
    </div>
  </div>
</header>

<div class="h-screen bg-gray-50">
    