<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';
include 'header.php';

$status = sanitize($_GET['status'] ?? '');
$tx_ref = sanitize($_GET['tx_ref'] ?? '');
$transaction_id = sanitize($_GET['transaction_id'] ?? '');
$totalAmount = filter_input(INPUT_GET, 'total_amount', FILTER_VALIDATE_FLOAT);
$orderId = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);

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
        if ($verifiedAmount >= $totalAmount && $verifiedCurrency === 'NGN' && $orderId) {
            // Payment is verified and successful
            $messageType = 'success';
            $message = 'Payment successful! Your order has been confirmed.';

            try {
                $pdo->beginTransaction();

                // Update the existing order status and payment status
                $stmt = $pdo->prepare("
                    UPDATE food_orders
                    SET order_status = 'confirmed', payment_status = 'paid'
                    WHERE order_id = ? AND payment_status = 'unpaid'
                ");
                $stmt->execute([$orderId]);

                // Insert payment record
                $stmt = $pdo->prepare("
                    INSERT INTO payments (
                        order_id, payment_amount, payment_method, payment_gateway,
                        payment_status, transaction_id, reference_number,
                        flutterwave_tx_ref, flutterwave_transaction_id, payment_date
                    ) VALUES (?, ?, 'card', 'flutterwave', 'completed', ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $orderId,
                    $verifiedAmount,
                    $transaction_id,
                    $responseData['data']['flw_ref'] ?? $tx_ref,
                    $tx_ref,
                    $transaction_id
                ]);

                $pdo->commit();

                // Clear the cart session after successful payment
                unset($_SESSION['cart']);

                $message = 'Payment successful! Your order #' . $orderId . ' has been confirmed and is being prepared.';

            } catch (PDOException $e) {
                $pdo->rollBack();
                error_log("Database error during payment callback: " . $e->getMessage());
                $message = 'Payment was successful, but there was an error updating your order. Please contact support with order ID: ' . $orderId;
                $messageType = 'error';
            }
        } else {
            $message = 'Payment verification failed: Amount or currency mismatch, or invalid order.';
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