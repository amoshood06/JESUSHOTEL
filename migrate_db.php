<?php
require_once 'config/database.php';

try {
    // Add customer information columns to food_orders table
    $pdo->exec("
        ALTER TABLE food_orders
        ADD COLUMN customer_email VARCHAR(255) DEFAULT NULL AFTER room_number,
        ADD COLUMN customer_phone VARCHAR(20) DEFAULT NULL AFTER customer_email
    ");

    echo "Database migration completed successfully!\n";
    echo "Added customer_email and customer_phone columns to food_orders table.\n";

} catch (PDOException $e) {
    if ($e->getCode() == '42S21') { // Column already exists
        echo "Columns already exist - migration may have been run before.\n";
    } else {
        echo "Migration failed: " . $e->getMessage() . "\n";
    }
}
?>