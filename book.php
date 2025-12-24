<?php
require_once 'config/database.php';
include 'header.php';

// Item 2: Implement user login check
if (!isLoggedIn()) {
    redirect('login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$room = null;
$room_id = $_GET['room_id'] ?? null;

// Item 3: Fetch and display selected room details
if ($room_id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM rooms WHERE room_id = :room_id AND status = 'available'");
        $stmt->execute([':room_id' => $room_id]);
        $room = $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Error fetching room details: " . $e->getMessage());
        // Handle error gracefully
    }
}

if (!$room) {
    echo "<section class='py-12 bg-gray-50'><div class='container mx-auto px-4 lg:px-8'><p class='text-center text-red-500 text-lg'>Room not found or not available.</p></div></section>";
    include 'footer.php';
    exit();
}

$check_in_date = $_POST['check_in_date'] ?? '';
$check_out_date = $_POST['check_out_date'] ?? '';
$number_of_guests = $_POST['number_of_guests'] ?? 1;
$special_requests = $_POST['special_requests'] ?? '';

$message = '';
$messageType = '';

// Handle booking form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_room'])) {
    $check_in_date = sanitize($_POST['check_in_date']);
    $check_out_date = sanitize($_POST['check_out_date']);
    $number_of_guests = (int)sanitize($_POST['number_of_guests']);
    $special_requests = sanitize($_POST['special_requests']);
    $user_id = $_SESSION['user_id'];

    // Basic validation
    if (empty($check_in_date) || empty($check_out_date) || $number_of_guests < 1) {
        $message = 'Please fill in all required booking details.';
        $messageType = 'error';
    } elseif (strtotime($check_in_date) >= strtotime($check_out_date)) {
        $message = 'Check-out date must be after check-in date.';
        $messageType = 'error';
    } elseif ($number_of_guests > $room['capacity']) {
        $message = 'Number of guests exceeds room capacity (' . $room['capacity'] . ').';
        $messageType = 'error';
    } else {
        // Calculate total nights and amount
        $check_in_obj = new DateTime($check_in_date);
        $check_out_obj = new DateTime($check_out_date);
        $interval = $check_in_obj->diff($check_out_obj);
        $total_nights = $interval->days;

        if ($total_nights <= 0) {
            $message = 'Booking must be for at least one night.';
            $messageType = 'error';
        } else {
            $total_amount = $total_nights * $room['price_per_night'];

            // Placeholder for Flutterwave payment integration
            // In a real scenario, you'd initiate Flutterwave payment here,
            // get a transaction reference, and then proceed.

            // For now, let's just simulate a booking and set payment status to unpaid
            try {
                // Generate a simple booking code
                $booking_code = 'BK' . strtoupper(uniqid());

                $stmt = $pdo->prepare("INSERT INTO bookings (booking_code, user_id, room_id, check_in_date, check_out_date, number_of_guests, total_nights, total_amount, status, payment_status, special_requests)
                                       VALUES (:booking_code, :user_id, :room_id, :check_in_date, :check_out_date, :number_of_guests, :total_nights, :total_amount, 'pending', 'unpaid', :special_requests)");
                $stmt->execute([
                    ':booking_code' => $booking_code,
                    ':user_id' => $user_id,
                    ':room_id' => $room['room_id'],
                    ':check_in_date' => $check_in_date,
                    ':check_out_date' => $check_out_date,
                    ':number_of_guests' => $number_of_guests,
                    ':total_nights' => $total_nights,
                    ':total_amount' => $total_amount,
                    ':special_requests' => $special_requests
                ]);

                $booking_id = $pdo->lastInsertId();

                $message = 'Booking initiated successfully! Proceeding to payment gateway...';
                $messageType = 'success';

                // Redirect to Flutterwave for payment, passing necessary details
                // This is a simplified example. You would typically do this server-side
                // and then redirect the user or pass info to JS.
                $callback_url = 'http://localhost/Jesus/payment_callback.php?booking_id=' . $booking_id . '&amount=' . $total_amount; // Replace with actual domain
                $flutterwave_public_key = 'FLWPUBK_TEST-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX-X'; // Replace with your actual public key

                echo '<script src="https://checkout.flutterwave.com/v3.js"></script>';
                echo '<script>
                    function makePayment() {
                        FlutterwaveCheckout({
                            public_key: "' . $flutterwave_public_key . '",
                            tx_ref: "' . $booking_code . '",
                            amount: ' . $total_amount . ',
                            currency: "NGN", // Assuming Nigerian Naira based on formatCurrency
                            payment_options: "card,mobilemoney,ussd",
                            customer: {
                                email: "' . ($_SESSION['email'] ?? 'N/A') . '",
                                phone_number: "' . ($_SESSION['phone'] ?? 'N/A') . '",
                                name: "' . ($_SESSION['full_name'] ?? 'Guest User') . '",
                            },
                            customizations: {
                                title: "AVILLA OKADA HOTEL Booking",
                                description: "Payment for Room ' . $room['room_number'] . '",
                                logo: "https://your-hotel-website.com/logo.png", // Replace with your logo
                            },
                            callback: function (data) {
                                // console.log(data); // Log the payment data for debugging
                                if (data.status === "successful") {
                                    window.location.href = "' . $callback_url . '&transaction_id=" + data.transaction_id + "&tx_ref=" + data.tx_ref;
                                } else {
                                    alert("Payment failed or was cancelled. Please try again.");
                                    window.location.href = "' . $callback_url . '&status=failed&tx_ref=" + data.tx_ref; // Redirect to handle failed payment
                                }
                            },
                            onclose: function() {
                                // User closed the payment modal
                                alert("Payment was closed. Your booking is pending payment.");
                            }
                        });
                    }
                    window.onload = makePayment; // Trigger payment on page load after booking initiation
                </script>';
            } catch (PDOException $e) {
                error_log("Error creating booking: " . $e->getMessage());
                $message = 'An error occurred while initiating your booking. Please try again.';
                $messageType = 'error';
            }
        }
    }
}
// Get current date for min attribute on date inputs
$current_date = date('Y-m-d');
?>

<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Book Room: <?= htmlspecialchars($room['room_type']) ?> - <?= htmlspecialchars($room['room_number']) ?></h1>

        <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-lg <?= $messageType === 'error' ? 'bg-red-100 text-red-800 border border-red-200' : 'bg-green-100 text-green-800 border border-green-200'; ?>">
                <?= htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-xl overflow-hidden md:flex">
            <!-- Room Details Section -->
            <div class="md:w-1/2 p-8 border-r border-gray-200">
                <img src="<?= htmlspecialchars($room['image_url'] ?? 'https://via.placeholder.com/600x400?text=Room+Image') ?>"
                     alt="Room <?= htmlspecialchars($room['room_number']) ?>"
                     class="w-full h-64 object-cover rounded-lg mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4"><?= htmlspecialchars($room['room_type']) ?></h2>
                <p class="text-gray-700 mb-2"><strong>Room Number:</strong> <?= htmlspecialchars($room['room_number']) ?></p>
                <p class="text-gray-700 mb-2"><strong>Capacity:</strong> <?= htmlspecialchars($room['capacity']) ?> Guests</p>
                <p class="text-gray-700 mb-2"><strong>Price per Night:</strong> <?= formatCurrency($room['price_per_night']) ?></p>
                <p class="text-gray-700 mb-4"><?= htmlspecialchars($room['description'] ?? 'No description available.') ?></p>
                <!-- Add more room details if needed -->
            </div>

            <!-- Booking Form Section -->
            <div class="md:w-1/2 p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Your Booking Details</h2>
                <form action="" method="POST" class="space-y-4">
                    <div>
                        <label for="check_in_date" class="block text-sm font-medium text-gray-700">Check-in Date</label>
                        <input type="text" id="check_in_date" name="check_in_date" required
                               value="<?= htmlspecialchars($check_in_date) ?>"
                               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500 flatpickr-input">
                    </div>
                    <div>
                        <label for="check_out_date" class="block text-sm font-medium text-gray-700">Check-out Date</label>
                        <input type="text" id="check_out_date" name="check_out_date" required
                               value="<?= htmlspecialchars($check_out_date) ?>"
                               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500 flatpickr-input">
                    </div>
                    <div>
                        <label for="number_of_guests" class="block text-sm font-medium text-gray-700">Number of Guests</label>
                        <input type="number" id="number_of_guests" name="number_of_guests" required min="1" max="<?= htmlspecialchars($room['capacity']) ?>"
                               value="<?= htmlspecialchars($number_of_guests) ?>"
                               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div class="bg-teal-50 p-3 rounded-md border border-teal-200">
                        <p class="text-sm font-medium text-teal-800">Total Nights: <span id="total_nights" class="font-bold">0</span></p>
                        <p class="text-lg font-bold text-teal-800">Total Amount: <span id="total_amount" class="font-bold">₦0.00</span></p>
                    </div>
                    <div>
                        <label for="special_requests" class="block text-sm font-medium text-gray-700">Special Requests (optional)</label>
                        <textarea id="special_requests" name="special_requests" rows="3"
                                  class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-teal-500 focus:border-teal-500"><?= htmlspecialchars($special_requests) ?></textarea>
                    </div>
                    <div class="text-right">
                        <button type="submit" name="book_room"
                                class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                            Proceed to Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>