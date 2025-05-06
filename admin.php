<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "mking";
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";

// Handle insert/update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $product_id = intval($_POST['product_id']);
    $product_name = $conn->real_escape_string($_POST['product_name']);
    $person_name = $conn->real_escape_string($_POST['person_name']);
    $quantity = floatval($_POST['quantity']);
    $price = floatval($_POST['price']);
    $review = $conn->real_escape_string($_POST['review']);

    if (!empty($_POST['edit_id'])) {
        $edit_id = intval($_POST['edit_id']);
        $stmt = $conn->prepare("UPDATE product_transactions SET product_id=?, product_name=?, person_name=?, quantity=?, price=?, review=? WHERE id=?");
        $stmt->bind_param("issddsi", $product_id, $product_name, $person_name, $quantity, $price, $review, $edit_id);
        $message = $stmt->execute() ? "Record updated!" : "Update error: " . $stmt->error;
        $stmt->close();
    } else {
        $stmt = $conn->prepare("INSERT INTO product_transactions (product_id, product_name, person_name, quantity, price, review) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdds", $product_id, $product_name, $person_name, $quantity, $price, $review);
        $message = $stmt->execute() ? "Record added!" : "Insert error: " . $stmt->error;
        $stmt->close();
    }
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM product_transactions WHERE id=?");
    $stmt->bind_param("i", $delete_id);
    $message = $stmt->execute() ? "Record deleted!" : "Delete error: " . $stmt->error;
    $stmt->close();
}

// Fetch all records
$result = $conn->query("SELECT * FROM product_transactions ORDER BY created_at DESC");

// Search handling
$search = isset($_GET['search']) ? $_GET['search'] : '';

if ($search) {
    $search = $conn->real_escape_string($search);
    $sql = "SELECT * FROM product_transactions WHERE 
            product_name LIKE '%$search%' OR 
            person_name LIKE '%$search%' OR 
            review LIKE '%$search%' 
            ORDER BY created_at DESC";
} else {
    $sql = "SELECT * FROM product_transactions ORDER BY created_at DESC";
}

$result = $conn->query($sql);


