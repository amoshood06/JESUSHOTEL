<?php
require_once '../config/database.php';

// Check if user is logged in and is admin/staff
if (!isLoggedIn() || !isStaff()) {
    redirect('../login.php');
}

// Get room statistics
try {
    $stmt = $pdo->query("SELECT
        COUNT(*) as total_rooms,
        SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_rooms,
        SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) as occupied_rooms,
        SUM(CASE WHEN status = 'maintenance' THEN 1 ELSE 0 END) as maintenance_rooms
        FROM rooms");
    $roomStats = $stmt->fetch();
} catch(PDOException $e) {
    $roomStats = ['total_rooms' => 0, 'available_rooms' => 0, 'occupied_rooms' => 0, 'maintenance_rooms' => 0];
    error_log('Error fetching room stats: ' . $e->getMessage());
}

// Get today's check-ins
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM bookings WHERE status = 'checked_in' AND DATE(check_in_date) = CURDATE()");
    $stmt->execute();
    $todayCheckins = $stmt->fetch()['count'];
} catch(PDOException $e) {
    $todayCheckins = 0;
    error_log('Error fetching today check-ins: ' . $e->getMessage());
}

// Get booking statistics for current month
try {
    $stmt = $pdo->query("SELECT
        COUNT(*) as total_bookings,
        SUM(total_amount) as total_revenue,
        SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
        SUM(CASE WHEN status = 'checked_in' THEN 1 ELSE 0 END) as active_bookings
        FROM bookings
        WHERE MONTH(booking_date) = MONTH(CURDATE()) AND YEAR(booking_date) = YEAR(CURDATE())");
    $bookingStats = $stmt->fetch();
} catch(PDOException $e) {
    $bookingStats = ['total_bookings' => 0, 'total_revenue' => 0, 'confirmed_bookings' => 0, 'active_bookings' => 0];
    error_log('Error fetching booking stats: ' . $e->getMessage());
}

$pageTitle = 'Dashboard Overview';
$currentPage = 'overview';
require_once 'admin-header.php';
?>

<!-- Overview Content -->
<div class="p-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard Overview</h1>
        <p class="text-gray-600">Welcome back! Here's what's happening at AVILLA OKADA HOTEL today.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Rooms</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $roomStats['total_rooms']; ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Available Rooms</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $roomStats['available_rooms']; ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Today's Check-ins</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo $todayCheckins; ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Monthly Revenue</p>
                    <p class="text-2xl font-bold text-gray-900"><?php echo formatCurrency($bookingStats['total_revenue']); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Trend</h3>
            <canvas id="revenueChart" width="400" height="200"></canvas>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Weekly Bookings</h3>
            <canvas id="bookingsChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">New booking received</p>
                        <p class="text-sm text-gray-500">Room 101 booked for tomorrow</p>
                    </div>
                    <div class="text-sm text-gray-500">2 hours ago</div>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">Food order completed</p>
                        <p class="text-sm text-gray-500">Order #1234 delivered to room 205</p>
                    </div>
                    <div class="text-sm text-gray-500">4 hours ago</div>
                </div>

                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="h-4 w-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">Maintenance request</p>
                        <p class="text-sm text-gray-500">Room 312 requires plumbing repair</p>
                    </div>
                    <div class="text-sm text-gray-500">6 hours ago</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'admin-footer.php'; ?>