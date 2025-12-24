<?php
// Include the main header
include_once __DIR__ . '/../header.php';

// Include database connection and helper functions
include_once __DIR__ . '/../config/database.php';

// Check if user is logged in, otherwise redirect to login page
if (!isLoggedIn()) {
    redirect('../login.php');
}

$currentUser = getCurrentUser();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <!-- Include shared styles -->
    <link rel="stylesheet" href="../shared-styles.css">
    <style>
        /* Add user-specific styles here */
        .user-sidebar {
            width: 250px;
            background-color: #f4f4f4;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            min-height: calc(100vh - 120px); /* Adjust based on header/footer height */
        }
        .user-sidebar ul {
            list-style: none;
            padding: 0;
        }
        .user-sidebar ul li {
            margin-bottom: 10px;
        }
        .user-sidebar ul li a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
            display: block;
            padding: 8px 10px;
            border-radius: 5px;
        }
        .user-sidebar ul li a:hover,
        .user-sidebar ul li a.active {
            background-color: #007bff;
            color: white;
        }
        .user-content {
            flex-grow: 1;
            padding: 20px;
        }
        .dashboard-container {
            display: flex;
            margin-top: 20px; /* Adjust based on header height */
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <aside class="user-sidebar">
        <nav>
            <ul>
                <li><a href="user-dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'user-dashboard.php' ? 'active' : ''; ?>">Dashboard</a></li>
                <li><a href="profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">Profile</a></li>
                <li><a href="bookings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : ''; ?>">My Bookings</a></li>
                <li><a href="orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>">My Orders</a></li>
                <li><a href="payments.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'payments.php' ? 'active' : ''; ?>">My Payments</a></li>
                <li><a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">Settings</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </aside>
    <main class="user-content">
