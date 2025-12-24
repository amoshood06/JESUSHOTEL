<?php
require_once '../config/database.php';

// Check if user is logged in and is admin/staff
if (!isLoggedIn() || !isStaff()) {
    redirect('../login.php');
}

// Handle food order operations
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        // Update order status
        $order_id = (int)$_POST['order_id'];
        $status = sanitize($_POST['status']);
        $notes = sanitize($_POST['notes'] ?? '');

        try {
            $pdo->beginTransaction();

            // Update order status
            $stmt = $pdo->prepare("UPDATE food_orders SET status = ?, updated_at = NOW() WHERE order_id = ?");
            $stmt->execute([$status, $order_id]);

            // Add status update note if provided
            if ($notes) {
                $stmt = $pdo->prepare("INSERT INTO order_notes (order_id, note_type, notes, created_by) VALUES (?, 'status_update', ?, ?)");
                $stmt->execute([$order_id, $notes, $_SESSION['user_id']]);
            }

            $pdo->commit();
            $message = 'Order status updated successfully!';
            $messageType = 'success';
        } catch(PDOException $e) {
            $pdo->rollBack();
            $message = 'Error updating order status: ' . $e->getMessage();
            $messageType = 'error';
        }
    } elseif (isset($_POST['cancel_order'])) {
        // Cancel order
        $order_id = (int)$_POST['order_id'];
        $cancel_reason = sanitize($_POST['cancel_reason']);

        try {
            $pdo->beginTransaction();

            // Update order status to cancelled
            $stmt = $pdo->prepare("UPDATE food_orders SET status = 'cancelled', updated_at = NOW() WHERE order_id = ?");
            $stmt->execute([$order_id]);

            // Add cancellation note
            $stmt = $pdo->prepare("INSERT INTO order_notes (order_id, note_type, notes, created_by) VALUES (?, 'cancellation', ?, ?)");
            $stmt->execute([$order_id, $cancel_reason, $_SESSION['user_id']]);

            $pdo->commit();
            $message = 'Order cancelled successfully!';
            $messageType = 'success';
        } catch(PDOException $e) {
            $pdo->rollBack();
            $message = 'Error cancelling order: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Get filter parameters
$status_filter = $_GET['status'] ?? '';
$date_filter = $_GET['date'] ?? '';
$search_filter = $_GET['search'] ?? '';

// Build query with filters
$query = "SELECT fo.*, CONCAT(u.first_name, ' ', u.last_name) as customer_name, u.phone, u.email,
          GROUP_CONCAT(CONCAT(fm.item_name, ' (', oi.quantity, ')') SEPARATOR ', ') as items
          FROM food_orders fo
          LEFT JOIN users u ON fo.user_id = u.user_id
          LEFT JOIN order_items oi ON fo.order_id = oi.order_id
          LEFT JOIN food_menu fm ON oi.menu_item_id = fm.menu_item_id";

$conditions = [];
$params = [];

if ($status_filter && $status_filter !== 'all') {
    $conditions[] = "fo.status = ?";
    $params[] = $status_filter;
}

if ($date_filter) {
    $conditions[] = "DATE(fo.created_at) = ?";
    $params[] = $date_filter;
}

if ($search_filter) {
    $conditions[] = "(CONCAT(u.first_name, ' ', u.last_name) LIKE ? OR u.email LIKE ? OR fo.order_id = ? OR fo.delivery_type LIKE ?)";
    $params[] = "%$search_filter%";
    $params[] = "%$search_filter%";
    $params[] = $search_filter;
    $params[] = "%$search_filter%";
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$query .= " GROUP BY fo.order_id ORDER BY fo.created_at DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();
} catch(PDOException $e) {
    $orders = [];
    error_log('Error fetching food orders: ' . $e->getMessage());
}

// Get order statistics
try {
    $stmt = $pdo->query("SELECT
        COUNT(*) as total,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'preparing' THEN 1 ELSE 0 END) as preparing,
        SUM(CASE WHEN status = 'ready' THEN 1 ELSE 0 END) as ready,
        SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
        SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
        SUM(total_amount) as total_revenue
        FROM food_orders");
    $stats = $stmt->fetch();
} catch(PDOException $e) {
    $stats = ['total' => 0, 'pending' => 0, 'preparing' => 0, 'ready' => 0, 'delivered' => 0, 'cancelled' => 0, 'total_revenue' => 0];
}

$pageTitle = 'Food Orders Management';
$currentPage = 'orders';
require_once 'admin-header.php';
?>

<!-- Food Orders Management -->
<div id="orders" class="tab-content p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Food Orders Management</h1>
            <p class="text-gray-600">Manage restaurant orders and track delivery status</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4 text-center">
            <div class="text-2xl font-bold text-gray-900"><?php echo $stats['total']; ?></div>
            <div class="text-sm text-gray-600">Total Orders</div>
        </div>
        <div class="bg-yellow-50 rounded-lg shadow p-4 text-center border-l-4 border-yellow-400">
            <div class="text-2xl font-bold text-yellow-700"><?php echo $stats['pending']; ?></div>
            <div class="text-sm text-yellow-600">Pending</div>
        </div>
        <div class="bg-blue-50 rounded-lg shadow p-4 text-center border-l-4 border-blue-400">
            <div class="text-2xl font-bold text-blue-700"><?php echo $stats['preparing']; ?></div>
            <div class="text-sm text-blue-600">Preparing</div>
        </div>
        <div class="bg-green-50 rounded-lg shadow p-4 text-center border-l-4 border-green-400">
            <div class="text-2xl font-bold text-green-700"><?php echo $stats['ready']; ?></div>
            <div class="text-sm text-green-600">Ready</div>
        </div>
        <div class="bg-purple-50 rounded-lg shadow p-4 text-center border-l-4 border-purple-400">
            <div class="text-2xl font-bold text-purple-700"><?php echo $stats['delivered']; ?></div>
            <div class="text-sm text-purple-600">Delivered</div>
        </div>
        <div class="bg-red-50 rounded-lg shadow p-4 text-center border-l-4 border-red-400">
            <div class="text-2xl font-bold text-red-700"><?php echo $stats['cancelled']; ?></div>
            <div class="text-sm text-red-600">Cancelled</div>
        </div>
        <div class="bg-teal-50 rounded-lg shadow p-4 text-center border-l-4 border-teal-400">
            <div class="text-2xl font-bold text-teal-700"><?php echo formatCurrency($stats['total_revenue']); ?></div>
            <div class="text-sm text-teal-600">Revenue</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-wrap gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="statusFilter" class="form-select">
                    <option value="all" <?php echo $status_filter === '' || $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                    <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="preparing" <?php echo $status_filter === 'preparing' ? 'selected' : ''; ?>>Preparing</option>
                    <option value="ready" <?php echo $status_filter === 'ready' ? 'selected' : ''; ?>>Ready</option>
                    <option value="delivered" <?php echo $status_filter === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                    <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" id="dateFilter" class="form-input" value="<?php echo $date_filter; ?>">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" id="searchFilter" class="form-input" placeholder="Name, room, or order ID" value="<?php echo htmlspecialchars($search_filter); ?>">
            </div>
        </div>
    </div>

    <!-- Message Display -->
    <?php if ($message): ?>
        <div class="mb-6 p-4 rounded-lg <?php echo $messageType === 'error' ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-green-50 text-green-700 border border-green-200'; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Orders (<?php echo count($orders); ?>)</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Delivery Type</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Order Time</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-500">
                                <svg class="h-12 w-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                No orders found. Orders will appear here when customers place them.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td class="font-medium">#<?php echo $order['order_id']; ?></td>
                                <td>
                                    <div>
                                        <div class="font-medium"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($order['phone']); ?></div>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($order['delivery_type'] ?? 'N/A'); ?></td>
                                <td>
                                    <div class="max-w-xs truncate" title="<?php echo htmlspecialchars($order['items']); ?>">
                                        <?php echo htmlspecialchars($order['items']); ?>
                                    </div>
                                </td>
                                <td class="font-medium"><?php echo formatCurrency($order['total_amount']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="text-sm">
                                        <div><?php echo date('M d, H:i', strtotime($order['created_at'])); ?></div>
                                        <?php if ($order['updated_at'] && $order['updated_at'] !== $order['created_at']): ?>
                                            <div class="text-gray-500">Updated: <?php echo date('M d, H:i', strtotime($order['updated_at'])); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex space-x-2">
                                        <button onclick="updateStatus(<?php echo $order['order_id']; ?>, '<?php echo $order['status']; ?>')" class="btn-secondary text-xs px-3 py-1">
                                            Update Status
                                        </button>
                                        <?php if ($order['status'] !== 'cancelled' && $order['status'] !== 'delivered'): ?>
                                            <button onclick="cancelOrder(<?php echo $order['order_id']; ?>)" class="btn-danger text-xs px-3 py-1">
                                                Cancel
                                            </button>
                                        <?php endif; ?>
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

<!-- Update Status Modal -->
<div id="updateStatusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Update Order Status</h3>
                <button onclick="closeUpdateStatusModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="order_id" id="status_order_id">
                <div>
                    <label class="block text-sm font-medium text-gray-700">New Status</label>
                    <select name="status" id="status_select" required class="form-select">
                        <option value="pending">Pending</option>
                        <option value="preparing">Preparing</option>
                        <option value="ready">Ready for Pickup/Delivery</option>
                        <option value="delivered">Delivered</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Notes (Optional)</label>
                    <textarea name="notes" rows="3" class="form-textarea" placeholder="Add any notes about this status update..."></textarea>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeUpdateStatusModal()" class="btn-secondary">Cancel</button>
                    <button type="submit" name="update_status" class="btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div id="cancelOrderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Cancel Order</h3>
                <button onclick="closeCancelOrderModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="order_id" id="cancel_order_id">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Cancellation Reason</label>
                    <textarea name="cancel_reason" required rows="3" class="form-textarea" placeholder="Please provide a reason for cancellation..."></textarea>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                    <div class="flex">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">This action cannot be undone. The order will be marked as cancelled.</p>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeCancelOrderModal()" class="btn-secondary">Keep Order</button>
                    <button type="submit" name="cancel_order" class="btn-danger">Cancel Order</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateStatus(orderId, currentStatus) {
    document.getElementById('status_order_id').value = orderId;
    document.getElementById('status_select').value = currentStatus;
    document.getElementById('updateStatusModal').classList.remove('hidden');
}

function closeUpdateStatusModal() {
    document.getElementById('updateStatusModal').classList.add('hidden');
}

function cancelOrder(orderId) {
    document.getElementById('cancel_order_id').value = orderId;
    document.getElementById('cancelOrderModal').classList.remove('hidden');
}

function closeCancelOrderModal() {
    document.getElementById('cancelOrderModal').classList.add('hidden');
}

// Filter functionality
document.getElementById('statusFilter').addEventListener('change', function() {
    const status = this.value;
    const date = document.getElementById('dateFilter').value;
    const search = document.getElementById('searchFilter').value;
    const url = new URL(window.location);
    if (status === 'all') {
        url.searchParams.delete('status');
    } else {
        url.searchParams.set('status', status);
    }
    if (date) url.searchParams.set('date', date);
    else url.searchParams.delete('date');
    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');
    window.location.href = url.toString();
});

document.getElementById('dateFilter').addEventListener('change', function() {
    const date = this.value;
    const status = document.getElementById('statusFilter').value;
    const search = document.getElementById('searchFilter').value;
    const url = new URL(window.location);
    if (status !== 'all') url.searchParams.set('status', status);
    else url.searchParams.delete('status');
    if (date) url.searchParams.set('date', date);
    else url.searchParams.delete('date');
    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');
    window.location.href = url.toString();
});

document.getElementById('searchFilter').addEventListener('input', function() {
    const search = this.value;
    const status = document.getElementById('statusFilter').value;
    const date = document.getElementById('dateFilter').value;
    const url = new URL(window.location);
    if (status !== 'all') url.searchParams.set('status', status);
    else url.searchParams.delete('status');
    if (date) url.searchParams.set('date', date);
    else url.searchParams.delete('date');
    if (search) url.searchParams.set('search', search);
    else url.searchParams.delete('search');
    window.location.href = url.toString();
});
</script>

<?php require_once 'admin-footer.php'; ?>