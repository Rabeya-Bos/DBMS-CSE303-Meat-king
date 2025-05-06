<?php
session_start(); // IMPORTANT: start session

// Fetch from session if available
$price_quantity_data = isset($_SESSION['price_quantity_data']) ? $_SESSION['price_quantity_data'] : [];

// PED Calculation function
function calculate_ped($old_price, $new_price, $old_qty, $new_qty) {
    $price_change = ($new_price - $old_price) / $old_price;
    $qty_change = ($new_qty - $old_qty) / $old_qty;
    return $price_change != 0 ? ($qty_change / $price_change) : 0;
}
?>
