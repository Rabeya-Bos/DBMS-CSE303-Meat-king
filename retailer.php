<?php
session_start();

// For testing purposes, assuming the retailerid is stored in session
$_SESSION['retailerid'] = 1; // Example retailer ID
$retailerid = $_SESSION['retailerid'];

// DB connection
$mysqli = new mysqli("localhost", "root", "", "mking");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Buy Product Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy_product'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $order_date = $_POST['order_date'];

    if (isset($retailerid)) {
        $stmt = $mysqli->prepare("INSERT INTO orders_o_t (retailerid, product_id, quantity, order_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $retailerid, $product_id, $quantity, $order_date);
        $stmt->execute();
        header("Location: retailer.php");
        exit();
    } else {
        echo "Retailer ID missing.";
    }
}

// Sell Product Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sell_product'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $order_date = $_POST['order_date'];

    $stmt = $mysqli->prepare("INSERT INTO retailer_sales (retailerid, product_id, quantity, sell_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $retailerid, $product_id, $quantity, $order_date);
    $stmt->execute();
    header("Location: retailer.php");
    exit();
}

// Edit Product Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $new_quantity = $_POST['quantity'];

    // Update logic (Example: update stock)
    $stmt = $mysqli->prepare("UPDATE product SET quantity = ? WHERE product_id = ?");
    $stmt->bind_param("ii", $new_quantity, $product_id);
    $stmt->execute();
    header("Location: retailer.php");
    exit();
}

