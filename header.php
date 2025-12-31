<?php
require_once 'config/database.php'; // Ensure session is started and helpers are available

// Calculate cart item count
$cartItemCount = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cartItemCount += $item['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AVILLA OKADA HOTEL - Affordable Comfort & Everyday Luxury</title>
    <meta name="description" content="Experience affordable comfort at AVILLA OKADA HOTEL. Book rooms, enjoy authentic Nigerian cuisine, and create memorable moments in Edo State.">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="shared-styles.css">
    <link rel="icon" href="/icon.svg" type="image/svg+xml">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>
<body class="bg-gray-50">
    <!-- Top Bar -->
    <div class="bg-gray-100 border-b border-gray-200">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between h-10 text-sm">
                <div class="flex items-center gap-2 text-gray-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Okada Town, Edo State</span>
                </div>
                <div class="hidden md:flex items-center gap-6">
                    <a href="#" class="text-gray-600 hover:text-gray-900 transition-colors">Track Booking</a>
                    <a href="#" class="text-gray-600 hover:text-gray-900 transition-colors">Special Offers</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between h-16 md:h-20 gap-4">
                <!-- Logo -->
                <div class="flex items-center gap-3">
                    <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <a href="#" class="flex items-center gap-2">
                        <img src="asset/image/mylogo.jpg" class="h-8 w-8 rounded-lg bg-teal-600 flex items-center justify-center" alt="">
                        <span class="text-xl md:text-2xl font-bold text-teal-600">AVILLA OKADA HOTEL</span>
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="hidden md:flex flex-1 max-w-xl mx-8">
                    <form action="search.php" method="GET" class="relative w-full">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="q" placeholder="Search for rooms, food, drinks..." class="w-full pl-10 pr-4 h-11 bg-gray-100 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                    </form>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2 md:gap-4">
                    <a href="account.php" class="hidden md:flex p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </a>
                    <a href="cart.php">
                        <button class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="hidden md:inline">Cart</span>
                            <?php if ($cartItemCount > 0): ?>
                                <span class="ml-1 px-2 py-0.5 text-xs font-bold bg-red-500 text-white rounded-full"><?= $cartItemCount ?></span>
                            <?php endif; ?>
                        </button>
                    </a>
                    <?php if (!isLoggedIn()): ?>
                        <a href="login.php">
                            <button class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition-colors hidden md:block">
                                Login
                            </button>
                        </a>
                    <?php else:
                        $dashboard_link = isAdmin() ? 'admin/admin-dashboard.php' : 'user/user-dashboard.php';
                        ?>
                        <a href="<?= $dashboard_link ?>">
                            <button class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition-colors hidden md:block">
                                Dashboard
                            </button>
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Desktop Category Navigation -->
            <div class="hidden md:flex items-center gap-1 h-12 border-t border-gray-200 overflow-x-auto">
                <a href="index.php" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors text-sm whitespace-nowrap">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Home
                </a>
                <a href="room.php" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors text-sm whitespace-nowrap">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Rooms
                </a>
                <a href="food.php" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors text-sm whitespace-nowrap">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Food
                </a>
                <a href="drink.php" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors text-sm whitespace-nowrap">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    Drinks
                </a>
                <a href="entertainment.php" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors text-sm whitespace-nowrap">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.148-6.443a1.76 1.76 0 011.164-2.264l5.524-1.84a1.76 1.76 0 012.264 1.164l2.148 6.443A1.76 1.76 0 0111 19.241v-7.359"/>
                    </svg>
                    Entertainment
                </a>
                <a href="about.php" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors text-sm whitespace-nowrap">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    About Us
                </a>
                <!-- <a href="privacy-policy.php" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors text-sm whitespace-nowrap">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Privacy Policy
                </a>
                <a href="terms-and-conditions.php" class="flex items-center gap-2 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors text-sm whitespace-nowrap">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Terms & Conditions
                </a> -->
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-menu" class="hidden fixed inset-0 z-40 bg-black bg-opacity-50">
        <div class="absolute inset-y-0 left-0 w-64 bg-white shadow-lg transform transition-transform">
            <div class="p-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <span class="font-bold text-lg text-teal-600">Menu</span>
                    <button id="mobile-menu-close" class="p-2 rounded-lg hover:bg-gray-100">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
            <nav class="p-4 space-y-2">
                <a href="index.php" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-teal-50 text-teal-600 font-medium">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Home
                </a>
                <a href="room.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Rooms
                </a>
                <a href="food.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Food
                </a>
                <a href="drink.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    Drinks
                </a>
                <a href="entertainment.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.148-6.443a1.76 1.76 0 011.164-2.264l5.524-1.84a1.76 1.76 0 012.264 1.164l2.148 6.443A1.76 1.76 0 0111 19.241v-7.359"/>
                    </svg>
                    Entertainment
                </a>
                <a href="about.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    About Us
                </a>
                <a href="privacy-policy.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Privacy Policy
                </a>
                <a href="terms-and-conditions.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Terms & Conditions
                </a>
                <a href="login.php" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    My Account
                </a>
            </nav>
        </div>
    </div>