<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';
include 'header.php';

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
                <a href="food.php" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700">
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
                    <?php if (isLoggedIn()): ?>
                        <button id="proceedToPaymentBtn" class="w-full bg-teal-600 text-white px-6 py-3 rounded-lg hover:bg-teal-700 transition-colors text-lg font-medium">
                            Proceed to Payment
                        </button>
                    <?php else: ?>
                        <p class="text-red-500 text-center mb-4">Please log in to proceed with payment.</p>
                        <a href="login.php?redirect=<?= urlencode('cart.php') ?>" class="w-full text-center inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-teal-600 hover:bg-teal-700">
                            Login to Checkout
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const proceedToPaymentBtn = document.getElementById('proceedToPaymentBtn');
        if (proceedToPaymentBtn) {
            proceedToPaymentBtn.addEventListener('click', function() {
                // In a real application, you would make an AJAX call here
                // to create an order in the database and then initiate Flutterwave payment.
                // For simplicity, we'll directly initiate Flutterwave from here for now,
                // passing necessary details.
                // alert('Proceeding to payment (Flutterwave integration will be here).'); // Removed alert

                // Example of how you might initiate Flutterwave (this will be improved later)
                // You would typically get tx_ref and total_amount from server-side after order creation
                const totalAmount = <?= $cartTotal ?>; // Use PHP variable
                const userEmail = "<?= $_SESSION['email'] ?? 'guest@example.com' ?>";
                const userName = "<?= $_SESSION['full_name'] ?? 'Guest User' ?>";

                // Dummy transaction reference - replace with a unique one from server after order creation
                const txRef = 'FLW_FOOD_ORDER_' + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);

                FlutterwaveCheckout({
                    public_key: 'FLWPUBK_TEST-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX-X', // Replace with your actual public key
                    tx_ref: txRef,
                    amount: totalAmount,
                    currency: "NGN",
                    payment_options: "card,mobilemoney,ussd",
                    customer: {
                        email: userEmail,
                        phone_number: "N/A", // Replace with actual phone if available
                        name: userName,
                    },
                    customizations: {
                        title: "AVILLA OKADA HOTEL Food Order",
                        description: "Payment for food and drink order",
                        logo: "asset/image/logo1.png", // Replace with your logo
                    },
                    callback: function (data) {
                        // Handle payment success or failure
                        if (data.status === "successful") {
                            window.location.href = "payment_callback.php?status=successful&tx_ref=" + data.tx_ref + "&transaction_id=" + data.transaction_id + "&total_amount=" + totalAmount;
                        } else {
                            window.location.href = "payment_callback.php?status=failed&tx_ref=" + data.tx_ref;
                        }
                    },
                    onclose: function() {
                        alert("Payment window closed. Your order is pending payment.");
                    }
                });
            });
        }
    });
</script>

<script src="https://checkout.flutterwave.com/v3.js"></script>