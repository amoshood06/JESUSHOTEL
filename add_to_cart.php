<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php'; // For session_start() and sanitize()

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
    $itemName = sanitize($_POST['item_name'] ?? '');
    $itemPrice = filter_input(INPUT_POST, 'item_price', FILTER_VALIDATE_FLOAT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

    if ($itemId && $itemName && $itemPrice !== false && $quantity && $quantity > 0) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Check if item already in cart
        if (isset($_SESSION['cart'][$itemId])) {
            $_SESSION['cart'][$itemId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$itemId] = [
                'item_id' => $itemId,
                'name' => $itemName,
                'price' => $itemPrice,
                'quantity' => $quantity,
                'image_url' => '', // Will need to fetch this if we want to display it in cart
            ];
            // Optionally, fetch image_url from database here if not passed with AJAX
            // This would involve another database query, or we can pass it via AJAX
            // For simplicity, I'm leaving it empty for now, assuming it might be fetched on cart page
        }

        $response['success'] = true;
        $response['message'] = 'Item added to cart successfully.';
        // Optionally, add cart total or item count to response
        $response['cart_item_count'] = array_sum(array_column($_SESSION['cart'], 'quantity'));
    } else {
        $response['message'] = 'Missing or invalid item details.';
    }
}

echo json_encode($response);
exit();