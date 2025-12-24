<?php
require_once 'config/database.php';

try {
    // Insert sample rooms
    $rooms = [
        ['101', 'Standard Room', 2, 25000.00, 'available', 'Comfortable standard room with basic amenities'],
        ['102', 'Standard Room', 2, 25000.00, 'occupied', 'Comfortable standard room with basic amenities'],
        ['201', 'Deluxe Room', 3, 45000.00, 'available', 'Spacious deluxe room with premium amenities'],
        ['202', 'Deluxe Room', 3, 45000.00, 'maintenance', 'Spacious deluxe room with premium amenities'],
        ['301', 'Executive Suite', 4, 75000.00, 'available', 'Luxurious executive suite with panoramic views'],
        ['302', 'Executive Suite', 4, 75000.00, 'reserved', 'Luxurious executive suite with panoramic views'],
    ];

    $stmt = $pdo->prepare("INSERT INTO rooms (room_number, room_type, capacity, price_per_night, status, description) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($rooms as $room) {
        $stmt->execute($room);
    }

    // Insert sample admin user (password: admin123)
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password_hash, role, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute(['Admin', 'User', 'admin@avillahotel.com', '+2341234567890', $adminPassword, 'admin', 1]);

    // Insert sample booking
    $stmt = $pdo->prepare("INSERT INTO bookings (booking_code, user_id, room_id, check_in_date, check_out_date, number_of_guests, total_nights, total_amount, status, payment_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute(['BK001', 1, 1, date('Y-m-d'), date('Y-m-d', strtotime('+2 days')), 2, 2, 50000.00, 'confirmed', 'paid']);

    echo "Sample data inserted successfully!\n";
    echo "Admin login: admin@avillahotel.com / admin123\n";

} catch(PDOException $e) {
    echo "Error inserting sample data: " . $e->getMessage() . "\n";
}
?>