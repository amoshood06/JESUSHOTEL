<?php
require_once '../config/database.php';

// Check if user is logged in and is admin/staff
if (!isLoggedIn() || !isStaff()) {
    redirect('../login.php');
}

// Get menu item ID from URL
$menu_item_id = (int)($_GET['id'] ?? 0);

if ($menu_item_id > 0) {
    try {
        // First, get the image URL to delete the file
        $stmt = $pdo->prepare("SELECT image_url FROM food_menu WHERE menu_item_id = ?");
        $stmt->execute([$menu_item_id]);
        $item = $stmt->fetch();

        if ($item && !empty($item['image_url'])) {
            $imageUrl = $item['image_url'];
            if ($imageUrl && file_exists('../' . $imageUrl)) {
                unlink('../' . $imageUrl);
            }
        }

        // Now, delete the menu item record
        $stmt = $pdo->prepare("DELETE FROM food_menu WHERE menu_item_id = ?");
        $stmt->execute([$menu_item_id]);
        
        // Set success message and redirect
        $_SESSION['message'] = 'Menu item deleted successfully!';
        $_SESSION['message_type'] = 'success';
        
    } catch(PDOException $e) {
        // Set error message and redirect
        $_SESSION['message'] = 'Error deleting menu item: ' . $e->getMessage();
        $_SESSION['message_type'] = 'error';
    }
} else {
    // Set error message for invalid ID and redirect
    $_SESSION['message'] = 'Invalid menu item ID.';
    $_SESSION['message_type'] = 'error';
}

redirect('food-menu.php');
?>