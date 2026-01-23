<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';

include 'header-one.php';

// Handle removing item from cart
if (isset($_POST['remove_item_id'])) {
    $removeItemId = filter_input(INPUT_POST, 'remove_item_id', FILTER_VALIDATE_INT);
    if ($removeItemId && isset($_SESSION['cart'][$removeItemId])) {
        unset($_SESSION['cart'][$removeItemId]);
        // Redirect to prevent form resubmission on refresh
        header('Location: cart.php');
        exit();
    }
}

$cartItems = $_SESSION['cart'] ?? [];
$cartTotal = 0;
foreach ($cartItems as $item) {
    $cartTotal += $item['price'] * $item['quantity'];
}
?>

<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Your Shopping Cart</h1>

        <?php if (empty($cartItems)): ?>
            <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                <p class="text-gray-600 text-lg mb-4">Your cart is empty.</p>
                <a href="food.php" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-black hover:bg-teal-700">
                    Continue Shopping
                </a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 bg-white rounded-lg shadow-lg p-6">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Items in Cart</h2>
                    <div class="divide-y divide-gray-200">
                        <?php foreach ($cartItems as $itemId => $item): ?>
                            <div class="flex items-center py-4">
                                <div class="flex-shrink-0 w-20 h-20 rounded-lg overflow-hidden mr-4">
                                    <img src="<?= htmlspecialchars($item['image_url'] ?: 'https://via.placeholder.com/80x80?text=Food') ?>"
                                         alt="<?= htmlspecialchars($item['name']) ?>" class="w-full h-full object-cover">
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900"><?= htmlspecialchars($item['name']) ?></h3>
                                    <p class="text-gray-600">Price: <?= formatCurrency($item['price']) ?></p>
                                    <p class="text-gray-600">Quantity: <?= htmlspecialchars($item['quantity']) ?></p>
                                    <p class="text-gray-800 font-semibold">Subtotal: <?= formatCurrency($item['price'] * $item['quantity']) ?></p>
                                </div>
                                <form method="POST" action="cart.php" class="ml-4">
                                    <input type="hidden" name="remove_item_id" value="<?= htmlspecialchars($itemId) ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">Remove</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="lg:col-span-1 bg-white rounded-lg shadow-lg p-6 h-fit">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-6">Order Summary</h2>
                    <div class="flex justify-between items-center text-xl font-bold text-gray-900 mb-6">
                        <span>Total:</span>
                        <span><?= formatCurrency($cartTotal) ?></span>
                    </div>

                    <!-- Customer Information Form -->
                    <form id="customerForm" class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Delivery Information</h3>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                            <input type="email" id="email" name="email" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                   placeholder="your.email@example.com">
                        </div>

                        <div>
                            <label for="room_number" class="block text-sm font-medium text-gray-700 mb-1">Room Number *</label>
                            <input type="text" id="room_number" name="room_number" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                   placeholder="e.g., 101, 205A">
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                            <input type="tel" id="phone" name="phone" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                   placeholder="+234 xxx xxx xxxx">
                        </div>

                        <button type="submit" id="proceedToPaymentBtn"
                                class="w-full bg-black text-white px-6 py-3 rounded-lg hover:bg-teal-700 transition-colors text-lg font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                            Proceed to Payment
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer-one.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const customerForm = document.getElementById('customerForm');
        const proceedToPaymentBtn = document.getElementById('proceedToPaymentBtn');

        customerForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Get form values
            const email = document.getElementById('email').value.trim();
            const roomNumber = document.getElementById('room_number').value.trim();
            const phone = document.getElementById('phone').value.trim();

            // Validate form
            if (!email || !roomNumber || !phone) {
                alert('Please fill in all required fields.');
                return;
            }

            // Validate email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                alert('Please enter a valid email address.');
                return;
            }

            // Disable button during processing
            proceedToPaymentBtn.disabled = true;
            proceedToPaymentBtn.textContent = 'Processing...';

            // Proceed to payment with form data
            initiatePayment(email, roomNumber, phone);
        });

        function initiatePayment(email, roomNumber, phone) {
            const totalAmount = <?= $cartTotal ?>;

            // First, save the order to database
            fetch('save_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    email: email,
                    room_number: roomNumber,
                    phone: phone,
                    cart_items: <?= json_encode($cartItems) ?>,
                    total_amount: totalAmount
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Order saved successfully, proceed to payment
                    proceedToFlutterwavePayment(email, roomNumber, phone, data.order_id, totalAmount);
                } else {
                    alert('Error saving order: ' + data.message);
                    proceedToPaymentBtn.disabled = false;
                    proceedToPaymentBtn.textContent = 'Proceed to Payment';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving order. Please try again.');
                proceedToPaymentBtn.disabled = false;
                proceedToPaymentBtn.textContent = 'Proceed to Payment';
            });
        }

        function proceedToFlutterwavePayment(email, roomNumber, phone, orderId, totalAmount) {
            // Create transaction reference
            const txRef = 'FLW_FOOD_ORDER_' + orderId + '_' + Math.random().toString(36).substring(2, 8);

            FlutterwaveCheckout({
                public_key: 'FLWPUBK_TEST-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX-X', // Replace with your actual public key
                tx_ref: txRef,
                amount: totalAmount,
                currency: "NGN",
                payment_options: "card,mobilemoney,ussd",
                customer: {
                    email: email,
                    phone_number: phone,
                    name: `Room ${roomNumber}`,
                },
                customizations: {
                    title: "AVILLA OKADA HOTEL Food Order",
                    description: `Payment for food order - Room ${roomNumber} (Order #${orderId})`,
                    logo: "asset/image/logo1.png",
                },
                callback: function (data) {
                    // Handle payment success or failure
                    if (data.status === "successful") {
                        window.location.href = `payment_callback.php?status=successful&tx_ref=${data.tx_ref}&transaction_id=${data.transaction_id}&total_amount=${totalAmount}&order_id=${orderId}`;
                    } else {
                        window.location.href = "payment_callback.php?status=failed&tx_ref=" + data.tx_ref;
                    }
                },
                onclose: function() {
                    // Re-enable button if payment window is closed
                    proceedToPaymentBtn.disabled = false;
                    proceedToPaymentBtn.textContent = 'Proceed to Payment';
                    alert("Payment window closed. Your order is pending payment.");
                }
            });
        }
    });
</script>

<script src="https://checkout.flutterwave.com/v3.js"></script>