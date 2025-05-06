<?php
session_start();

// Assuming consumerid is stored in session for demo
$_SESSION['consumerid'] = 1;
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

    $stmt = $mysqli->prepare("INSERT INTO product (meat_type, cut, price) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $meat_type, $cut, $price);
    $stmt->execute();
    header("Location: consumer.php");
    exit();
}

// Edit Product Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $meat_type = $_POST['meat_type'];
    $cut = $_POST['cut'];
    $price = $_POST['price'];

    $stmt = $mysqli->prepare("UPDATE product SET meat_type = ?, cut = ?, price = ? WHERE product_id = ?");
    $stmt->bind_param("sssi", $meat_type, $cut, $price, $product_id);
    $stmt->execute();
    header("Location: consumer.php");
    exit();
}

// Delete Product Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $product_id = $_POST['product_id'];

    $stmt = $mysqli->prepare("DELETE FROM product WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    header("Location: consumer.php");
    exit();
}

// Place Order Logic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $order_date = $_POST['order_date'];

    if (isset($consumerid)) {
        $stmt = $mysqli->prepare("INSERT INTO orders_o_t (consumerid, product_id, quantity, order_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $consumerid, $product_id, $quantity, $order_date);
        $stmt->execute();
        header("Location: consumer.php");
        exit();
    } else {
        echo "Consumer ID missing. Please login.";
    }
}

// Sample historical price and quantity data for PED calculation (replace with your DB data)
$price_quantity_data = [
    1 => ['old_price' => 100, 'new_price' => 90, 'old_qty' => 50, 'new_qty' => 70],
    2 => ['old_price' => 200, 'new_price' => 180, 'old_qty' => 30, 'new_qty' => 40],
    3 => ['old_price' => 150, 'new_price' => 150, 'old_qty' => 40, 'new_qty' => 40], // zero elasticity example
];

