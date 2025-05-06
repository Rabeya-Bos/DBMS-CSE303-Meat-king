<?php
session_start(); // start session to store data

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $old_price = $_POST['old_price'];
    $new_price = $_POST['new_price'];
    $old_qty = $_POST['old_qty'];
    $new_qty = $_POST['new_qty'];

    // If session data doesn't exist, create it
    if (!isset($_SESSION['price_quantity_data'])) {
        $_SESSION['price_quantity_data'] = [];
    }

    // Save the new entry
    $_SESSION['price_quantity_data'][$product_id] = [
        'old_price' => $old_price,
        'new_price' => $new_price,
        'old_qty' => $old_qty,
        'new_qty' => $new_qty,
    ];

    // Redirect back to main page
    header("Location: ped_management.php");
    exit();
}
?>
