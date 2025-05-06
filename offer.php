<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "mking";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Consumer ID (temporary / use session id if login system exists)
$consumer_id = 1; // Static for now

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['product_name'];
    $offer_price = $_POST['offer_price'];

    if (isset($_POST['add_to_cart'])) {
        $stmt = $conn->prepare("INSERT INTO cart_items (user_id, product_name, offer_price, status) VALUES (?, ?, ?, 'in_cart')");
        $stmt->bind_param("isd", $consumer_id, $product_name, $offer_price);
        $stmt->execute();
        $stmt->close();

        header("Location: consumer_offers.php");
        exit();
    }

    if (isset($_POST['place_order'])) {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, product_name, offer_price) VALUES (?, ?, ?)");
        $stmt->bind_param("isd", $consumer_id, $product_name, $offer_price);
        $stmt->execute();
        $stmt->close();

        // Remove from cart if already in
        $deleteCart = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_name = ? AND status = 'in_cart'");
        $deleteCart->bind_param("is", $consumer_id, $product_name);
        $deleteCart->execute();
        $deleteCart->close();

        header("Location: order_confirmation.php"); // optional redirection page
        exit();
    }

    if (isset($_POST['cancel'])) {
        $stmt = $conn->prepare("UPDATE cart_items SET status = 'cancelled' WHERE user_id = ? AND product_name = ?");
        $stmt->bind_param("is", $consumer_id, $product_name);
        $stmt->execute();
        $stmt->close();

        header("Location: consumer_offers.php");
        exit();
    }
}

// Fetch offers
$sql = "SELECT * FROM historical_data";
$result = $conn->query($sql);

// Count items in cart
$cartCountResult = $conn->query("SELECT COUNT(*) AS count FROM cart_items WHERE user_id = $consumer_id AND status = 'in_cart'");
$cartData = $cartCountResult->fetch_assoc();
$cart_count = $cartData['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Consumer - Product Offers</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Segoe UI'; background: #f4f6f9; margin: 0; }
    .sidebar { width: 250px; height: 100vh; position: fixed; background: #343a40; color: #fff; padding-top: 20px; }
    .sidebar a { color: #fff; padding: 15px 25px; display: block; text-decoration: none; }
    .sidebar a:hover, .sidebar .active { background: #495057; }
    .topbar { margin-left: 250px; height: 60px; background: #212529; color: #fff; display: flex; justify-content: space-between; align-items: center; padding: 0 30px; }
    .content { margin-left: 250px; padding: 30px; min-height: calc(100vh - 120px); }
    .profile-pic { width: 40px; height: 40px; object-fit: cover; }
    .btn-action { margin: 2px; }
  </style>
</head>
<body>

<div class="sidebar">
  <a href="#"><h3 style="color:red; font-weight:bold;">MEAT KING</h3></a>
  <a class="active" href="consumer_offers.php"><i class="fas fa-tags me-2"></i> Offers</a>
  <a href="consumer_dashboard.php"><i class="fas fa-home me-2"></i> Dashboard</a>
  <a href="cart.php"><i class="fas fa-shopping-cart me-2"></i> View Cart (<?php echo $cart_count; ?>)</a>
  <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>

<div class="topbar">
  <h5 class="mb-0">Consumer - View Offers</h5>
  <div class="d-flex align-items-center">
    <img src="img/user2.jpg" alt="User" class="rounded-circle profile-pic me-2"> Consumer
  </div>
</div>

<div class="content">

  <h2 class="mb-4">Available Product Offers</h2>

  <div class="table-responsive">
    <table class="table table-bordered" id="offersTable">
      <thead class="table-light">
        <tr>
          <th>Product Name</th>
          <th>Current Price ($)</th>
          <th>Offer Price ($)</th>
          <th class="text-center" style="width: 300px;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['product_name']); ?></td>
              <td><?php echo number_format($row['current_price'], 2); ?></td>
              <td><?php echo number_format($row['offer_price'], 2); ?></td>
              <td class="text-center">
                <form method="post" style="display:inline;">
                  <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($row['product_name']); ?>">
                  <input type="hidden" name="offer_price" value="<?php echo htmlspecialchars($row['offer_price']); ?>">
                  <button type="submit" name="add_to_cart" class="btn btn-primary btn-sm btn-action">
                    <i class="fas fa-cart-plus me-1"></i> Add to Cart
                  </button>
                </form>
                <form method="post" style="display:inline;">
                  <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($row['product_name']); ?>">
                  <input type="hidden" name="offer_price" value="<?php echo htmlspecialchars($row['offer_price']); ?>">
                  <button type="submit" name="place_order" class="btn btn-success btn-sm btn-action">
                    <i class="fas fa-check-circle me-1"></i> Place Order
                  </button>
                </form>
                <form method="post" style="display:inline;">
                  <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($row['product_name']); ?>">
                  <input type="hidden" name="offer_price" value="<?php echo htmlspecialchars($row['offer_price']); ?>">
                  <button type="submit" name="cancel" class="btn btn-danger btn-sm btn-action">
                    <i class="fas fa-times-circle me-1"></i> Cancel
                  </button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="4" class="text-center">No offers available.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</div>

<!-- Footer -->
<footer class="footer mt-auto py-3 bg-dark text-white text-center" style="margin-left:250px;">
  <div class="container">
    <span>&copy; 2025 Meat King. All rights reserved.</span>
  </div>
</footer>

</body>
</html>

<?php
$conn->close();
?>
