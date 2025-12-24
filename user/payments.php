<?php
include_once 'user-header.php';

$user_id = $currentUser['user_id'];
$payments = [];

if ($user_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT payment_id, booking_id, order_id, payment_amount, payment_method, payment_date, payment_status, transaction_id
            FROM payments
            WHERE user_id = ?
            ORDER BY payment_date DESC
        ");
        $stmt->execute([$user_id]);
        $payments = $stmt->fetchAll();
    } catch (PDOException $e) {
        echo "<p class='text-red-500'>Error fetching payment history: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
?>

<h1 class="text-3xl font-bold text-gray-800 mb-6">My Payment History</h1>

<div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
    <div class="p-6 border-b border-gray-200">
        <h3 class="text-lg font-bold text-gray-900">Payment Transactions</h3>
    </div>
    <div class="overflow-x-auto">
        <?php if (!empty($payments)): ?>
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Payment ID</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Method</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Transaction ID</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php foreach ($payments as $payment): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm font-medium text-gray-900"><?php echo htmlspecialchars($payment['payment_id']); ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo formatCurrency($payment['payment_amount']); ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $payment['payment_method']))); ?></td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo formatDateTime($payment['payment_date']); ?></td>
                    <td class="px-6 py-4">
                        <?php
                            $status_class = '';
                            switch ($payment['payment_status']) {
                                case 'completed':
                                    $status_class = 'bg-green-100 text-green-800';
                                    break;
                                case 'pending':
                                    $status_class = 'bg-yellow-100 text-yellow-800';
                                    break;
                                case 'failed':
                                    $status_class = 'bg-red-100 text-red-800';
                                    break;
                                case 'refunded':
                                    $status_class = 'bg-blue-100 text-blue-800';
                                    break;
                                default:
                                    $status_class = 'bg-gray-100 text-gray-800';
                                    break;
                            }
                        ?>
                        <span class="<?php echo $status_class; ?> px-3 py-1 rounded-full text-xs font-semibold">
                            <?php echo htmlspecialchars(ucfirst($payment['payment_status'])); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($payment['transaction_id'] ?? 'N/A'); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p class="p-6 text-gray-600">You have no payment transactions yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php
include_once 'user-footer.php';
?>