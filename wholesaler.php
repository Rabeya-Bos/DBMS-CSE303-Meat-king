<?php
session_start();

// For testing purposes, assuming the wholesalerid is stored in session
$_SESSION['wholesalerid'] = 1;
$wholesalerid = $_SESSION['wholesalerid'];

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

    $stmt = $mysqli->prepare("INSERT INTO product (meat_type, cut, price) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $meat_type, $cut, $price);
    $stmt->execute();
    header("Location: wholesaler.php");
    exit();
}

// Process Purchase Record from Retailer
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_purchase'])) {
    $retailer_id = 1; // For now assuming retailer ID is 1
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $purchase_date = date('Y-m-d');
    $price_per_unit = $_POST['price_per_unit'] ?? 100; // Dummy price if not passed
    $total_price = $price_per_unit * $quantity;

    $stmt = $mysqli->prepare("INSERT INTO retailer_purchase_record_t (retailer_id, wholesaler_id, product_id, quantity, purchase_date, price_per_unit, total_price) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisiid", $retailer_id, $wholesalerid, $product_id, $quantity, $purchase_date, $price_per_unit, $total_price);
    $stmt->execute();
    header("Location: wholesaler.php");
    exit();
}

// Update Product Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $edit_product_id = $_POST['edit_product_id'];
    $edit_meat_type = $_POST['edit_meat_type'];
    $edit_cut = $_POST['edit_cut'];
    $edit_price = $_POST['edit_price'];

    $stmt = $mysqli->prepare("UPDATE product SET meat_type = ?, cut = ?, price = ? WHERE product_id = ?");
    $stmt->bind_param("ssdi", $edit_meat_type, $edit_cut, $edit_price, $edit_product_id);
    $stmt->execute();
    header("Location: wholesaler.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Wholesaler Dashboard - MEAT KING</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
  <a href="index.html"><h3 style="color:red; font-weight:bold;">MEAT KING</h3></a>
  <a href="wholesaler.php" class="active"><i class="fa fa-home me-2"></i> Dashboard</a>
  <a href="orderhistory.php"><i class="fa fa-history me-2"></i> Order History</a>
  <a href="loging.php"><i class="fa fa-sign-out-alt me-2"></i> Logout</a>
</div>

<!-- Topbar -->
<div class="topbar bg-dark text-white">
  <button class="btn btn-outline-light me-3" id="sidebarToggle"><i class="fas fa-bars"></i></button>
  <h5 class="mb-0">Wholesaler Dashboard</h5>
  <div>
    <img src="img/user12.jpg" alt="User" class="rounded-circle me-2" style="width:40px;"> Afsana
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
  <!-- joss Reminders Section with New Colors -->
<h3 class="mt-5 mb-4">Reminders</h3>
<div class="row g-3">

  <!-- Wholesaler Reminder -->
  <div class="col-md-3">
    <div class="card-box reminder-card" style="background: linear-gradient(135deg, #ff6f91, #ff9671);">
      <h5><i class="fas fa-box-open me-2"></i>From Wholesaler</h5>
      <p>New stock arriving Friday. Prepare storage facilities accordingly.</p>
    </div>
  </div>

  <!-- Weather Reminder -->
  <div class="col-md-3">
    <div class="card-box reminder-card" style="background: linear-gradient(135deg, #00b894, #55efc4);">
      <h5><i class="fas fa-cloud-sun me-2"></i>Weather Update</h5>
      <p>Heavy rains expected this weekend. Plan your deliveries carefully!</p>
    </div>
  </div>

  <!-- Nutritionist Reminder -->
  <div class="col-md-3">
    <div class="card-box reminder-card" style="background: linear-gradient(135deg, #fdcb6e, #ffeaa7);">
     <h5>🥩 From Nutritionist</h5>
     <p>Focus on lean meat cuts this month based on health trends.</p>
    </div>
  </div>

  <!-- Livestock Farmer Reminder -->
<div class="col-md-3">
  <div class="card-box reminder-card" style="background: linear-gradient(135deg, #56ab2f, #a8e063); color: #fff;">
    <h5><i class="fas fa-cow me-2"></i>From Livestock Farmer</h5>
    <p>Stock levels are low. Time to increase livestock production.</p>
  </div>
</div>


<!-- Extra CSS for joss Styling -->
<style>
  .reminder-card {
    color: #333;
    padding: 20px;
    border-radius: 15px;
    height: 180px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    transition: all 0.4s ease;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    animation: fadeIn 1s ease-in;
  }

  .reminder-card:hover {
    transform: translateY(-8px) scale(1.05);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
  }

  .reminder-card h5 {
    font-weight: 700;
    margin-bottom: 12px;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #222;
  }

  .reminder-card h5 i {
    font-size: 1.4rem;
  }

  .reminder-card p {
    font-size: 0.95rem;
    line-height: 1.5;
    margin: 0 10px;
    color: #444;
  }

  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>

<!-- Font Awesome CDN for icons (if not included already) -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>


  <!-- Product List -->
  <h2 class="mb-4">Available Products</h2>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Meat Type</th>
        <th>Cut</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $results = $mysqli->query("SELECT * FROM product");
    while ($row = $results->fetch_assoc()) {
        $product_id = $row['product_id'];
        echo "<tr>
                <td>{$row['meat_type']}</td>
                <td>{$row['cut']}</td>
                <td>{$row['price']}</td>
                <td>
                  <form method='POST' action='wholesaler.php'>
                    <input type='hidden' name='product_id' value='{$product_id}' />
                    <input type='hidden' name='price_per_unit' value='{$row['price']}' />
                    <input type='number' name='quantity' class='form-control' required>
                </td>
                <td>
                    <button type='submit' name='process_purchase' class='btn btn-primary btn-sm mb-1'>Sell</button>
                  </form>
                  <a href='buy_product.php?product_id={$product_id}' class='btn btn-success btn-sm mb-1'>Buy</a>
                  <button type='button' class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editModal{$product_id}'>Edit</button>

                  <!-- Edit Modal -->
                  <div class='modal fade' id='editModal{$product_id}' tabindex='-1' aria-labelledby='editModalLabel{$product_id}' aria-hidden='true'>
                    <div class='modal-dialog'>
                      <div class='modal-content'>
                        <form method='POST' action='wholesaler.php'>
                          <div class='modal-header'>
                            <h5 class='modal-title'>Edit Product</h5>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                          </div>
                          <div class='modal-body'>
                            <input type='hidden' name='edit_product_id' value='{$product_id}'>
                            <div class='mb-3'>
                              <label class='form-label'>Meat Type</label>
                              <input type='text' name='edit_meat_type' class='form-control' value='{$row['meat_type']}' required>
                            </div>
                            <div class='mb-3'>
                              <label class='form-label'>Cut</label>
                              <input type='text' name='edit_cut' class='form-control' value='{$row['cut']}' required>
                            </div>
                            <div class='mb-3'>
                              <label class='form-label'>Price</label>
                              <input type='number' name='edit_price' class='form-control' value='{$row['price']}' required>
                            </div>
                          </div>
                          <div class='modal-footer'>
                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                            <button type='submit' name='update_product' class='btn btn-primary'>Save Changes</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>";
    }
    ?>
    </tbody>
  </table>

  <!-- Add Product Form -->
  <h3 class="mt-5">Add New Product</h3>
  <form method="POST" action="wholesaler.php">
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
  <p class="mb-0">&copy; 2025 Rabeya. All Rights Reserved.</p>
</footer>
</body>
</html>
