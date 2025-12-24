<?php
include_once 'user-header.php';

$user_id = $currentUser['user_id'];
$totalBookings = 0;
$activeBookingsCount = 0;
$currentBooking = null;

if ($user_id) {
    try {
        // Total Bookings
        $stmtTotalBookings = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ?");
        $stmtTotalBookings->execute([$user_id]);
        $totalBookings = $stmtTotalBookings->fetchColumn();

        // Active Bookings Count
        $stmtActiveBookingsCount = $pdo->prepare("
            SELECT COUNT(*) FROM bookings
            WHERE user_id = ? AND (status = 'confirmed' OR status = 'checked_in') AND check_out_date >= CURDATE()
        ");
        $stmtActiveBookingsCount->execute([$user_id]);
        $activeBookingsCount = $stmtActiveBookingsCount->fetchColumn();

        // Current Booking Details (most recent active booking)
        $stmtCurrentBooking = $pdo->prepare("
            SELECT b.booking_code, r.room_type, b.check_in_date, b.check_out_date, b.number_of_guests, b.total_amount, b.status as booking_status, r.image_url
            FROM bookings b
            JOIN rooms r ON b.room_id = r.room_id
            WHERE b.user_id = ? AND (b.status = 'confirmed' OR b.status = 'checked_in') AND b.check_out_date >= CURDATE()
            ORDER BY b.check_in_date ASC
            LIMIT 1
        ");
        $stmtCurrentBooking->execute([$user_id]);
        $currentBooking = $stmtCurrentBooking->fetch();

    } catch (PDOException $e) {
        echo "<p class='text-red-500'>Error fetching dashboard data: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">User Dashboard</h1>

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
        <div class="text-2xl font-bold text-gray-900 mb-1"><?php echo $totalBookings; ?></div>
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
        <div class="text-2xl font-bold text-gray-900 mb-1"><?php echo $activeBookingsCount; ?></div>
        <div class="text-sm text-gray-600">Active Booking(s)</div>
    </div>

    <!-- Removed Reward Points card as there's no corresponding DB field -->
</div>

<!-- Current Booking -->
<div class="bg-white rounded-lg border border-gray-200 overflow-hidden mt-6">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-900">Current Booking</h3>
    </div>
    <div class="p-6">
        <?php if ($currentBooking): ?>
        <div class="flex flex-col md:flex-row gap-6">
            <div class="w-full md:w-48 h-48 rounded-lg overflow-hidden bg-gray-100">
                <img src="/<?php echo htmlspecialchars($currentBooking['image_url']); ?>" alt="<?php echo htmlspecialchars($currentBooking['room_type']); ?>" class="w-full h-full object-cover">
            </div>
            <div class="flex-1 space-y-4">
                <div>
                    <div class="flex items-start justify-between mb-2">
                        <h4 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($currentBooking['room_type']); ?></h4>
                        <?php
                            $status_class = '';
                            switch ($currentBooking['booking_status']) {
                                case 'confirmed':
                                    $status_class = 'bg-green-100 text-green-800';
                                    break;
                                case 'checked_in':
                                    $status_class = 'bg-blue-100 text-blue-800';
                                    break;
                                default:
                                    $status_class = 'bg-gray-100 text-gray-800';
                                    break;
                            }
                        ?>
                        <span class="<?php echo $status_class; ?> px-3 py-1 rounded-full text-xs font-semibold"><?php echo htmlspecialchars(ucfirst($currentBooking['booking_status'])); ?></span>
                    </div>
                    <p class="text-gray-600 text-sm">Booking ID: <?php echo htmlspecialchars($currentBooking['booking_code']); ?></p>
                </div>
                <div class="grid md:grid-cols-2 gap-4 text-sm">
                    <div class="flex items-center gap-2 text-gray-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Check-in: <?php echo formatDate($currentBooking['check_in_date']); ?></span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Check-out: <?php echo formatDate($currentBooking['check_out_date']); ?></span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span><?php echo htmlspecialchars($currentBooking['number_of_guests']); ?> Guests</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span><?php echo formatCurrency($currentBooking['total_amount']); ?> Total</span>
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
        <?php else: ?>
            <p class="p-6 text-gray-600">You do not have any active bookings.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-lg border border-gray-200 p-6 mt-6">
    <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
    <div class="grid md:grid-cols-3 gap-4">
        <a href="../index.php" class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 hover:border-teal-500 hover:bg-teal-50 transition-all group">
            <div class="h-10 w-10 rounded-full bg-teal-100 flex items-center justify-center group-hover:bg-teal-600">
                <svg class="h-5 w-5 text-teal-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
            </div>
            <span class="font-medium text-gray-900">New Booking</span>
        </a>
        <a href="bookings.php" class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 hover:border-teal-500 hover:bg-teal-50 transition-all group">
            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center group-hover:bg-blue-600">
                <svg class="h-5 w-5 text-blue-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="font-medium text-gray-900">My Bookings</span>
        </a>
        <a href="../contact.php" class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 hover:border-teal-500 hover:bg-teal-50 transition-all group">
            <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center group-hover:bg-purple-600">
                <svg class="h-5 w-5 text-purple-600 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                </svg>
            </div>
            <span class="font-medium text-gray-900">Contact Support</span>
        </a>
    </div>
</div>

<?php
include_once 'user-footer.php';
?>