// PED calculation function
function calculate_ped($old_price, $new_price, $old_qty, $new_qty) {
    if ($old_price == 0 || $old_qty == 0) return 0;

    $percentage_change_qty = (($new_qty - $old_qty) / $old_qty) * 100;
    $percentage_change_price = (($new_price - $old_price) / $old_price) * 100;

    if ($percentage_change_price == 0) return 0;

    return round($percentage_change_qty / $percentage_change_price, 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Consumer Dashboard - MEAT KING</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f6f9; }
        .sidebar { width: 250px; height: 100vh; position: fixed; background-color: #343a40; color: #fff; padding-top: 20px; }
        .sidebar a { color: #fff; padding: 15px 25px; display: block; text-decoration: none; }
        .sidebar a:hover, .sidebar .active { background-color: #495057; }
        .topbar { margin-left: 250px; height: 60px; background: #fff; display: flex; justify-content: space-between; align-items: center; padding: 0 30px; border-bottom: 1px solid #ccc; }
        .content { margin-left: 250px; padding: 30px; }
        .card-box { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05); text-align: center; margin-bottom: 30px; }
        .reminder-card { border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.15); padding: 20px; color: white; min-height: 160px; display: flex; flex-direction: column; justify-content: center; transition: transform 0.3s ease, box-shadow 0.3s ease; cursor: pointer; }
        .reminder-card:hover { transform: translateY(-8px); box-shadow: 0 12px 30px rgba(0,0,0,0.3); }
        .reminder-card h5 { font-weight: 700; font-size: 1.25rem; margin-bottom: 12px; display: flex; align-items: center; }
        .reminder-card p { font-size: 1rem; line-height: 1.4; }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <a href="#"><h3 style="color:red; font-weight:bold;">MEAT KING</h3></a>
  <a class="active" href="#"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
  <a href="product1.php"><i class="fas fa-industry me-2"></i> Production Information</a>
  <a href="farmer_dashboard1.php"><i class="fas fa-history me-2"></i> Historical Production Data</a>
  <a href="consumer12.php"><i class="fas fa-users me-2"></i> Consumer Demand Data</a>
  <a href="real time supply.html"><i class="fas fa-shipping-fast me-2"></i> Real-time Supply</a>
  <a href="marketTrend.html"><i class="fas fa-chart-line me-2"></i> Market Trends</a>
  <a href="nutritionist.php"><i class="fas fa-apple-alt me-2"></i> Nutritionist</a>
  <a href="admin.php"><i class="fas fa-handshake me-2"></i>Buyer/seller</a>
  <a href="loging.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>

<!-- Topbar -->
<div class="topbar bg-dark text-white d-flex justify-content-between align-items-center px-3 py-2">
    <div class="d-flex align-items-center">
        <button class="btn btn-outline-light me-3" id="sidebarToggle"><i class="fas fa-bars"></i></button>
        <h5 class="mb-0">Consumer Demand Data</h5>
    </div>
    <div>
        <img src="img/user2.jpg" alt="Profile" class="rounded-circle" style="width: 35px; height: 35px;">
    </div>
</div>


<!-- Content -->
<div class="content" id="mainContent">

    <div class="row mb-4">
        <div class="col-md-3"><a href="offer.html" class="card-box text-decoration-none"><h6>Offer</h6><h3><i class="fa fa-gift"></i></h3></a></div>
        <div class="col-md-3"><a href="product.html" class="card-box text-decoration-none"><h6>Products</h6><h3><i class="fa fa-cogs"></i></h3></a></div>
        <div class="col-md-3"><a href="location.html" class="card-box text-decoration-none"><h6>Locations</h6><h3><i class="fa fa-globe"></i></h3></a></div>
        <div class="col-md-3"><a href="order.html" class="card-box text-decoration-none"><h6>Orders</h6><h3><i class="fa fa-truck"></i></h3></a></div>
    </div>
    <div style="display: flex; gap: 2rem; justify-content: center; margin-bottom: 2rem;">
  <!-- Left Chart -->
  <div style="width: 300px;">
    <h6 style="font-size: 0.9rem; text-align: center; margin-bottom: 0.5rem;">
      Bangladesh Regional Meat Consumption Preferences (Left)
    </h6>
    <canvas id="regionalChartLeft" width="300" height="180"></canvas>
  </div>

  <!-- Right Chart -->
  <div style="width: 280px;">
    <h6 style="font-size: 0.9rem; text-align: center; margin-bottom: 0.5rem;">
      Bangladesh Regional Meat Consumption Preferences (Right)
    </h6>
    <canvas id="regionalChartRight" width="280" height="170"></canvas>
  </div>
</div>
<script>
  const products = ['Beef', 'Chicken', 'Mutton', 'Duck', 'Fish'];
  const demandUnits = [500, 700, 300, 200, 800]; // Units demanded per month
  const pricesPerKg = [600, 350, 800, 450, 250]; // Price per kg

  // Left chart - Bar chart for Demand
  const ctxLeft = document.getElementById('regionalChartLeft').getContext('2d');
  new Chart(ctxLeft, {
    type: 'bar',
    data: {
      labels: products,
      datasets: [{
        label: 'Monthly Product Demand (Units)',
        data: demandUnits,
        backgroundColor: 'rgba(75, 192, 192, 0.6)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1,
        borderRadius: 4,
      }]
    },
    options: {
      responsive: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: ctx => ctx.parsed.y + ' units'
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: { display: true, text: 'Units', font: { size: 10 } },
          ticks: { font: { size: 10 } }
        },
        x: {
          title: { display: true, text: 'Product', font: { size: 10 } },
          ticks: { font: { size: 10 } }
        }
      }
    }
  });

  // Right chart - Line chart for Price
  const ctxRight = document.getElementById('regionalChartRight').getContext('2d');
  new Chart(ctxRight, {
    type: 'line',
    data: {
      labels: products,
      datasets: [{
        label: 'Price per Kg (BDT)',
        data: pricesPerKg,
        backgroundColor: 'rgba(255, 159, 64, 0.2)',
        borderColor: 'rgba(255, 159, 64, 1)',
        borderWidth: 2,
        fill: true,
        tension: 0.4,
        pointRadius: 5,
        pointBackgroundColor: 'rgba(255, 159, 64, 1)'
      }]
    },
    options: {
      responsive: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: ctx => '৳ ' + ctx.parsed.y + ' per kg'
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          title: { display: true, text: 'Price (৳)', font: { size: 10 } },
          ticks: { font: { size: 10 } }
        },
        x: {
          title: { display: true, text: 'Product', font: { size: 10 } },
          ticks: { font: { size: 10 } }
        }
      }
    }
  });
