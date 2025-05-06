<?php
// add_offer.php
$servername = "localhost";
$username = "root";
$password = "";
$database = "mking";

// Connect to database
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Validate form inputs
$product_name = trim($_POST['product_name']);
$current_price = floatval($_POST['current_price']);
$offer_price = floatval($_POST['offer_price']);

if ($product_name && $current_price > 0 && $offer_price > 0 && $offer_price < $current_price) {
  
  $stmt = $conn->prepare("INSERT INTO historical_data (product_name, current_price, offer_price) VALUES (?, ?, ?)");
  $stmt->bind_param("sdd", $product_name, $current_price, $offer_price);
  $stmt->execute();
  $stmt->close();
}

// Close connection
$conn->close();

// Redirect back
header("Location: offer.php");
exit();
?>
