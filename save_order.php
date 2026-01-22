<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/database.php';

header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit();
}

// Validate required fields
$required_fields = ['email', 'room_number', 'phone', 'cart_items', 'total_amount'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Missing required field: {$field}"]);
        exit();
    }
}

$email = sanitize($data['email']);
$roomNumber = sanitize($data['room_number']);
$phone = sanitize($data['phone']);
$cartItems = $data['cart_items'];
$totalAmount = (float) $data['total_amount'];

try {
    $pdo->beginTransaction();

    // Generate unique order code
    $orderCode = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));

    // Insert order into food_orders table
    $stmt = $pdo->prepare("
        INSERT INTO food_orders (
            order_code, room_number, customer_email, customer_phone,
            delivery_type, total_amount, order_date
        ) VALUES (?, ?, ?, ?, 'room-service', ?, NOW())
    ");

    $stmt->execute([$orderCode, $roomNumber, $email, $phone, $totalAmount]);
    $orderId = $pdo->lastInsertId();

    // Insert order items
    $itemStmt = $pdo->prepare("
        INSERT INTO order_items (
            order_id, menu_item_id, quantity, unit_price, subtotal
        ) VALUES (?, ?, ?, ?, ?)
    ");

    foreach ($cartItems as $itemId => $item) {
        $itemStmt->execute([
            $orderId,
            $itemId,
            $item['quantity'],
            $item['price'],
            $item['price'] * $item['quantity']
        ]);
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Order saved successfully',
        'order_id' => $orderId,
        'order_code' => $orderCode
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log('Order save error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to save order']);
}
?>