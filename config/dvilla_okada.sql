-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 23, 2025 at 02:32 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dvilla_okada`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `check_in_guest` (IN `p_booking_id` INT)   BEGIN
    DECLARE v_room_id INT;
    
    -- Get room ID
    SELECT room_id INTO v_room_id FROM bookings WHERE booking_id = p_booking_id;
    
    -- Update booking status
    UPDATE bookings SET status = 'checked_in' WHERE booking_id = p_booking_id;
    
    -- Update room status
    UPDATE rooms SET status = 'occupied' WHERE room_id = v_room_id;
    
    SELECT 'Guest checked in successfully' AS message;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `check_out_guest` (IN `p_booking_id` INT)   BEGIN
    DECLARE v_room_id INT;
    
    -- Get room ID
    SELECT room_id INTO v_room_id FROM bookings WHERE booking_id = p_booking_id;
    
    -- Update booking status
    UPDATE bookings SET status = 'checked_out' WHERE booking_id = p_booking_id;
    
    -- Update room status
    UPDATE rooms SET status = 'available' WHERE room_id = v_room_id;
    
    SELECT 'Guest checked out successfully' AS message;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `create_booking` (IN `p_user_id` INT, IN `p_room_id` INT, IN `p_check_in` DATE, IN `p_check_out` DATE, IN `p_guests` INT, IN `p_special_requests` TEXT)   BEGIN
    DECLARE v_total_nights INT;
    DECLARE v_price_per_night DECIMAL(10,2);
    DECLARE v_total_amount DECIMAL(10,2);
    DECLARE v_booking_code VARCHAR(20);
    DECLARE v_room_status VARCHAR(20);
    
    -- Check if room is available
    SELECT status INTO v_room_status FROM rooms WHERE room_id = p_room_id;
    
    IF v_room_status != 'available' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Room is not available';
    END IF;
    
    -- Calculate total nights
    SET v_total_nights = DATEDIFF(p_check_out, p_check_in);
    
    -- Get room price
    SELECT price_per_night INTO v_price_per_night FROM rooms WHERE room_id = p_room_id;
    
    -- Calculate total amount
    SET v_total_amount = v_total_nights * v_price_per_night;
    
    -- Generate booking code
    SET v_booking_code = CONCAT('BK', LPAD(FLOOR(RAND() * 999999), 6, '0'));
    
    -- Insert booking
    INSERT INTO bookings (
        booking_code, user_id, room_id, check_in_date, check_out_date,
        number_of_guests, total_nights, total_amount, status, payment_status
    ) VALUES (
        v_booking_code, p_user_id, p_room_id, p_check_in, p_check_out,
        p_guests, v_total_nights, v_total_amount, 'pending', 'unpaid'
    );
    
    -- Update room status
    UPDATE rooms SET status = 'reserved' WHERE room_id = p_room_id;
    
    -- Return booking details
    SELECT LAST_INSERT_ID() AS booking_id, v_booking_code AS booking_code, v_total_amount AS total_amount;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `process_payment` (IN `p_booking_id` INT, IN `p_order_id` INT, IN `p_user_id` INT, IN `p_amount` DECIMAL(10,2), IN `p_method` VARCHAR(20))   BEGIN
    DECLARE v_transaction_id VARCHAR(100);
    DECLARE v_reference_number VARCHAR(100);
    
    -- Generate transaction ID
    SET v_transaction_id = CONCAT('TXN', UNIX_TIMESTAMP(), FLOOR(RAND() * 1000));
    SET v_reference_number = CONCAT('REF', UNIX_TIMESTAMP(), FLOOR(RAND() * 1000));
    
    -- Insert payment
    INSERT INTO payments (
        booking_id, order_id, user_id, payment_amount, payment_method,
        payment_status, transaction_id, reference_number
    ) VALUES (
        p_booking_id, p_order_id, p_user_id, p_amount, p_method,
        'completed', v_transaction_id, v_reference_number
    );
    
    -- Update booking payment status if applicable
    IF p_booking_id IS NOT NULL THEN
        UPDATE bookings SET payment_status = 'paid' WHERE booking_id = p_booking_id;
    END IF;
    
    -- Update order payment status if applicable
    IF p_order_id IS NOT NULL THEN
        UPDATE food_orders SET payment_status = 'paid' WHERE order_id = p_order_id;
    END IF;
    
    SELECT v_transaction_id AS transaction_id, v_reference_number AS reference_number;
    
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `booking_code` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `number_of_guests` int(11) NOT NULL,
  `total_nights` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','checked_in','checked_out','cancelled') DEFAULT 'pending',
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `special_requests` text DEFAULT NULL,
  `payment_status` enum('unpaid','partial','paid','refunded') DEFAULT 'unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `booking_code`, `user_id`, `room_id`, `check_in_date`, `check_out_date`, `number_of_guests`, `total_nights`, `total_amount`, `status`, `booking_date`, `special_requests`, `payment_status`) VALUES
(1, 'BK001', 1, 1, '2025-12-23', '2025-12-25', 2, 2, 50000.00, 'confirmed', '2025-12-23 12:57:15', NULL, 'paid');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `event_name` varchar(200) NOT NULL,
  `event_type` enum('conference','wedding','birthday','corporate','private','other') NOT NULL,
  `event_date` date NOT NULL,
  `event_start_time` time NOT NULL,
  `event_end_time` time NOT NULL,
  `expected_guests` int(11) NOT NULL,
  `organizer_id` int(11) NOT NULL,
  `event_description` text DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL COMMENT 'Event hall, outdoor, etc.',
  `status` enum('pending','confirmed','in_progress','completed','cancelled') DEFAULT 'pending',
  `total_cost` decimal(10,2) DEFAULT NULL,
  `catering_required` tinyint(1) DEFAULT 0,
  `special_requirements` text DEFAULT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_items`
