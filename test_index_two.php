<?php
// test_index_two.php

// Capture the output of index-two.php
ob_start();
include 'index-two.php';
$output = ob_get_clean();

// Simple assertion to check if "Drinks" is NOT present
if (strpos($output, 'Drinks') === false) {
    echo "Test Passed: 'Drinks' is not found on the page.\n";
} else {
    echo "Test Failed: 'Drinks' was found on the page.\n";
}

// Simple assertion to check if "Delicious Food" IS present
if (strpos($output, 'Delicious Food') !== false) {
    echo "Test Passed: 'Delicious Food' is found on the page.\n";
} else {
    echo "Test Failed: 'Delicious Food' was not found on the page.\n";
}

