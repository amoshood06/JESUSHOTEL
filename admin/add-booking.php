<?php
require_once '../config/database.php';

// Check if user is logged in and is admin/staff
if (!isLoggedIn() || !isStaff()) {
    redirect('../login.php');
}

$pageTitle = 'Add New Booking';
$currentPage = 'bookings';

$message = '';
$messageType = '';

// Fetch available rooms
try {
    $stmt = $pdo->query("SELECT room_id, room_number, room_type, price_per_night FROM rooms WHERE status = 'available'");
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching rooms: " . $e->getMessage());
    $rooms = [];
}

// Fetch users for guest selection
try {
    $stmt = $pdo->query("SELECT user_id, first_name, last_name, email FROM users WHERE role = 'user' ORDER BY first_name ASC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error fetching users: " . $e->getMessage());
    $users = [];
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room_id = sanitize($_POST['room_id']);
    $user_id = sanitize($_POST['user_id']);
    $check_in_date = sanitize($_POST['check_in_date']);
    $check_out_date = sanitize($_POST['check_out_date']);
    $number_of_guests = sanitize($_POST['number_of_guests']);
    $status = sanitize($_POST['status']);
    
    // Basic validation
    if (empty($room_id) || empty($user_id) || empty($check_in_date) || empty($check_out_date) || empty($number_of_guests) || empty($status)) {
        $message = 'All fields are required.';
        $messageType = 'error';
    } elseif (strtotime($check_in_date) >= strtotime($check_out_date)) {
        $message = 'Check-out date must be after check-in date.';
        $messageType = 'error';
    } else {
        try {
            // Calculate total price (simplified, ideally this would be more complex based on room price and duration)
            $stmt = $pdo->prepare("SELECT price_per_night FROM rooms WHERE room_id = ?");
            $stmt->execute([$room_id]);
            $room = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($room) {
                $check_in_timestamp = strtotime($check_in_date);
                $check_out_timestamp = strtotime($check_out_date);
                $nights = ceil(abs($check_out_timestamp - $check_in_timestamp) / (60 * 60 * 24));
                $total_amount = $room['price_per_night'] * $nights;
            } else {
                $total_amount = 0; // Fallback
            }

            // Generate a simple booking code (e.g., BK-YYYYMMDD-RANDOM)
            $booking_code = 'BK-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(4)));

            $stmt = $pdo->prepare("INSERT INTO bookings (room_id, user_id, check_in_date, check_out_date, number_of_guests, total_amount, status, booking_code, booking_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$room_id, $user_id, $check_in_date, $check_out_date, $number_of_guests, $total_amount, $status, $booking_code]);

            $message = 'Booking added successfully!';
            $messageType = 'success';
            // Optionally redirect to bookings list or clear form
            // redirect('bookings.php');
        } catch (PDOException $e) {
            $message = 'Error adding booking: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

require_once 'admin-header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Customizing Flatpickr for Tailwind compatibility */
    .flatpickr-calendar {
        border-radius: 0.5rem; /* rounded-lg */
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); /* shadow-lg */
        border: 1px solid #e2e8f0; /* border-gray-200 */
    }
    .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, .flatpickr-day.selected.prevMonth, .flatpickr-day.startRange.prevMonth, .flatpickr-day.endRange.prevMonth, .flatpickr-day.selected.nextMonth, .flatpickr-day.startRange.nextMonth, .flatpickr-day.endRange.nextMonth {
        background: #0d9488; /* bg-teal-600 */
        border-color: #0d9488;
        color: #fff;
    }
    .flatpickr-day.today {
        border-color: #0d9488; /* border-teal-600 */
    }
    .flatpickr-day.today:hover, .flatpickr-day.today:focus {
        background: #0d9488;
        color: #fff;
    }
</style>

<div id="add-booking" class="tab-content p-6 lg:p-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Add New Booking</h1>
            <p class="text-gray-500 mt-1">Create a new booking for a guest.</p>
        </div>
        <div class="flex items-center gap-2 mt-4 sm:mt-0">
            <a href="bookings.php" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors font-semibold flex items-center gap-2">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Bookings
            </a>
        </div>
    </div>

    <?php if ($message): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'error' ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-green-100 text-green-800 border border-green-200'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm p-6 lg:p-8">
        <form action="add-booking.php" method="POST"
      class="space-y-6 bg-white p-6 rounded-xl border border-gray-200 shadow-sm">

    <!-- Guest & Room -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="user_id" class="block text-sm font-semibold text-gray-700 mb-1">
                Guest User
            </label>
            <select id="user_id" name="user_id" required
                class="w-full rounded-lg border border-gray-300 px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-green-500
                       focus:border-green-500">
                <option value="">Select a Guest</option>
                <?php foreach ($users as $user_option): ?>
                    <option value="<?= htmlspecialchars($user_option['user_id']); ?>">
                        <?= htmlspecialchars($user_option['first_name'].' '.$user_option['last_name'].' ('.$user_option['email'].')'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="room_id" class="block text-sm font-semibold text-gray-700 mb-1">
                Room
            </label>
            <select id="room_id" name="room_id" required
                class="w-full rounded-lg border border-gray-300 px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-green-500
                       focus:border-green-500">
                <option value="">Select a Room</option>
                <?php foreach ($rooms as $room_option): ?>
                    <option value="<?= htmlspecialchars($room_option['room_id']); ?>">
                        <?= htmlspecialchars($room_option['room_number'].' - '.$room_option['room_type'].' (₦'.number_format($room_option['price_per_night'],2).'/night)'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Dates -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="check_in_date" class="block text-sm font-semibold text-gray-700 mb-1">
                Check-in Date
            </label>
            <input type="date" id="check_in_date" name="check_in_date" required
                class="w-full rounded-lg border border-gray-300 px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-green-500
                       focus:border-green-500">
        </div>

        <div>
            <label for="check_out_date" class="block text-sm font-semibold text-gray-700 mb-1">
                Check-out Date
            </label>
            <input type="date" id="check_out_date" name="check_out_date" required
                class="w-full rounded-lg border border-gray-300 px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-green-500
                       focus:border-green-500">
        </div>
    </div>

    <!-- Guests & Status -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="number_of_guests" class="block text-sm font-semibold text-gray-700 mb-1">
                Number of Guests
            </label>
            <input type="number" id="number_of_guests" name="number_of_guests" min="1" value="1" required
                class="w-full rounded-lg border border-gray-300 px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-green-500
                       focus:border-green-500">
        </div>

        <div>
            <label for="status" class="block text-sm font-semibold text-gray-700 mb-1">
                Status
            </label>
            <select id="status" name="status" required
                class="w-full rounded-lg border border-gray-300 px-3 py-2
                       focus:outline-none focus:ring-2 focus:ring-green-500
                       focus:border-green-500">
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <!-- Button -->
    <div class="flex justify-end pt-4">
        <button type="submit"
            class="flex items-center gap-2 px-6 py-3 rounded-lg
                   border-2 border-green-500 text-green-500 font-semibold
                   hover:bg-green-500 hover:text-white
                   focus:outline-none focus:ring-2 focus:ring-green-400
                   transition duration-200">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M5 13l4 4L19 7"/>
            </svg>
            Add Booking
        </button>
    </div>
</form>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkInDate = document.getElementById('check_in_date');
        const checkOutDate = document.getElementById('check_out_date');

        const fpCheckIn = flatpickr(checkInDate, {
            dateFormat: 'Y-m-d',
            minDate: 'today',
            onChange: function(selectedDates, dateStr, instance) {
                fpCheckOut.set('minDate', dateStr);
            }
        });

        const fpCheckOut = flatpickr(checkOutDate, {
            dateFormat: 'Y-m-d',
            minDate: 'today',
            onChange: function(selectedDates, dateStr, instance) {
                fpCheckIn.set('maxDate', dateStr);
            }
        });
    });
</script>

<?php require_once 'admin-footer.php'; ?>
