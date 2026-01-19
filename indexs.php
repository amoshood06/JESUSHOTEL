<?php
require_once 'config/database.php';

$activeIndexPage = 'index-one.php'; // Default homepage

try {
    $stmt = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'active_index'");
    $result = $stmt->fetchColumn();
    if ($result) {
        $activeIndexPage = $result;
    }
} catch (PDOException $e) {
    // If there's an error (like table not found), we'll just use the default.
    // The setup.php script should be run to create the table.
}

if (file_exists($activeIndexPage)) {
    require_once $activeIndexPage;
} else {
    // Fallback if the file doesn't exist.
    require_once 'index-one.php';
}
?>