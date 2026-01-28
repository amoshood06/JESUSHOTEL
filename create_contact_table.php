<?php
require_once 'config/database.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS contact_messages (
        id INT PRIMARY KEY AUTO_INCREMENT,
        first_name VARCHAR(255) NOT NULL,
        last_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        message LONGTEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_read BOOLEAN DEFAULT FALSE
    )";
    
    $pdo->exec($sql);
    echo "Contact messages table created successfully!";
    
} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
