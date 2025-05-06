<?php
session_start();

// For testing purposes, assuming the consumerid is stored in session
$_SESSION['consumerid'] = 1; // Example consumer ID for testing
$consumerid = $_SESSION['consumerid'];

// DB connection
$mysqli = new mysqli("localhost", "root", "", "mking");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Add Product Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $meat_type = $_POST['meat_type'];
    $cut = $_POST['cut'];
    $price = $_POST['price'];

    $stmt = $mysqli->prepare("INSERT INTO coustomer (meat_type, cut, price) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $meat_type, $cut, $price);
    $stmt->execute();
    header("Location: consumer_rood.php"); // Reload page to show the new product
    exit();
}

// Edit Product Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $meat_type = $_POST['meat_type'];
    $cut = $_POST['cut'];
    $price = $_POST['price'];

    $stmt = $mysqli->prepare("UPDATE coustomer SET meat_type = ?, cut = ?, price = ? WHERE product_id = ?");
    $stmt->bind_param("sssi", $meat_type, $cut, $price, $product_id);
    $stmt->execute();
    header("Location: consumer_rood.php"); // Reload page after editing the product
    exit();
}

// Delete Product Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];

    $stmt = $mysqli->prepare("DELETE FROM coustomer WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    header("Location: consumer_rood.php"); // Reload page after deleting the product
    exit();
}

// Place Order Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $order_date = $_POST['order_date'];

    if (isset($consumerid)) {
        $stmt = $mysqli->prepare("INSERT INTO rood (consumerid, product_id, quantity, order_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $consumerid, $product_id, $quantity, $order_date);
        $stmt->execute();
        header("Location: consumer_rood.php"); // Redirect to the same page after placing the order
        exit();
    } else {
        echo "Consumer ID is missing. Please log in again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Consumer Dashboard - MEAT KING</title>
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
      transition: all 0.3s;
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
      transition: all 0.3s;
    }
    .content {
      margin-left: 250px;
      padding: 30px;
      transition: all 0.3s;
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
  <a href="index.html"><h3 style="color:red; font-weight:bold;">MEAT KING</h3></a>
  <a href="consumer_rood.php" class="active"><i class="fa fa-home me-2"></i> Dashboard</a>
  <a href="orderhistory.php"><i class="fa fa-history me-2"></i> Order History</a>
  <a href="loging.php"><i class="fa fa-sign-out-alt me-2"></i> Logout</a>
</div>

<!-- Topbar -->
<div class="topbar bg-dark text-white">
  <button class="btn btn-outline-light me-3" id="sidebarToggle"><i class="fas fa-bars"></i></button>
  <h5 class="mb-0">Consumer Dashboard</h5>
  <div>
    <img src="file:///C:/Users/User/Desktop/dbms/MEAT%20KING/img/user2.jpg" class="rounded-circle me-2" style="width:40px;"> Consumer Name
  </div>
</div>
<!-- Content -->
<div class="content" id="mainContent">
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
</div>

<!-- Content -->
<div class="content">
  <h2 class="mb-4">Available Products</h2>

  <!-- Product List with Form Table -->
  <form method="POST" action="consumer_rood.php">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Meat Type</th>
          <th>Cut</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Order Date</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $results = $mysqli->query("SELECT * FROM coustomer");
        while ($row = $results->fetch_assoc()) {
          echo "<tr>
                  <td>{$row['meat_type']}</td>
                  <td>{$row['cut']}</td>
                  <td>{$row['price']}</td>
                  <td><input type='number' name='quantity' class='form-control' required></td>
                  <td><input type='date' name='order_date' class='form-control' required></td>
                  <td>
                    <form method='POST' action='consumer_rood.php' style='display:inline-block; margin-bottom: 4px;'>
                      <input type='hidden' name='product_id' value='{$row['product_id']}' />
                      <input type='number' name='quantity' class='form-control mb-1' placeholder='Quantity' required />
                      <input type='date' name='order_date' class='form-control mb-1' required />
                      <button type='submit' name='place_order' class='btn btn-primary btn-sm w-100'>Place Order</button>
                    </form>
                    <form method='POST' action='consumer_rood.php' style='display:inline-block; margin-left:5px; margin-bottom:4px;'>
                      <input type='hidden' name='product_id' value='{$row['product_id']}' />
                      <button type='submit' name='delete_product' class='btn btn-danger btn-sm'>Delete</button>
                    </form>
                    <form method='POST' action='consumer_rood.php' style='display:inline-block; margin-left:5px;'>
                      <input type='hidden' name='product_id' value='{$row['product_id']}' />
                      <input type='text' name='meat_type' value='{$row['meat_type']}' required />
                      <input type='text' name='cut' value='{$row['cut']}' required />
                      <input type='text' name='price' value='{$row['price']}' required />
                      <button type='submit' name='edit_product' class='btn btn-warning btn-sm'>Edit</button>
                    </form>
                  </td>
                </tr>";
        }
        ?>
      </tbody>
    </table>
  </form>

  <!-- Add Product Form -->
  <h3>Add New Product</h3>
  <form method="POST" action="consumer_rood.php">
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

<footer class="text-center py-3 mt-5" style="background-color: #343a40; color: #fff;">
  <p class="mb-0">Copyright &copy; 2025 Rabeya. All Rights Reserved.</p>
</footer>
</body>
</html>
