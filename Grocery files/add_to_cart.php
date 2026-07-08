<?php
session_start();
require_once 'db.php';

// Check if data was sent via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $price = $_POST['price'];
    $image = $_POST['image'];

    // Initialize the cart if it doesn't exist
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // If item is already in cart, just increase quantity
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['qty'] += 1;
    } else {
        // Otherwise, add the new product details
        $_SESSION['cart'][$product_id] = [
            'name' => $name,
            'price' => $price,
            'image' => $image,
            'qty' => 1
        ];
    }

    // Redirect to cart page to show the added item
    header("Location: cart.php");
    exit();
} else {
    // If someone tries to access this file directly, send them back
    header("Location: index.php");
    exit();
}