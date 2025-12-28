<?php
require_once 'config/database.php';

try {
    // Create settings table
    $sql = "CREATE TABLE IF NOT EXISTS `settings` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `setting_key` varchar(255) NOT NULL,
      `setting_value` text DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
      PRIMARY KEY (`id`),
      UNIQUE KEY `setting_key` (`setting_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    $pdo->exec($sql);

    echo "Table 'settings' created or already exists.<br>";

    // Default settings
    $defaultSettings = [
        'active_index' => 'index-one.php',
        'hotel_name' => 'AVILLA OKADA HOTEL',
        'hotel_address' => '123 Hotel Street, City, Country',
        'hotel_phone' => '+234 xxx xxx xxxx',
        'hotel_email' => 'info@avillaokada.com',
        'hotel_website' => 'https://www.avillaokada.com',
        'check_in_time' => '14:00',
        'check_out_time' => '12:00',
        'currency' => '₦',
        'cancellation_fee' => '5000',
        'early_checkout_fee' => '2500',
        'late_checkout_fee' => '3000',
        'service_charge_percent' => '10',
        'maintenance_mode' => '0',
        'allow_registrations' => '1',
        'email_notifications' => '1',
        'backup_frequency' => 'daily'
    ];

    // Insert or update settings
    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value) ON DUPLICATE KEY UPDATE setting_value = :value");

    foreach ($defaultSettings as $key => $value) {
        $stmt->execute(['key' => $key, 'value' => $value]);
    }

    echo "Default settings inserted or updated.<br>";
    echo "Setup complete!";

} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?>