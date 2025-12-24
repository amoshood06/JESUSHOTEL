<?php
include_once 'user-header.php';

// Fetch bookings for the current user
$user_id = $currentUser['user_id'];
$bookings = [];
if ($user_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT b.booking_id, b.booking_code, r.room_type, b.check_in_date, b.check_out_date, b.status
            FROM bookings b
            JOIN rooms r ON b.room_id = r.room_id
            WHERE b.user_id = ?
            ORDER BY b.booking_date DESC
        ");
        $stmt->execute([$user_id]);
        $bookings = $stmt->fetchAll();
    } catch (PDOException $e) {
        // Log error or display a user-friendly message
        echo "<p class='text-red-500'>Error fetching bookings: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">My Bookings History</h1>

<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-900">My Bookings</h3>
    </div>
    <div class="overflow-x-auto">
        <?php if (!empty($bookings)): ?>
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Booking Code</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Room Type</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Check-in</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Check-out</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($bookings as $booking): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($booking['booking_code']); ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($booking['room_type']); ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo formatDate($booking['check_in_date']); ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo formatDate($booking['check_out_date']); ?></td>
                    <td class="px-6 py-4">
                        <?php
                            $status_class = '';
                            switch ($booking['status']) {
                                case 'confirmed':
                                    $status_class = 'bg-green-100 text-green-800';
                                    break;
                                case 'pending':
                                    $status_class = 'bg-yellow-100 text-yellow-800';
                                    break;
                                case 'cancelled':
                                    $status_class = 'bg-red-100 text-red-800';
                                    break;
                                case 'checked_in':
                                    $status_class = 'bg-blue-100 text-blue-800';
                                    break;
                                case 'checked_out':
                                    $status_class = 'bg-gray-100 text-gray-800';
                                    break;
                                default:
                                    $status_class = 'bg-gray-100 text-gray-800';
                                    break;
                            }
                        ?>
                        <span class="<?php echo $status_class; ?> px-3 py-1 rounded-full text-xs font-semibold">
                            <?php echo htmlspecialchars(ucfirst($booking['status'])); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="print_receipt.php?booking_id=<?php echo htmlspecialchars($booking['booking_id']); ?>" target="_blank" class="text-teal-600 hover:text-teal-700 font-medium text-sm mr-2">Print Receipt</a>
                        <button class="text-teal-600 hover:text-teal-700 font-medium text-sm">View</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p class="p-6 text-gray-600">You have no bookings yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php
include_once 'user-footer.php';
?>