--

CREATE TABLE `event_items` (
  `event_item_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `item_description` varchar(255) NOT NULL,
  `item_category` enum('catering','decoration','entertainment','equipment_rental') NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `food_menu`
--

CREATE TABLE `food_menu` (
  `menu_item_id` int(11) NOT NULL,
  `item_name` varchar(150) NOT NULL,
  `category` enum('Nigerian Dishes','Continental Plates','Breakfast Specials','Drinks & Beverages','Appetizers','Desserts') NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `availability` tinyint(1) DEFAULT 1,
  `preparation_time` int(11) DEFAULT NULL COMMENT 'in minutes',
  `image_url` varchar(255) DEFAULT NULL,
  `is_vegetarian` tinyint(1) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `food_menu`
--

INSERT INTO `food_menu` (`menu_item_id`, `item_name`, `category`, `description`, `price`, `availability`, `preparation_time`, `image_url`, `is_vegetarian`, `is_featured`, `created_date`, `last_updated`) VALUES
(1, 'Jollof Rice', 'Nigerian Dishes', 'cook', 2500.00, 1, 10, NULL, 0, 0, '2025-12-23 13:30:28', '2025-12-23 13:30:28');

-- --------------------------------------------------------

--
-- Table structure for table `food_orders`
--

CREATE TABLE `food_orders` (
  `order_id` int(11) NOT NULL,
  `order_code` varchar(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `room_number` varchar(10) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `delivery_type` enum('dine-in','room-service','takeaway','delivery') NOT NULL,
  `order_status` enum('pending','confirmed','preparing','ready','delivered','cancelled') DEFAULT 'pending',
  `special_requests` text DEFAULT NULL,
  `delivery_address` varchar(255) DEFAULT NULL,
  `delivery_time` timestamp NULL DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('unpaid','paid') DEFAULT 'unpaid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `special_instructions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `payment_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','card','bank_transfer','mobile_money','cheque','flutterwave') NOT NULL,
  `payment_gateway` enum('manual','flutterwave','paystack','stripe') DEFAULT 'manual',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `transaction_id` varchar(100) DEFAULT NULL,
  `card_last_4` varchar(4) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `flutterwave_tx_ref` varchar(100) DEFAULT NULL,
  `flutterwave_transaction_id` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `receipt_generated` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `room_type` enum('Standard Room','Deluxe Room','Executive Suite') NOT NULL,
  `capacity` int(11) NOT NULL,
  `price_per_night` decimal(10,2) NOT NULL,
  `status` enum('available','occupied','maintenance','reserved') DEFAULT 'available',
  `description` text DEFAULT NULL,
  `amenities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`amenities`)),
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `image_url` varchar(255) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_number`, `room_type`, `capacity`, `price_per_night`, `status`, `description`, `amenities`, `features`, `image_url`, `created_date`, `last_updated`) VALUES
(1, '101', 'Standard Room', 2, 25000.00, 'available', 'Comfortable standard room with basic amenities', NULL, NULL, NULL, '2025-12-23 12:57:15', '2025-12-23 12:57:15'),
(2, '102', 'Standard Room', 2, 25000.00, 'occupied', 'Comfortable standard room with basic amenities', NULL, NULL, NULL, '2025-12-23 12:57:15', '2025-12-23 12:57:15'),
(3, '201', 'Deluxe Room', 3, 45000.00, 'available', 'Spacious deluxe room with premium amenities', NULL, NULL, NULL, '2025-12-23 12:57:15', '2025-12-23 12:57:15'),
(4, '202', 'Deluxe Room', 3, 45000.00, 'maintenance', 'Spacious deluxe room with premium amenities', NULL, NULL, NULL, '2025-12-23 12:57:15', '2025-12-23 12:57:15'),
(5, '301', 'Executive Suite', 4, 75000.00, 'available', 'Luxurious executive suite with panoramic views', NULL, NULL, NULL, '2025-12-23 12:57:15', '2025-12-23 12:57:15'),
(6, '302', 'Executive Suite', 4, 75000.00, 'reserved', 'Luxurious executive suite with panoramic views', NULL, NULL, NULL, '2025-12-23 12:57:15', '2025-12-23 12:57:15');

-- --------------------------------------------------------

--
-- Table structure for table `room_amenities`
--

CREATE TABLE `room_amenities` (
  `amenity_id` int(11) NOT NULL,
  `amenity_name` varchar(100) NOT NULL,
  `amenity_icon` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `position` enum('Manager','Chef','Waiter','Housekeeper','Receptionist','Security','Maintenance') NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `hire_date` date NOT NULL,
  `employment_status` enum('active','inactive','on_leave','terminated') DEFAULT 'active',
  `national_id` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `emergency_contact` varchar(100) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('guest','admin','staff') DEFAULT 'guest',
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `profile_image` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'Nigeria',
  `postal_code` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `phone`, `password_hash`, `role`, `registration_date`, `last_login`, `is_active`, `profile_image`, `address`, `city`, `state`, `country`, `postal_code`) VALUES
(1, 'Admin', 'User', 'admin@avillahotel.com', '+2341234567890', '$2y$10$h6zGe8siAVGwaYET10RbPumDBoUnDgkxbTxOSATXoX0eqkxne56wm', 'admin', '2025-12-23 12:57:15', '2025-12-23 13:15:35', 1, NULL, NULL, NULL, NULL, 'Nigeria', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD UNIQUE KEY `booking_code` (`booking_code`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_room_id` (`room_id`),
  ADD KEY `idx_check_in` (`check_in_date`),
  ADD KEY `idx_check_out` (`check_out_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_booking_date` (`booking_date`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `organizer_id` (`organizer_id`),
  ADD KEY `idx_event_date` (`event_date`),
  ADD KEY `idx_event_type` (`event_type`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `event_items`
--
ALTER TABLE `event_items`
  ADD PRIMARY KEY (`event_item_id`),
  ADD KEY `idx_event_id` (`event_id`);

--
-- Indexes for table `food_menu`
--
ALTER TABLE `food_menu`
  ADD PRIMARY KEY (`menu_item_id`),
  ADD KEY `idx_category` (`category`),
  ADD KEY `idx_availability` (`availability`),
  ADD KEY `idx_price` (`price`);

--
-- Indexes for table `food_orders`
--
ALTER TABLE `food_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD UNIQUE KEY `order_code` (`order_code`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_order_date` (`order_date`),
  ADD KEY `idx_order_status` (`order_status`),
  ADD KEY `idx_delivery_type` (`delivery_type`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_menu_item_id` (`menu_item_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD UNIQUE KEY `reference_number` (`reference_number`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_payment_date` (`payment_date`),
  ADD KEY `idx_payment_status` (`payment_status`),
  ADD KEY `idx_transaction_id` (`transaction_id`),
  ADD KEY `idx_payment_gateway` (`payment_gateway`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD UNIQUE KEY `room_number` (`room_number`),
  ADD KEY `idx_room_type` (`room_type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_price` (`price_per_night`);

--
-- Indexes for table `room_amenities`
--
ALTER TABLE `room_amenities`
  ADD PRIMARY KEY (`amenity_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `national_id` (`national_id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_position` (`position`),
  ADD KEY `idx_employment_status` (`employment_status`),
  ADD KEY `idx_hire_date` (`hire_date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_registration_date` (`registration_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_items`
--
ALTER TABLE `event_items`
  MODIFY `event_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `food_menu`
--
ALTER TABLE `food_menu`
  MODIFY `menu_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `food_orders`
--
ALTER TABLE `food_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `room_amenities`
--
ALTER TABLE `room_amenities`
  MODIFY `amenity_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`organizer_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `event_items`
--
ALTER TABLE `event_items`
  ADD CONSTRAINT `event_items_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE;

--
-- Constraints for table `food_orders`
--
ALTER TABLE `food_orders`
  ADD CONSTRAINT `food_orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `food_orders_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `food_orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `food_menu` (`menu_item_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `food_orders` (`order_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payments_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `staff`
--
ALTER TABLE `staff`
  ADD CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