// Edit data if exists
$edit_record = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $res = $conn->query("SELECT * FROM product_transactions WHERE id=$edit_id");
    if ($res && $res->num_rows > 0) {
        $edit_record = $res->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard - Rabeya</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { font-family: 'Segoe UI'; background: #f4f6f9; margin: 0; }
    .sidebar { width: 250px; height: 100vh; position: fixed; background: #343a40; color: #fff; padding-top: 20px; }
    .sidebar a { color: #fff; padding: 15px 25px; display: block; text-decoration: none; }
    .sidebar a:hover, .sidebar .active { background: #495057; }
    .topbar { margin-left: 250px; height: 60px; background: #212529; color: #fff; display: flex; justify-content: space-between; align-items: center; padding: 0 30px; }
    .content { margin-left: 250px; padding: 30px; }
    .card-box { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 30px; text-align: center; }
    .profile-pic { width: 40px; height: 40px; object-fit: cover; }
    .small-chart { width: 100%; height: 200px; }
    .calendar-container { background: rgba(75,192,192,0.7); padding: 10px; border-radius: 8px; color: #fff; }
    .calendar-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px; }
    .calendar-cell { background: rgba(255,255,255,0.3); text-align: center; padding: 5px; border-radius: 4px; }
    .calendar-today { background-color: #084298 !important; font-weight: bold; }
    .sticky-note {
      background: #fff3cd;
      border-left: 5px solid #ffc107;
      padding: 15px;
      font-size: 14px;
      text-align: left;
      border-radius: 8px;
      box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
    }
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
  <a href="realtimesupply.php"><i class="fas fa-shipping-fast me-2"></i> Real-time Supply</a>
  <a href="marketTrend.php"><i class="fas fa-chart-line me-2"></i> Market Trends</a>
  <a href="nutritionist1.php"><i class="fas fa-apple-alt me-2"></i>Recomemandator</a>
  <a href="buyerseller_directory.php"><i class="fas fa-handshake me-2"></i>Buyer/seller</a>
  <a href="loging.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>



<div class="topbar">
  <h5 class="mb-0">Admin Dashboard</h5>
  <div class="d-flex align-items-center">
    <img src="img/user2.jpg" alt="User" class="rounded-circle profile-pic me-2"> Rabeya
  </div>
</div>

<div class="content">
  <h2>Welcome, Rabeya </h2>

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<div class="row">
  <div class="col-md-3">
    <a href="livestock.php" class="text-decoration-none">
      <div class="card-box text-center p-3">
        <i class="fas fa-cow fa-3x mb-2"></i>
        <h5>Livestock</h5>
        <h2></h2>
      </div>
    </a>
  </div>
  <div class="col-md-3">
    <a href="product.php" class="text-decoration-none">
      <div class="card-box text-center p-3">
        <i class="fas fa-box-open fa-3x mb-2"></i>
        <h5>Products in Stock</h5>
        <h2></h2>
      </div>
    </a>
  </div>
  <div class="col-md-3">
    <a href="order.html" class="text-decoration-none">
      <div class="card-box text-center p-3">
        <i class="fas fa-shopping-cart fa-3x mb-2"></i>
        <h5>Orders Today</h5>
        <h2></h2>
      </div>
    </a>
  </div>
  <div class="col-md-3">
    <a href="consumer.php" class="text-decoration-none">
      <div class="card-box text-center p-3">
        <i class="fas fa-user fa-3x mb-2"></i>
        <h5>Consumer</h5>
        <h2></h2>
      </div>
    </a>
  </div>
</div>

  <div class="row">
    <div class="col-md-6">
      <div class="card-box"><h5>Orders Summary</h5><canvas id="myPieChart" class="small-chart"></canvas></div>
    </div>
    <div class="col-md-6">
      <div class="card-box">
        <h5>Product Price Chart</h5>
        <canvas id="myDataChart" class="small-chart mb-3"></canvas>
        <div class="calendar-container">
          <h6 class="text-center">April</h6>
          <div class="calendar-grid" id="staticCalendar">
            <?php for ($i = 1; $i <= 30; $i++): ?>
              <div class="<?= date('j') == $i ? 'calendar-cell calendar-today' : 'calendar-cell' ?>"><?= $i ?></div>
            <?php endfor; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mb-4">
  <!-- WEATHER CARD -->
  <div class="col-md-6">
    <div class="card-box" style="background: linear-gradient(135deg, rgba(54, 162, 235, 0.7), rgba(255, 206, 86, 0.7)); color: #000;">
      <h4>Weather</h4>
      <p>Today's weather: Sunny, 30°C</p>

      <!-- REMINDER BAR -->
      <div class="alert alert-info py-2 px-3 mt-3" style="font-size: 14px; border-left: 5px solid #0d6efd;">
        <strong>🌤 Tip:</strong> Check humidity levels for proper feed storage!
      </div>

      <!-- IMAGE UPLOAD -->
      <form class="mt-3" enctype="multipart/form-data">
        <label for="weatherImage" class="form-label">Upload Weather Snapshot (optional):</label>
        <input type="file" class="form-control" id="weatherImage" name="weatherImage" accept="image/*">
      </form>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card-box d-flex flex-column" style="height: 100%;">
      <h4>Send Notification</h4>
      <form>
        <textarea class="form-control mb-2" rows="4" placeholder="Type your message..." style="resize: vertical;"></textarea>
        <button class="btn btn-warning w-100">Send Message</button>
      </form>
      <div class="sticky-note mt-3" style="font-size: 13px; padding: 10px;">
        <strong>📌 Reminder:</strong><br>
        Don't forget to update livestock health records every Friday. Stay consistent!
      </div>
    </div>
  </div>
</div>

  <div class="row align-items-stretch">
    <!-- LEFT COLUMN - FORM -->
    <div class="col-lg-6 d-flex">
      <div class="card-box flex-fill w-100">
        <h4><?= $edit_record ? "Edit" : "Add" ?> Product Transaction</h4>
        <form method="POST">
          <input type="hidden" name="edit_id" value="<?= $edit_record['id'] ?? '' ?>">
          <input type="number" name="product_id" class="form-control mb-2" required placeholder="Product ID " value="<?= $edit_record['product_id'] ?? '' ?>">
          <input type="text" name="product_name" class="form-control mb-2" required placeholder="Name" value="<?= $edit_record['product_name'] ?? '' ?>">
          <input type="text" name="person_name" class="form-control mb-2" required placeholder="Costomer Name" value="<?= $edit_record['person_name'] ?? '' ?>">
          <input type="number" step="0.01" name="quantity" class="form-control mb-2" required placeholder="Quantity" value="<?= $edit_record['quantity'] ?? '' ?>">
          <input type="number" step="0.01" name="price" class="form-control mb-2" required placeholder="Price" value="<?= $edit_record['price'] ?? '' ?>">
          <textarea name="review" class="form-control mb-2" placeholder="Address"><?= $edit_record["address"] ?? '' ?></textarea>
          <button class="btn btn-primary w-100"><?= $edit_record ? "Update" : "Add" ?> Product</button>
        </form>
      </div>
    </div>

    <!-- RIGHT COLUMN - RECENT SALES -->
    <div class="col-lg-6 d-flex">
      <div class="card-box recent-sales flex-fill w-100">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <h5 class="mb-0">Recent Sales</h5>
          <a href="#">Show All</a>
        </div>
        <div class="table-responsive">
          <table class="table text-start align-middle table-bordered table-hover mb-0">
            <thead>
              <tr class="text-white">
                <th><input class="form-check-input" type="checkbox"></th>
                <th>DATE</th>
                <th>PRODUCT</th>
                <th>CUSTOMER</th>
                <th>AMOUNT</th>
                <th>STATUS</th>
                <th>EDIT</th>
                <th>DELETE</th>
              </tr>
            </thead>
            <tbody>
              <!-- Static example entries -->
              <tr>
                <td><input class="form-check-input" type="checkbox"></td>
                <td>17 April 2025</td>
                <td>Chicken (5KG)</td>
                <td>Ananna</td>
                <td>1050 TK</td>
                <td>Paid</td>
                <td><a class="btn btn-sm btn-success" href="#">EDIT</a></td>
                <td><a class="btn btn-sm btn-primary" href="#">DELETE</a></td>
              </tr>
              <tr>
                <td><input class="form-check-input" type="checkbox"></td>
                <td>17 April 2025</td>
                <td>Mutton (3KG)</td>
                <td>Afsana</td>
                <td>3060 TK</td>
                <td>Paid</td>
                <td><a class="btn btn-sm btn-success" href="#">EDIT</a></td>
                <td><a class="btn btn-sm btn-primary" href="#">DELETE</a></td>
              </tr>
              <!-- Add more static or dynamic entries here -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Search Bar -->
<div class="row mt-4 mb-2">
  <div class="col-12">
    <form method="GET" class="d-flex" role="search">
      <input 
        type="text" 
        name="search" 
        class="form-control me-2" 
        placeholder="Search by product, customer, or address" 
        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
      >
      <button type="submit" class="btn btn-primary">Search</button>
    </form>
  </div>
</div>

  <!-- FULL TRANSACTION TABLE BELOW BOTH COLUMNS -->
  <div class="row mt-4">
    <div class="col-12">
      <div class="card-box">
        <h4>Buyer and seller directories.</h4>
        <table class="table table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th>ID</th>
              <th>Product</th>
              <th>Customer</th>
              <th>Quantity</th>
              <th>Price</th>
              <th>Address</th>
              <th>Date</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= $row['product_id'] ?></td>
                <td><?= htmlspecialchars($row['product_name']) ?></td>
                <td><?= htmlspecialchars($row['person_name']) ?></td>
                <td><?= $row['quantity'] ?></td>
                <td><?= $row['price'] ?></td>
                <td><?= htmlspecialchars($row['review']) ?></td>
                <td><?= $row['created_at'] ?></td>
                <td>
                  <a href="?edit_id=<?= $row['id'] ?>" class="btn btn-sm btn-success">Edit</a>
                  <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete?')">Delete</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

</body>
</html>
<script>
const pie = new Chart(document.getElementById('myPieChart'), {
  type: 'pie',
  data: {
    labels: ['Pending', 'Completed', 'Cancelled'],
    datasets: [{ data: [4, 3, 1], backgroundColor: ['#17a2b8', '#138494', '#0f6674'] }]
  }
});

const bar = new Chart(document.getElementById('myDataChart'), {
  type: 'bar',
  data: {
    labels: ['Beef', 'Goat', 'Chicken'],
    datasets: [{
      label: 'Price ($)',
      data: [120, 80, 60],
      backgroundColor: '#17a2b8'
    }]
  }
});
</script>
<footer class="text-center py-3 mt-5" style="background-color: #343a40; color: #fff;">
  <p class="mb-0">Copyright &copy; 2025 Rabeya. All Rights Reserved.</p>
</footer>
</body>
</html>

