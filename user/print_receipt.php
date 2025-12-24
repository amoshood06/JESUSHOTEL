<?php
include_once __DIR__ . '/../config/database.php';

// Ensure user is logged in
if (!isLoggedIn()) {
    redirect('../login.php');
}

$booking_id = filter_input(INPUT_GET, 'booking_id', FILTER_VALIDATE_INT);

if (!$booking_id) {
    die("Invalid booking ID.");
}

$booking = null;
$error = '';

try {
    // Fetch comprehensive booking details
    $stmt = $pdo->prepare("
        SELECT
            b.booking_id, b.booking_code, b.check_in_date, b.check_out_date,
            b.number_of_guests, b.total_nights, b.total_amount, b.status AS booking_status,
            b.booking_date, b.special_requests, b.payment_status,
            r.room_number, r.room_type, r.price_per_night,
            u.first_name, u.last_name, u.email, u.phone, u.address
        FROM bookings b
        JOIN rooms r ON b.room_id = r.room_id
        JOIN users u ON b.user_id = u.user_id
        WHERE b.booking_id = ? AND b.user_id = ?
    ");
    $stmt->execute([$booking_id, $currentUser['user_id']]);
    $booking = $stmt->fetch();

    if (!$booking) {
        $error = "Booking not found or you don't have permission to view it.";
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Receipt - <?php echo htmlspecialchars($booking['booking_code'] ?? 'N/A'); ?></title>
    <link rel="stylesheet" href="../shared-styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .receipt-container {
            width: 800px;
            margin: 0 auto;
            border: 1px solid #ccc;
            padding: 30px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
        }
        .receipt-header h1 {
            margin: 0;
            color: #007bff;
        }
        .receipt-header p {
            margin: 5px 0;
            font-size: 0.9em;
        }
        .section-title {
            font-weight: bold;
            color: #007bff;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-top: 20px;
            margin-bottom: 15px;
        }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 20px;
            margin-bottom: 20px;
        }
        .details-grid p {
            margin: 0;
        }
        .details-grid p strong {
            display: inline-block;
            width: 120px; /* Align labels */
        }
        .total-section {
            text-align: right;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
        .total-section p {
            font-size: 1.1em;
            margin: 5px 0;
        }
        .total-section .grand-total {
            font-size: 1.5em;
            font-weight: bold;
            color: #007bff;
        }
        .print-button-container {
            text-align: center;
            margin-top: 30px;
        }
        @media print {
            .print-button-container {
                display: none;
            }
            body {
                margin: 0;
            }
            .receipt-container {
                width: auto;
                border: none;
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <?php if ($error): ?>
            <p class="text-red-500 text-center"><?php echo htmlspecialchars($error); ?></p>
        <?php elseif ($booking): ?>
            <div class="receipt-header">
                <h1>Booking Receipt</h1>
                <p><strong>AVILLA OKADA HOTEL</strong></p>
                <p>Booking Date: <?php echo formatDate($booking['booking_date']); ?></p>
            </div>

            <div class="section-title">Customer Details</div>
            <div class="details-grid">
                <p><strong>Name:</strong> <?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($booking['email']); ?></p>
                <?php if (!empty($booking['phone'])): ?>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($booking['phone']); ?></p>
                <?php endif; ?>
                <?php if (!empty($booking['address'])): ?>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($booking['address']); ?></p>
                <?php endif; ?>
            </div>

            <div class="section-title">Booking Details</div>
            <div class="details-grid">
                <p><strong>Booking Code:</strong> <?php echo htmlspecialchars($booking['booking_code']); ?></p>
                <p><strong>Room Type:</strong> <?php echo htmlspecialchars($booking['room_type']); ?></p>
                <p><strong>Room Number:</strong> <?php echo htmlspecialchars($booking['room_number']); ?></p>
                <p><strong>Guests:</strong> <?php echo htmlspecialchars($booking['number_of_guests']); ?></p>
                <p><strong>Check-in:</strong> <?php echo formatDate($booking['check_in_date']); ?></p>
                <p><strong>Check-out:</strong> <?php echo formatDate($booking['check_out_date']); ?></p>
                <p><strong>Nights:</strong> <?php echo htmlspecialchars($booking['total_nights']); ?></p>
                <p><strong>Booking Status:</strong> <?php echo htmlspecialchars(ucfirst($booking['booking_status'])); ?></p>
                <p><strong>Payment Status:</strong> <?php echo htmlspecialchars(ucfirst($booking['payment_status'])); ?></p>
            </div>

            <div class="section-title">Payment Summary</div>
            <div class="total-section">
                <p>Subtotal: <?php echo formatCurrency($booking['total_amount']); ?></p>
                <p>Tax (0%): <?php echo formatCurrency(0); ?></p>
                <p class="grand-total">Total Amount: <?php echo formatCurrency($booking['total_amount']); ?></p>
            </div>

            <?php if (!empty($booking['special_requests'])): ?>
                <div class="section-title">Special Requests</div>
                <p><?php echo nl2br(htmlspecialchars($booking['special_requests'])); ?></p>
            <?php endif; ?>

            <div class="print-button-container">
                <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Print Receipt
                </button>
            </div>

        <?php endif; ?>
    </div>
</body>
</html>
