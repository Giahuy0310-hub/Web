<?php
// xoa1.php

// Initialize or retrieve $cartItems
session_start(); // Assuming you are using sessions to store cart items
$cartItems = isset($_SESSION['cartItems']) ? $_SESSION['cartItems'] : [];

// Check if the required parameters are set in the URL
if (isset($_GET['id_product']) && isset($_GET['id_color']) && isset($_GET['size'])) {
    $productId = $_GET['id_product'];
    $colorId = $_GET['id_color'];
    $size = $_GET['size'];

    // Debugging statements
    error_log("Deleting item: $productId, $colorId, $size");
    $sql = "DELETE FROM giohang5 WHERE id_product = ? AND id_color = ? AND size = ?";


    foreach ($cartItems as $key => $item) {
        if ($item['id_product'] == $productId && $item['id_color'] == $colorId && $item['size'] == $size) {
            unset($cartItems[$key]);
            break;
        }
    }

    // Save the updated $cartItems to the session
    $_SESSION['cartItems'] = $cartItems;

    // Return a success message (adjust this based on your needs)
    echo json_encode(['success' => true, 'message' => 'Item successfully deleted from the cart']);
} else {
    // Return an error message if the required parameters are not set
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
}
?>
