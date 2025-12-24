<?php
require_once 'config/database.php';
include 'header.php';

$message = '';
$messageType = '';

// Retrieve parameters from Flutterwave callback
$booking_id = $_GET['booking_id'] ?? null;
$expected_amount = $_GET['amount'] ?? null;
$transaction_id = $_GET['transaction_id'] ?? null; // Flutterwave's transaction ID
$tx_ref = $_GET['tx_ref'] ?? null; // Our internal transaction reference (booking code)
$flw_status = $_GET['status'] ?? 'unknown'; // Payment status from Flutterwave

// Flutterwave Secret Key (PLACEHOLDER - REPLACE WITH YOUR ACTUAL SECRET KEY)
// NEVER expose your secret key in client-side code. This should be securely stored.
$FLUTTERWAVE_SECRET_KEY = 'FLWSECK_TEST-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX-X';

if (!$booking_id || !$expected_amount || !$tx_ref) {
    $message = 'Invalid payment callback. Missing essential parameters.';
    $messageType = 'error';
} else {
    try {
        // First, check if the booking exists and is unpaid
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE booking_id = :booking_id AND booking_code = :tx_ref AND payment_status = 'unpaid'");
        $stmt->execute([':booking_id' => $booking_id, ':tx_ref' => $tx_ref]);
        $booking = $stmt->fetch();

        if (!$booking) {
            $message = 'Booking not found or already processed.';
            $messageType = 'error';
        } else {
            // Verify payment with Flutterwave (server-to-server call)
            // This is crucial to prevent fraud and ensure payment legitimacy
            $curl = curl_init();
            $url = "https://api.flutterwave.com/v3/transactions/" . $transaction_id . "/verify";

            curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/json",
                    "Authorization: Bearer " . $FLUTTERWAVE_SECRET_KEY
                ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                error_log("cURL Error #:" . $err);
                $message = "Payment verification failed due to network error. Please contact support.";
                $messageType = 'error';
            } else {
                $res = json_decode($response);

                if ($res && $res->status === 'success') {
                    $transaction_data = $res->data;

                    // Check if transaction was successful
                    if ($transaction_data->status === 'successful') {
                        // Check transaction amount against expected amount
                        if ($transaction_data->amount >= $expected_amount) {
                            // Update booking status and insert payment record
                            $pdo->beginTransaction();
                            try {
                                $update_booking_stmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed', payment_status = 'paid' WHERE booking_id = :booking_id");
                                $update_booking_stmt->execute([':booking_id' => $booking_id]);

                                $insert_payment_stmt = $pdo->prepare("INSERT INTO payments (booking_id, user_id, payment_amount, payment_method, payment_gateway, payment_status, transaction_id, reference_number, flutterwave_tx_ref, flutterwave_transaction_id)
                                                                       VALUES (:booking_id, :user_id, :payment_amount, 'card', 'flutterwave', 'completed', :transaction_id, :tx_ref, :flutterwave_tx_ref, :flutterwave_transaction_id)");
                                $insert_payment_stmt->execute([
                                    ':booking_id' => $booking_id,
                                    ':user_id' => $booking['user_id'], // Get user_id from the booking record
                                    ':payment_amount' => $transaction_data->amount,
                                    ':transaction_id' => $transaction_data->id,
                                    ':tx_ref' => $transaction_data->tx_ref,
                                    ':flutterwave_tx_ref' => $transaction_data->flw_ref,
                                    ':flutterwave_transaction_id' => $transaction_data->id
                                ]);
                                $pdo->commit();

                                $message = 'Payment successful and booking confirmed! Your booking code is ' . htmlspecialchars($tx_ref);
                                $messageType = 'success';
                            } catch (PDOException $e) {
                                $pdo->rollBack();
                                error_log("Database error updating booking/payment: " . $e->getMessage());
                                $message = 'Payment verified, but there was an issue updating your booking. Please contact support with your booking code ' . htmlspecialchars($tx_ref);
                                $messageType = 'error';
                            }
                        } else {
                            // Amount mismatch - possible fraud or error
                            $message = 'Payment amount mismatch. Please contact support.';
                            $messageType = 'error';
                            // Optionally update booking status to 'amount_mismatch'
                        }
                    } else {
                        // Transaction not successful on Flutterwave's side
                        $message = 'Payment failed or was not successful (' . htmlspecialchars($transaction_data->status) . '). Please try again.';
                        $messageType = 'error';
                    }
                } else {
                    $message = 'Payment verification failed. Could not reach Flutterwave or invalid response.';
                    $messageType = 'error';
                }
            }
        }
    } catch (PDOException $e) {
        error_log("Error in payment callback: " . $e->getMessage());
        $message = 'An internal error occurred during payment processing. Please contact support.';
        $messageType = 'error';
    }
}

?>

<section class="py-12 bg-gray-50 min-h-[60vh] flex items-center justify-center">
    <div class="container mx-auto px-4 lg:px-8">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-xl p-8 text-center">
            <?php if ($messageType === 'success'): ?>
                <svg class="mx-auto h-16 w-16 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h2 class="text-2xl font-bold text-gray-800 mt-4 mb-2">Payment Successful!</h2>
                <p class="text-gray-600 mb-6"><?= htmlspecialchars($message) ?></p>
                <a href="account.php" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                    View My Bookings
                </a>
            <?php else: ?>
                <svg class="mx-auto h-16 w-16 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h2 class="text-2xl font-bold text-gray-800 mt-4 mb-2">Payment Failed!</h2>
                <p class="text-gray-600 mb-6"><?= htmlspecialchars($message) ?></p>
                <a href="room.php" class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                    Try Again
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>