</script>



    <div class="row g-4">
        <div class="col-md-4">
            <div class="reminder-card" style="background: linear-gradient(135deg, #43cea2, #185a9d);">
                <h5><i class="fas fa-cow me-3"></i>From Livestock Farmer</h5>
                <p>Fresh organic meat available. Check out new arrivals!</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="reminder-card" style="background: linear-gradient(135deg, #ff6a00, #ee0979);">
                <h5><i class="fas fa-store me-3"></i>From Retailer</h5>
                <p>Special discounts on bulk purchases this week.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="reminder-card" style="background: linear-gradient(135deg, #11998e, #38ef7d);">
                <h5><i class="fas fa-tags me-3"></i>Today's Offer</h5>
                <p>Get 15% off on all beef cuts. Limited time only!</p>
            </div>
        </div>
    </div>

    <h2 class="mb-4 mt-5">Add New Product</h2>
    <form method="POST" action="consumer.php" class="row g-3 mb-4">
        <div class="col-md-4">
            <input type="text" name="meat_type" class="form-control" placeholder="Meat Type" required>
        </div>
        <div class="col-md-4">
            <input type="text" name="cut" class="form-control" placeholder="Cut" required>
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" name="price" class="form-control" placeholder="Price" required>
        </div>
        <div class="col-md-2">
            <button type="submit" name="add_product" class="btn btn-success w-100">Add Product</button>
        </div>
    </form>

    <!-- Product List -->
    <h2 class="mb-4">Available Products</h2>
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
            $results = $mysqli->query("SELECT * FROM product");
            while ($row = $results->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['meat_type']}</td>
                    <td>{$row['cut']}</td>
                    <td>{$row['price']}</td>
                    <td>
                      <form method='POST' action='consumer.php' class='d-flex gap-2 align-items-center'>
                        <input type='number' name='quantity' class='form-control' style='width: 80px;' required min='1'>
                    </td>
                    <td>
                        <input type='date' name='order_date' class='form-control' required>
                    </td>
                    <td class='d-flex gap-1'>
                        <input type='hidden' name='product_id' value='{$row['product_id']}'>

                        <button type='submit' name='place_order' class='btn btn-primary btn-sm'>Order</button>
                      </form>

                      <form method='POST' action='consumer.php' class='d-inline'>
                        <input type='hidden' name='product_id' value='{$row['product_id']}'>
                        <input type='hidden' name='meat_type' value='{$row['meat_type']}'>
                        <input type='hidden' name='cut' value='{$row['cut']}'>
                        <input type='hidden' name='price' value='{$row['price']}'>
                        <button type='submit' name='edit_product' class='btn btn-warning btn-sm'>Edit</button>
                      </form>

                      <form method='POST' action='consumer.php' class='d-inline'>
                        <input type='hidden' name='product_id' value='{$row['product_id']}'>
                        <button type='submit' name='delete_product' class='btn btn-danger btn-sm'>Delete</button>
                      </form>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- PED Table -->
<h2 class="mt-5 mb-4">Price Elasticity of Demand (PED) Analysis</h2>

<!-- Add New Button -->
<div class="mb-2 text-end">
    <a href="add_ped.php" class="btn btn-success btn-sm">+ Add New</a>

</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Product ID</th>
            <th>Old Price</th>
            <th>New Price</th>
            <th>Old Quantity</th>
            <th>New Quantity</th>
            <th>PED Value</th>
            <th>Actions</th> <!-- New column for Edit/Delete -->
        </tr>
    </thead>
    <tbody>
        <?php
        $ped_values = [];
        foreach ($price_quantity_data as $pid => $data) {
            $ped = calculate_ped($data['old_price'], $data['new_price'], $data['old_qty'], $data['new_qty']);
            $ped_values[$pid] = $ped;
            echo "<tr>
                <td>{$pid}</td>
                <td>{$data['old_price']}</td>
                <td>{$data['new_price']}</td>
                <td>{$data['old_qty']}</td>
                <td>{$data['new_qty']}</td>
                <td>{$ped}</td>
                <td>
                    <a href='edit_ped.php?id={$pid}' class='btn btn-warning btn-sm'>Edit</a>
                    <a href='delete_ped.php?id={$pid}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this entry?\")'>Delete</a>
                </td>
            </tr>";
        }
        ?>
    </tbody>
</table>


    <!-- PED Chart -->
    <h3 class="mt-5">PED Values Chart</h3>
    <canvas id="pedChart" width="600" height="300"></canvas>

</div>

<script>
const ctx = document.getElementById('pedChart').getContext('2d');
const pedChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_keys($ped_values)); ?>,
        datasets: [{
            label: 'PED Value',
            data: <?php echo json_encode(array_values($ped_values)); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1,
        }]
    },
    options: {
        scales: {
            y: { beginAtZero: true, title: { display: true, text: 'Elasticity Value' } },
            x: { title: { display: true, text: 'Product ID' } }
        }
    }
});

</script>
<footer class="text-center py-3 mt-5" style="background-color: #343a40; color: #fff;">
    <p class="mb-0">Copyright &copy; 2025 Rabeya. All Rights Reserved.</p>
</footer>

</body>
</html>
