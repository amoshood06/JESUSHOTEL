<?php
require_once '../config/database.php';

// Check if user is logged in and is admin/staff
if (!isLoggedIn() || !isStaff()) {
    redirect('../login.php');
}

// Get room ID from URL
$room_id = (int)($_GET['id'] ?? 0);

if ($room_id > 0) {
    try {
        // First, get the image URL to delete the file
        $stmt = $pdo->prepare("SELECT image_url FROM rooms WHERE room_id = ?");
        $stmt->execute([$room_id]);
        $room = $stmt->fetch();

        if ($room && !empty($room['image_url'])) {
            // The deleteRoomImage function is in rooms.php, so I'll replicate it here.
            $imageUrl = $room['image_url'];
            if ($imageUrl && file_exists('../' . $imageUrl)) {
                unlink('../' . $imageUrl);
            }
        }

        // Now, delete the room record
        $stmt = $pdo->prepare("DELETE FROM rooms WHERE room_id = ?");
        $stmt->execute([$room_id]);
        
        // Set success message and redirect
        $_SESSION['message'] = 'Room deleted successfully!';
        $_SESSION['message_type'] = 'success';
        
    } catch(PDOException $e) {
        // Set error message and redirect
        $_SESSION['message'] = 'Error deleting room: ' . $e->getMessage();
        $_SESSION['message_type'] = 'error';
    }
} else {
    // Set error message for invalid ID and redirect
    $_SESSION['message'] = 'Invalid room ID.';
    $_SESSION['message_type'] = 'error';
}

redirect('rooms.php');
?>