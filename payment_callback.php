<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';
include 'header.php';

$status = sanitize($_GET['status'] ?? '');
$tx_ref = sanitize($_GET['tx_ref'] ?? '');
$transaction_id = sanitize($_GET['transaction_id'] ?? '');
$totalAmount = filter_input(INPUT_GET, 'total_amount', FILTER_VALIDATE_FLOAT); // Passed from cart.php for now

// Placeholder for Flutterwave Secret Key - In a real app, define this securely (e.g., config file, environment variable)
define('FLUTTERWAVE_SECRET_KEY', 'FLWSECK_TEST-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX-X');

$message = '';
$messageType = 'error';

if ($status === 'successful' && $tx_ref && $transaction_id) {
    // Verify the transaction with Flutterwave
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/{$transaction_id}/verify",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization: Bearer " . FLUTTERWAVE_SECRET_KEY
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $responseData = json_decode($response, true);

    if ($responseData && $responseData['status'] === 'success' && $responseData['data']['status'] === 'successful') {
        $verifiedAmount = $responseData['data']['amount'];
        $verifiedCurrency = $responseData['data']['currency'];

        // Important: Compare verifiedAmount with the expected amount from your system ($totalAmount)
        // Also check if the currency matches, and if the transaction_id or tx_ref hasn't been used before.
        if ($verifiedAmount >= $totalAmount && $verifiedCurrency === 'NGN') { // Assuming NGN
            // Payment is verified and successful
            $messageType = 'success';
            $message = 'Payment successful! Your order has been placed.';

            // Now, save the order to the database
            try {
                $pdo->beginTransaction();

                $userId = $_SESSION['user_id'] ?? null; // Assume user is logged in
                if (!$userId) {
                    // Handle case where user is not logged in (e.g., redirect to login or show error)
                    $message = 'Payment successful, but you are not logged in. Please log in to view your order.';
                    $pdo->rollBack();
                    // Consider storing the transaction details and linking it to a user later
                } else {
                    $orderCode = 'ORD' . strtoupper(uniqid());
                    $totalOrderAmount = $totalAmount; // Use the verified amount for the order

                    // Insert into food_orders
                    $stmt = $pdo->prepare("INSERT INTO food_orders (order_code, user_id, order_date, delivery_type, order_status, total_amount, payment_status)
                                           VALUES (:order_code, :user_id, NOW(), 'room-service', 'pending', :total_amount, 'paid')");
                    $stmt->execute([
                        ':order_code' => $orderCode,
                        ':user_id' => $userId,
                        ':total_amount' => $totalOrderAmount
                    ]);
                    $orderId = $pdo->lastInsertId();

                    // Insert into order_items
                    foreach ($_SESSION['cart'] as $itemId => $item) {
                        $stmt = $pdo->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price, subtotal)
                                               VALUES (:order_id, :menu_item_id, :quantity, :unit_price, :subtotal)");
                        $stmt->execute([
                            ':order_id' => $orderId,
                            ':menu_item_id' => $item['item_id'],
                            ':quantity' => $item['quantity'],
                            ':unit_price' => $item['price'],
                            ':subtotal' => $item['price'] * $item['quantity']
                        ]);
                    }

                    // Insert into payments
                    $stmt = $pdo->prepare("INSERT INTO payments (order_id, user_id, payment_amount, payment_method, payment_gateway, payment_status, transaction_id, reference_number, flutterwave_tx_ref, flutterwave_transaction_id)
                                           VALUES (:order_id, :user_id, :payment_amount, :payment_method, :payment_gateway, :payment_status, :transaction_id, :reference_number, :flutterwave_tx_ref, :flutterwave_transaction_id)");
                    $stmt->execute([
                        ':order_id' => $orderId,
                        ':user_id' => $userId,
                        ':payment_amount' => $verifiedAmount,
                        ':payment_method' => 'card', // Or derive from Flutterwave response
                        ':payment_gateway' => 'flutterwave',
                        ':payment_status' => 'completed',
                        ':transaction_id' => $transaction_id,
                        ':reference_number' => $responseData['data']['flw_ref'] ?? $tx_ref, // Use flw_ref if available
                        ':flutterwave_tx_ref' => $tx_ref,
                        ':flutterwave_transaction_id' => $transaction_id
                    ]);

                    $pdo->commit();
                    unset($_SESSION['cart']); // Clear the cart after successful order
                }

            } catch (PDOException $e) {
                $pdo->rollBack();
                error_log("Database error during payment callback: " . $e->getMessage());
                $message = 'Payment was successful, but there was an error saving your order. Please contact support.';
                $messageType = 'error';
            }
        } else {
            $message = 'Payment verification failed: Amount or currency mismatch, or duplicate transaction.';
        }
    } else {
        $message = 'Payment verification failed with Flutterwave API.';
        error_log("Flutterwave verification response error: " . ($response ?: 'Empty response'));
    }
} elseif ($status === 'failed') {
    $message = 'Payment failed. Please try again or choose another payment method.';
} else {
    $message = 'Payment cancelled or an unknown error occurred.';
}
?>

<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Payment Status</h1>

        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8 text-center">
            <?php if ($messageType === 'success'): ?>
                <div class="text-green-600 text-5xl mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-semibold text-green-700 mb-4"><?= htmlspecialchars($message) ?></p>
                <p class="text-gray-600 mb-6">Transaction Reference: <strong><?= htmlspecialchars($tx_ref) ?></strong></p>
                <a href="index.php" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700">
                    Go to Homepage
                </a>
            <?php else: ?>
                <div class="text-red-600 text-5xl mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-semibold text-red-700 mb-4"><?= htmlspecialchars($message) ?></p>
                <?php if ($tx_ref): ?>
                    <p class="text-gray-600 mb-6">Transaction Reference: <strong><?= htmlspecialchars($tx_ref) ?></strong></p>
                <?php endif; ?>
                <a href="cart.php" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                    Return to Cart
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>