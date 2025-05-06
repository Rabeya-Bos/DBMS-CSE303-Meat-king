<?php
session_start();

// Handle adding items to the cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    // Example product data
    $product_name = $_POST['product_name'];
    $offer_price = $_POST['offer_price'];

    // Create an item with a timestamp when it was added to the cart
    $item = [
        'product_name' => $product_name,
        'offer_price' => $offer_price,
        'added_date' => date("Y-m-d H:i:s") // Store the date when the product was added
    ];

    // Add item to the session cart
    $_SESSION['cart'][] = $item;
}

// Handle the checkout process (this is just a placeholder for actual logic)
if (isset($_POST['checkout'])) {
    // Process checkout (you can add actual checkout logic here)
    echo "<script>alert('Proceeding to checkout!');</script>";
    // Optionally, clear the cart after checkout
    // unset($_SESSION['cart']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>My Cart</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container my-5">
  <h2>My Cart</h2>
  <a href="consumer_offers.php" class="btn btn-secondary mb-3">← Back to Offers</a>

  <!-- Add Product Form -->
  <form method="POST" class="mb-4">
    <h4>Add Product to Cart</h4>
    <div class="mb-3">
      <label for="product_name" class="form-label">Product Name</label>
      <input type="text" name="product_name" id="product_name" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="offer_price" class="form-label">Offer Price ($)</label>
      <input type="number" name="offer_price" id="offer_price" class="form-control" required>
    </div>
    <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
  </form>

  <?php if (!empty($_SESSION['cart'])): ?>
    <!-- Cart Table -->
    <h4>Items in Your Cart</h4>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Product Name</th>
          <th>Offer Price ($)</th>
          <th>Date Added</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($_SESSION['cart'] as $item): ?>
          <tr>
            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
            <td><?php echo number_format($item['offer_price'], 2); ?></td>
            <td><?php echo date("F j, Y, g:i a", strtotime($item['added_date'])); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <!-- Checkout Button -->
    <form method="POST">
      <button type="submit" name="checkout" class="btn btn-success">Proceed to Checkout</button>
    </form>

  <?php else: ?>
    <div class="alert alert-info">Your cart is empty.</div>
  <?php endif; ?>
</div>

</body>
</html>
