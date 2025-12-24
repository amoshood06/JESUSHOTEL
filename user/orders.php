<?php
include_once 'user-header.php';

// Fetch orders for the current user
$user_id = $currentUser['user_id'];
$orders = [];
if ($user_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT fo.order_id, fo.order_code, fo.order_date, fo.total_amount, fo.order_status
            FROM food_orders fo
            WHERE fo.user_id = ?
            ORDER BY fo.order_date DESC
        ");
        $stmt->execute([$user_id]);
        $orders = $stmt->fetchAll();
    } catch (PDOException $e) {
        // Log error or display a user-friendly message
        echo "<p class='text-red-500'>Error fetching orders: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">My Food and Drinks Orders</h1>

<div class="bg-white rounded-lg border border-gray-200 p-6">
    <h3 class="text-lg font-bold text-gray-900 mb-4">Order History</h3>
    <?php if (!empty($orders)): ?>
    <div class="overflow-x-auto">
        <table class="w-full mt-4">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Order Code</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                <tr>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($order['order_code']); ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo formatDateTime($order['order_date']); ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        <?php
                        // Fetch items for this order
                        $item_list = [];
                        try {
                            $item_stmt = $pdo->prepare("
                                SELECT fm.item_name, oi.quantity
                                FROM order_items oi
                                JOIN food_menu fm ON oi.menu_item_id = fm.menu_item_id
                                WHERE oi.order_id = ?
                            ");
                            $item_stmt->execute([$order['order_id']]);
                            $items = $item_stmt->fetchAll();
                            foreach ($items as $item) {
                                $item_list[] = htmlspecialchars($item['item_name']) . " (x" . htmlspecialchars($item['quantity']) . ")";
                            }
                            echo implode(', ', $item_list);
                        } catch (PDOException $e) {
                            echo "Error fetching items.";
                        }
                        ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo formatCurrency($order['total_amount']); ?></td>
                    <td class="px-6 py-4">
                        <?php
                            $status_class = '';
                            switch ($order['order_status']) {
                                case 'pending':
                                    $status_class = 'bg-yellow-100 text-yellow-800';
                                    break;
                                case 'confirmed':
                                case 'preparing':
                                    $status_class = 'bg-blue-100 text-blue-800';
                                    break;
                                case 'ready':
                                case 'delivered':
                                    $status_class = 'bg-green-100 text-green-800';
                                    break;
                                case 'cancelled':
                                    $status_class = 'bg-red-100 text-red-800';
                                    break;
                                default:
                                    $status_class = 'bg-gray-100 text-gray-800';
                                    break;
                            }
                        ?>
                        <span class="<?php echo $status_class; ?> px-3 py-1 rounded-full text-xs font-semibold">
                            <?php echo htmlspecialchars(ucfirst($order['order_status'])); ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <p class="p-6 text-gray-600">You have no food or drink orders yet.</p>
    <?php endif; ?>
</div>

<?php
include_once 'user-footer.php';
?>