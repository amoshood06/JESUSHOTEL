<?php
require_once '../config/database.php';

// Check if user is logged in and is admin/staff
if (!isLoggedIn() || !isStaff()) {
    redirect('../login.php');
}

// Handle booking operations
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        // Update booking status
        $booking_id = (int)$_POST['booking_id'];
        $status = sanitize($_POST['status']);

        try {
            $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE booking_id = ?");
            $stmt->execute([$status, $booking_id]);
            $message = 'Booking status updated successfully!';
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = 'Error updating booking: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif (isset($_POST['check_in'])) {
        // Check-in guest using stored procedure
        $booking_id = (int)$_POST['booking_id'];

        try {
            $stmt = $pdo->prepare("CALL check_in_guest(?)");
            $stmt->execute([$booking_id]);
            $message = 'Guest checked in successfully!';
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = 'Error during check-in: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif (isset($_POST['check_out'])) {
        // Check-out guest using stored procedure
        $booking_id = (int)$_POST['booking_id'];

        try {
            $stmt = $pdo->prepare("CALL check_out_guest(?)");
            $stmt->execute([$booking_id]);
            $message = 'Guest checked out successfully!';
            $messageType = 'success';
        } catch(PDOException $e) {
            $message = 'Error during check-out: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$date_filter = $_GET['date'] ?? '';

// Build query with filters
$query = "SELECT b.*, r.room_number, r.room_type, u.first_name, u.last_name, u.email, u.phone
          FROM bookings b
          JOIN rooms r ON b.room_id = r.room_id
          JOIN users u ON b.user_id = u.user_id";

$conditions = [];
$params = [];

if ($status_filter && $status_filter !== 'all') {
    $conditions[] = "b.status = ?";
    $params[] = $status_filter;
}

if ($date_filter) {
    $conditions[] = "DATE(b.check_in_date) = ?";
    $params[] = $date_filter;
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " ORDER BY b.booking_date DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $bookings = $stmt->fetchAll();
} catch(PDOException $e) {
    $bookings = [];
    error_log('Error fetching bookings: ' . $e->getMessage());
}

// Get booking statistics
try {
    $stmt = $pdo->query("SELECT
        COUNT(*) as total,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
        SUM(CASE WHEN status = 'checked_in' THEN 1 ELSE 0 END) as checked_in,
        SUM(CASE WHEN status = 'checked_out' THEN 1 ELSE 0 END) as checked_out,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
        FROM bookings");
    $stats = $stmt->fetch();
} catch(PDOException $e) {
    $stats = ['total' => 0, 'pending' => 0, 'confirmed' => 0, 'checked_in' => 0, 'checked_out' => 0, 'cancelled' => 0];
}

$pageTitle = 'Booking Management';
$currentPage = 'bookings';
require_once 'admin-header.php';
?>

<!-- Bookings Management -->
<div id="bookings" class="tab-content p-6 lg:p-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 tracking-tight">Booking Management</h1>
            <p class="text-gray-500 mt-1">View, manage, and track all hotel bookings.</p>
        </div>
        <div class="flex items-center gap-2 mt-4 sm:mt-0">
                    <a href="add-booking.php" class="btn-primary outline-none flex items-center gap-2 bg-teal-600 hover:bg-teal-700 p-[12px_20px] rounded-lg text-white font-medium shadow-md hover:shadow-lg transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Add New Booking
                    </a>        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <a href="?status=all" class="bg-white rounded-xl shadow-sm p-4 text-center hover:shadow-lg transition-shadow border-l-4 border-gray-300">
            <div class="text-3xl font-bold text-gray-800"><?php echo $stats['total']; ?></div>
            <div class="text-sm font-medium text-gray-500 mt-1">Total Bookings</div>
        </a>
        <a href="?status=pending" class="bg-white rounded-xl shadow-sm p-4 text-center hover:shadow-lg transition-shadow border-l-4 border-yellow-400">
            <div class="text-3xl font-bold text-yellow-700"><?php echo $stats['pending']; ?></div>
            <div class="text-sm font-medium text-yellow-600 mt-1">Pending</div>
        </a>
        <a href="?status=confirmed" class="bg-white rounded-xl shadow-sm p-4 text-center hover:shadow-lg transition-shadow border-l-4 border-blue-400">
            <div class="text-3xl font-bold text-blue-700"><?php echo $stats['confirmed']; ?></div>
            <div class="text-sm font-medium text-blue-600 mt-1">Confirmed</div>
        </a>
        <a href="?status=checked_in" class="bg-white rounded-xl shadow-sm p-4 text-center hover:shadow-lg transition-shadow border-l-4 border-green-400">
            <div class="text-3xl font-bold text-green-700"><?php echo $stats['checked_in']; ?></div>
            <div class="text-sm font-medium text-green-600 mt-1">Checked In</div>
        </a>
        <a href="?status=checked_out" class="bg-white rounded-xl shadow-sm p-4 text-center hover:shadow-lg transition-shadow border-l-4 border-purple-400">
            <div class="text-3xl font-bold text-purple-700"><?php echo $stats['checked_out']; ?></div>
            <div class="text-sm font-medium text-purple-600 mt-1">Checked Out</div>
        </a>
        <a href="?status=cancelled" class="bg-white rounded-xl shadow-sm p-4 text-center hover:shadow-lg transition-shadow border-l-4 border-red-400">
            <div class="text-3xl font-bold text-red-700"><?php echo $stats['cancelled']; ?></div>
            <div class="text-sm font-medium text-red-600 mt-1">Cancelled</div>
        </a>
    </div>

    <!-- Message Display -->
    <?php if ($message): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'error' ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-green-100 text-green-800 border border-green-200'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Bookings Table -->
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <h3 class="text-lg font-semibold text-gray-800 mb-2 sm:mb-0">All Bookings (<?php echo count($bookings); ?>)</h3>
            <div class="flex items-center gap-3">
                <input type="date" id="dateFilter" value="<?php echo htmlspecialchars($date_filter); ?>" class="form-input rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm" placeholder="Filter by date">
                <select id="statusFilter" class="form-select rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500 text-sm">
                    <option value="all" <?php echo $status_filter === '' || $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                    <option value="checked_in" <?php echo $status_filter === 'checked_in' ? 'selected' : ''; ?>>Checked In</option>
                    <option value="checked_out" <?php echo $status_filter === 'checked_out' ? 'selected' : ''; ?>>Checked Out</option>
                    <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Booking ID</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Guest</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Room</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Dates</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php if (empty($bookings)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-12">
                                <div class="flex flex-col items-center justify-center text-gray-500">
                                    <svg class="h-12 w-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    <h3 class="text-lg font-medium text-gray-700">No Bookings Found</h3>
                                    <p class="text-sm mt-1">There are no bookings matching the current filters.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bookings as $booking): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-gray-800">#<?php echo htmlspecialchars(str_pad($booking['booking_id'], 4, '0', STR_PAD_LEFT)); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo htmlspecialchars($booking['booking_code']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900"><?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></div>
                                    <div class="text-gray-500"><?php echo htmlspecialchars($booking['email']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900"><?php echo htmlspecialchars($booking['room_type']); ?></div>
                                    <div class="text-gray-500">No. <?php echo htmlspecialchars($booking['room_number']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900"><?php echo formatDate($booking['check_in_date']); ?></div>
                                    <div class="text-gray-500">to <?php echo formatDate($booking['check_out_date']); ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $status_classes = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'confirmed' => 'bg-blue-100 text-blue-800',
                                        'checked_in' => 'bg-green-100 text-green-800',
                                        'checked_out' => 'bg-purple-100 text-purple-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                    $status_class = $status_classes[strtolower($booking['status'])] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_class; ?>">
                                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $booking['status']))); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-800">
                                    <?php echo formatCurrency($booking['total_amount']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <?php if ($booking['status'] === 'confirmed'): ?>
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                                <button type="submit" name="check_in" class="btn-success text-xs px-3 py-1.5 rounded-md font-semibold bg-green-500 text-white hover:bg-green-600 transition-colors">
                                                    Check In
                                                </button>
                                            </form>
                                        <?php elseif ($booking['status'] === 'checked_in'): ?>
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="booking_id" value="<?php echo $booking['booking_id']; ?>">
                                                <button type="submit" name="check_out" class="btn-primary text-xs px-3 py-1.5 rounded-md font-semibold bg-blue-500 text-white hover:bg-blue-600 transition-colors">
                                                    Check Out
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <select onchange="updateStatus(<?php echo $booking['booking_id']; ?>, this.value)" class="form-select text-xs rounded-md border-gray-300 shadow-sm focus:border-teal-500 focus:ring-teal-500">
                                            <option disabled <?php echo !in_array($booking['status'], ['pending', 'confirmed', 'cancelled']) ? 'selected' : ''; ?>>Update...</option>
                                            <option value="pending" <?php echo $booking['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="confirmed" <?php echo $booking['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                            <option value="cancelled" <?php echo $booking['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function updateStatus(bookingId, status) {
    if (confirm('Are you sure you want to update this booking status to "' + status + '"?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        form.innerHTML = `
            <input type="hidden" name="booking_id" value="${bookingId}">
            <input type="hidden" name="status" value="${status}">
            <input type="hidden" name="update_status" value="1">
        `;
        document.body.appendChild(form);
        form.submit();
    } else {
        // Reload the page to reset the dropdown to its original state
        location.reload();
    }
}

// Combined filter functionality
function applyFilters() {
    const status = document.getElementById('statusFilter').value;
    const date = document.getElementById('dateFilter').value;
    const url = new URL(window.location);
    
    if (status === 'all') {
        url.searchParams.delete('status');
    } else {
        url.searchParams.set('status', status);
    }

    if (date) {
        url.searchParams.set('date', date);
    } else {
        url.searchParams.delete('date');
    }
    
    window.location.href = url.toString();
}

document.getElementById('statusFilter').addEventListener('change', applyFilters);
document.getElementById('dateFilter').addEventListener('change', applyFilters);
</script>

<?php require_once 'admin-footer.php'; ?>