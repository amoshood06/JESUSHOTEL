<?php
require_once 'config/database.php';

try {
    $drinks = [
        [
            'item_name' => 'Classic Mojito',
            'category' => 'Drinks & Beverages',
            'description' => 'A refreshing blend of white rum, fresh mint, lime juice, sugar, and soda water.',
            'price' => 3500.00,
            'availability' => 1,
            'preparation_time' => 8,
            'image_url' => 'asset/image/rooms/classic-mojito.jpg',
            'is_vegetarian' => 1,
            'is_featured' => 1
        ],
        [
            'item_name' => 'Strawberry Daiquiri',
            'category' => 'Drinks & Beverages',
            'description' => 'A sweet and fruity frozen cocktail made with rum, strawberries, and lime juice.',
            'price' => 4000.00,
            'availability' => 1,
            'preparation_time' => 10,
            'image_url' => 'asset/image/rooms/strawberry-daiquiri.jpg',
            'is_vegetarian' => 1,
            'is_featured' => 0
        ],
        [
            'item_name' => 'Iced Latte',
            'category' => 'Drinks & Beverages',
            'description' => 'Chilled espresso mixed with cold milk and served over ice.',
            'price' => 2800.00,
            'availability' => 1,
            'preparation_time' => 5,
            'image_url' => 'asset/image/rooms/iced-latte.jpg',
            'is_vegetarian' => 1,
            'is_featured' => 0
        ],
        [
            'item_name' => 'Pineapple & Ginger Juice',
            'category' => 'Drinks & Beverages',
            'description' => 'A healthy and zesty juice made from fresh pineapples and ginger root.',
            'price' => 2500.00,
            'availability' => 1,
            'preparation_time' => 7,
            'image_url' => 'asset/image/rooms/pineapple-ginger-juice.jpg',
            'is_vegetarian' => 1,
            'is_featured' => 1
        ],
        [
            'item_name' => 'Watermelon Smoothie',
            'category' => 'Drinks & Beverages',
            'description' => 'A cool and hydrating smoothie made with fresh watermelon and a hint of mint.',
            'price' => 3000.00,
            'availability' => 1,
            'preparation_time' => 6,
            'image_url' => 'asset/image/rooms/watermelon-smoothie.jpg',
            'is_vegetarian' => 1,
            'is_featured' => 0
        ],
        [
            'item_name' => 'Heineken',
            'category' => 'Drinks & Beverages',
            'description' => 'A bottle of premium Heineken lager beer.',
            'price' => 1500.00,
            'availability' => 1,
            'preparation_time' => 2,
            'image_url' => 'asset/image/rooms/heineken.jpg',
            'is_vegetarian' => 1,
            'is_featured' => 0
        ]
    ];

    $stmt = $pdo->prepare(
        "INSERT INTO food_menu (item_name, category, description, price, availability, preparation_time, image_url, is_vegetarian, is_featured) 
         VALUES (:item_name, :category, :description, :price, :availability, :preparation_time, :image_url, :is_vegetarian, :is_featured)"
    );

    foreach ($drinks as $drink) {
        $stmt->execute($drink);
    }

    echo "Sample drinks data inserted successfully!\n";

} catch(PDOException $e) {
    echo "Error inserting sample drinks data: " . $e->getMessage() . "\n";
}
?>
