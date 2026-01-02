<?php
require_once 'config/database.php';

$drink_category = isset($_GET['category']) ? $_GET['category'] : 'All';

try {
    if ($drink_category === 'All') {
        $stmt = $pdo->query("SELECT * FROM food_menu WHERE category = 'Drinks' AND availability = 1 ORDER BY last_updated DESC LIMIT 12");
    } else {
        $stmt = $pdo->prepare("SELECT * FROM food_menu WHERE category = 'Drinks' AND drink_category = :drink_category AND availability = 1 ORDER BY last_updated DESC");
        $stmt->execute(['drink_category' => $drink_category]);
    }
    
    $drinks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    header('Content-Type: application/json');
    echo json_encode($drinks);
    
} catch (PDOException $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>