<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - AVILLA OKADA HOTEL</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="shared-styles.css">
    <link rel="icon" href="/icon.svg" type="image/svg+xml">
</head>
<body class="bg-gray-50">
    <!-- Top Bar -->
    <div class="bg-gray-100 border-b border-gray-200">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between h-10 text-sm">
                <div class="flex items-center gap-2 text-gray-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Welcome back, John!</span>
                </div>
                <div class="hidden md:flex items-center gap-6">
                    <a href="#" class="text-gray-600 hover:text-gray-900 transition-colors">Support</a>
                    <a href="index.html" class="text-gray-600 hover:text-gray-900 transition-colors">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="sticky top-0 z-50 bg-white border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between h-16 md:h-20 gap-4">
                <div class="flex items-center gap-3">
                    <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <a href="index.html" class="flex items-center gap-2">
                        <div class="h-8 w-8 rounded-lg bg-teal-600 flex items-center justify-center">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                        <span class="text-xl md:text-2xl font-bold text-teal-600">AVILLA OKADA HOTEL</span>
                    </a>
                </div>
                <div class="flex items-center gap-2 md:gap-4">
                    <a href="index.html" class="text-gray-600 hover:text-gray-900 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </a>
                    <button class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition-colors hidden md:block">
                        New Booking
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="hidden fixed inset-0 z-40 bg-black bg-opacity-50">
        <div class="absolute inset-y-0 left-0 w-64 bg-white shadow-lg">
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
                <a href="index.html" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Home
                </a>
                <a href="user-dashboard.html" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-teal-50 text-teal-600 font-medium">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    My Dashboard
                </a>
            </nav>
        </div>
    </div>

    <!-- Dashboard Content -->
    <div class="container mx-auto px-4 lg:px-8 py-8">
        <div class="grid lg:grid-cols-4 gap-6">
            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg border border-gray-200 p-6 space-y-6">
                    <!-- Profile Section -->
                    <div class="text-center space-y-3">
                        <div class="h-20 w-20 rounded-full bg-teal-100 flex items-center justify-center mx-auto">
                            <svg class="h-10 w-10 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">John Doe</h3>
                            <p class="text-sm text-gray-600">john.doe@email.com</p>
                        </div>
                        <span class="inline-block bg-amber-100 text-amber-800 px-3 py-1 rounded-full text-xs font-semibold">Gold Member</span>
                    </div>

                    <!-- Navigation -->
                    <nav class="space-y-1">
                        <a href="#dashboard" class="dashboard-tab active flex items-center gap-3 px-4 py-3 rounded-lg bg-teal-50 text-teal-600 font-medium transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Dashboard
                        </a>
                        <a href="#bookings" class="dashboard-tab flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            My Bookings history
                        </a>
                        <a href="#bookings" class="dashboard-tab flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            My Food and Drinks history
                        </a>
                        <a href="#profile" class="dashboard-tab flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Profile Settings
                        </a>
                        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-100 transition-colors text-red-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                <!-- Dashboard Tab Content -->
                <div id="dashboard-content" class="dashboard-content space-y-6">
                    <!-- Stats Cards -->
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="h-12 w-12 rounded-full bg-teal-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-2xl font-bold text-gray-900 mb-1">3</div>
                            <div class="text-sm text-gray-600">Total Bookings</div>
                        </div>

                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-2xl font-bold text-gray-900 mb-1">1</div>
                            <div class="text-sm text-gray-600">Active Booking</div>
                        </div>

                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="h-12 w-12 rounded-full bg-amber-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="text-2xl font-bold text-gray-900 mb-1">240</div>
                            <div class="text-sm text-gray-600">Reward Points</div>
                        </div>
                    </div>

                    <!-- Current Booking -->
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900">Current Booking</h3>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row gap-6">
                                <div class="w-full md:w-48 h-48 rounded-lg overflow-hidden bg-gray-100">
                                    <img src="/luxury-hotel-deluxe-room-spacious.jpg" alt="Room" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1 space-y-4">
                                    <div>
                                        <div class="flex items-start justify-between mb-2">
                                            <h4 class="text-xl font-bold text-gray-900">Deluxe Room</h4>
                                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Confirmed</span>
                                        </div>
                                        <p class="text-gray-600 text-sm">Booking ID: #AV2025-0123</p>
                                    </div>
                                    <div class="grid md:grid-cols-2 gap-4 text-sm">
                                        <div class="flex items-center gap-2 text-gray-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span>Check-in: Jan 28, 2025</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-gray-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            <span>Check-out: Jan 30, 2025</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-gray-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                            <span>2 Guests</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-gray-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span>₦24,000 Total</span>
                                        </div>
                                    </div>
                                    <div class="flex gap-3 pt-2">
                                        <button class="bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition-colors text-sm">
                                            View Details
                                        </button>
                                        <button class="border border-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                                            Cancel Booking
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="grid md:grid-cols-3 gap-4">
                            <a href="index.html" class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 hover:border-teal-500 hover:bg-teal-50 transition-all group">
                                <div class="h-10 w-10 rounded-full bg-teal-100 flex items-center justify-center group-hover:bg-teal-600">
                                    <svg class="h-5 w-5 text-teal-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </div>
                                <span class="font-medium text-gray-900">New Booking</span>
                            </a>
                            <a href="#" class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 hover:border-teal-500 hover:bg-teal-50 transition-all group">
                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center group-hover:bg-blue-600">
                                    <svg class="h-5 w-5 text-blue-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <span class="font-medium text-gray-900">Modify Booking</span>
                            </a>
                            <a href="#" class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 hover:border-teal-500 hover:bg-teal-50 transition-all group">
                                <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center group-hover:bg-purple-600">
                                    <svg class="h-5 w-5 text-purple-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                    </svg>
                                </div>
                                <span class="font-medium text-gray-900">Contact Support</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Bookings Tab Content -->
                <div id="bookings-content" class="dashboard-content hidden space-y-6">
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <div class="p-6 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900">My Bookings</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Booking ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Room Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Check-in</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Check-out</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">#AV2025-0123</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">Deluxe Room</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">Jan 28, 2025</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">Jan 30, 2025</td>
                                        <td class="px-6 py-4"><span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Confirmed</span></td>
                                        <td class="px-6 py-4"><button class="text-teal-600 hover:text-teal-700 font-medium text-sm">View</button></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">#AV2024-0987</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">Standard Room</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">Dec 15, 2024</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">Dec 17, 2024</td>
                                        <td class="px-6 py-4"><span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-semibold">Completed</span></td>
                                        <td class="px-6 py-4"><button class="text-teal-600 hover:text-teal-700 font-medium text-sm">View</button></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">#AV2024-0854</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">Executive Suite</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">Nov 20, 2024</td>
                                        <td class="px-6 py-4 text-sm text-gray-600">Nov 22, 2024</td>
                                        <td class="px-6 py-4"><span class="bg-gray-100 text-gray-800 px-3 py-1 rounded-full text-xs font-semibold">Completed</span></td>
                                        <td class="px-6 py-4"><button class="text-teal-600 hover:text-teal-700 font-medium text-sm">View</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Profile Tab Content -->
                <div id="profile-content" class="dashboard-content hidden space-y-6">
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Profile Settings</h3>
                        <form class="space-y-6">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                    <input type="text" value="John" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                    <input type="text" value="Doe" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <input type="email" value="john.doe@email.com" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" value="+234 801 234 5678" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                <textarea rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">Lagos, Nigeria</textarea>
                            </div>
                            <div class="flex gap-4">
                                <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 transition-colors">
                                    Save Changes
                                </button>
                                <button type="button" class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-6">Change Password</h3>
                        <form class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                <input type="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                            </div>
                            <button type="submit" class="bg-teal-600 text-white px-6 py-2 rounded-lg hover:bg-teal-700 transition-colors">
                                Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="app.js"></script>
</body>
</html>