// Add Product Logic (Admin can add products)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $meat_type = $_POST['meat_type'];
    $cut = $_POST['cut'];
    $price = $_POST['price'];

    $stmt = $mysqli->prepare("INSERT INTO product (meat_type, cut, price) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $meat_type, $cut, $price);
    $stmt->execute();
    header("Location: retailer.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Retailer Dashboard - MEAT KING</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6f9;
    }
    .sidebar {
      width: 250px;
      height: 100vh;
      position: fixed;
      background-color: #343a40;
      color: #fff;
      padding-top: 20px;
    }
    .sidebar a {
      color: #fff;
      padding: 15px 25px;
      display: block;
      text-decoration: none;
    }
    .sidebar a:hover, .sidebar .active {
      background-color: #495057;
    }
    .topbar {
      margin-left: 250px;
      height: 60px;
      background: #fff;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 30px;
      border-bottom: 1px solid #ccc;
    }
    .content {
      margin-left: 250px;
      padding: 30px;
    }
    .card-box {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      text-align: center;
      margin-bottom: 30px;
    }
  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <a href="#"><h3 style="color:red; font-weight:bold;">MEAT KING</h3></a>
  <a href="retailer.php" class="active"><i class="fa fa-home me-2"></i> Dashboard</a>
  <a href="orderhistory.php"><i class="fa fa-history me-2"></i> Order History</a>
  <a href="loging.php"><i class="fa fa-sign-out-alt me-2"></i> Logout</a>
</div>

<!-- Topbar -->
<div class="topbar bg-dark text-white">
  <button class="btn btn-outline-light me-3" id="sidebarToggle"><i class="fas fa-bars"></i></button>
  <h5 class="mb-0">Retailer Dashboard</h5>
  <div>
    <img src="img/user5.jpg" alt="User" class="rounded-circle me-2" style="width:40px;"> Kazi Taushia Nahar
  </div>
</div>

<!-- Main Content -->
<div class="content">

  <!-- Cards -->
  <div class="row mb-4">
    <div class="col-md-3">
      <a href="offer.html" class="card-box text-decoration-none">
        <h6>Offer</h6>
        <h3><i class="fa fa-gift"></i></h3>
      </a>
    </div>
    <div class="col-md-3">
      <a href="product.html" class="card-box text-decoration-none">
        <h6>Products</h6>
        <h3><i class="fa fa-cogs"></i></h3>
      </a>
    </div>
    <div class="col-md-3">
      <a href="location.html" class="card-box text-decoration-none">
        <h6>Locations</h6>
        <h3><i class="fa fa-globe"></i></h3>
      </a>
    </div>
    <div class="col-md-3">
      <a href="order.html" class="card-box text-decoration-none">
        <h6>Orders</h6>
        <h3><i class="fa fa-truck"></i></h3>
      </a>
    </div>
  </div>
  <!-- joss Reminders Section with Icons -->
<h3 class="mt-5 mb-4">Reminders</h3>
<div class="row g-3">

  <!-- Wholesaler Reminder -->
  <div class="col-md-3">
    <div class="card-box reminder-card" style="background: linear-gradient(135deg, #6a11cb, #2575fc);">
      <h5><i class="fas fa-box-open me-2"></i>From Wholesaler</h5>
      <p>New stock arriving Friday. Prepare storage facilities accordingly.</p>
    </div>
  </div>

  <!-- Weather Reminder -->
  <div class="col-md-3">
    <div class="card-box reminder-card" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
      <h5><i class="fas fa-cloud-sun me-2"></i>Weather Update</h5>
      <p>Heavy rains expected this weekend. Plan your deliveries carefully!</p>
    </div>
  </div>

  <!-- Nutritionist Reminder -->
  <div class="col-md-3">
    <div class="card-box reminder-card" style="background: linear-gradient(135deg, #f7971e, #ffd200);">
     <h5>🥩 From Nutritionist</h5>
     <p>Focus on lean meat cuts this month based on health trends.</p>
    </div>
  </div>

  <!-- Consumer Reminder -->
<div class="col-md-3">
  <div class="card-box reminder-card" style="background: linear-gradient(135deg, #ff9a9e, #fad0c4); color: #fff;">
    <h5><i class="fas fa-users me-2"></i> From Consumer</h5>
    <p>Looking for more grass-fed and organic meat options.</p>
  </div>
</div>

<!-- Extra CSS for Reminder joss Styling -->
<style>
  .reminder-card {
    color: #fff;
    padding: 20px;
    border-radius: 15px;
    height: 180px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    transition: all 0.4s ease;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    animation: fadeIn 1s ease-in;
  }

  .reminder-card:hover {
    transform: translateY(-8px) scale(1.05);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
  }

  .reminder-card h5 {
    font-weight: 700;
    margin-bottom: 12px;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .reminder-card h5 i {
    font-size: 1.4rem;
  }

  .reminder-card p {
    font-size: 0.95rem;
    line-height: 1.5;
    margin: 0 10px;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>

<!-- Font Awesome CDN for icons (if not included already) -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>


  <!-- Available Products -->
  <h2 class="mb-4">Available Products</h2>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Meat Type</th>
        <th>Cut</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Order Date</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $results = $mysqli->query("SELECT * FROM product");
      while ($row = $results->fetch_assoc()) {
        echo "<tr>
              <form method='POST' action='retailer.php'>
                <td>{$row['meat_type']}</td>
                <td>{$row['cut']}</td>
                <td>{$row['price']}</td>
                <td><input type='number' name='quantity' class='form-control' required></td>
                <td><input type='date' name='order_date' class='form-control' required></td>
                <td>
                  <input type='hidden' name='product_id' value='{$row['product_id']}'>
                  <div class='d-flex gap-1'>
                    <button type='submit' name='buy_product' class='btn btn-primary btn-sm'><i class='fa fa-cart-plus'></i> Buy</button>
                    <button type='submit' name='sell_product' class='btn btn-success btn-sm'><i class='fa fa-dollar-sign'></i> Sell</button>
                    <button type='submit' name='edit_product' class='btn btn-warning btn-sm'><i class='fa fa-edit'></i> Edit</button>
                  </div>
                </td>
              </form>
            </tr>";
      }
      ?>
    </tbody>
  </table>

  <!-- Add New Product -->
  <h3 class="mt-5">Add New Product</h3>
  <form method="POST" action="retailer.php">
    <div class="mb-3">
      <label for="meat_type" class="form-label">Meat Type</label>
      <input type="text" name="meat_type" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="cut" class="form-label">Cut</label>
      <input type="text" name="cut" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="price" class="form-label">Price</label>
      <input type="number" name="price" class="form-control" required>
    </div>
    <button type="submit" name="add_product" class="btn btn-success">Add Product</button>
  </form>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Footer -->
<footer class="text-center py-3 mt-5" style="background-color: #343a40; color: #fff;">
  <p class="mb-0">Copyright &copy; 2025 Rabeya. All Rights Reserved.</p>
</footer>
</body>
</html>
