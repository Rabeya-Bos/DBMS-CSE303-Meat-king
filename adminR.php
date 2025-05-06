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

<div class="sidebar">
  <a href="#"><h3 style="color:red; font-weight:bold;">MEAT KING</h3></a>
  <a class="active" href="#"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
  <a href="farmer_dashboard.php"><i class="fas fa-paw me-2"></i> Livestock_Farmer</a>
  <a href="Nutritionist.php"><i class="fas fa-apple-alt me-2"></i> Nutritionist</a>
  <a href="retailer.php"><i class="fas fa-store-alt me-2"></i> Retailer</a>
  <a href="wholesaler.php"><i class="fas fa-cogs me-2"></i> Wholesaler</a>
  <a href="coldstorage.php"><i class="fas fa-warehouse me-2"></i> Coldstorage_Manager</a>
  <a href="consumer.php"><i class="fas fa-users me-2"></i> Consumer</a>
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

  <div class="row">
    <div class="col-md-4"><div class="card-box"><h5>Total Livestock</h5><h2>54</h2></div></div>
    <div class="col-md-4"><div class="card-box"><h5>Products in Stock</h5><h2>112</h2></div></div>
    <div class="col-md-4"><div class="card-box"><h5>Orders Today</h5><h2>8</h2></div></div>
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
  <div class="col-md-6">
    <div class="card-box" style="background: linear-gradient(135deg, rgba(54, 162, 235, 0.7), rgba(255, 206, 86, 0.7)); color: #000;">
      <h4>Weather</h4>
      <p>Today's weather: Sunny, 30°C</p>
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
  <div class="row mb-5">
    <!-- Product Form -->
    <div class="col-md-6">
      <div class="card-box">
        <h4>Product Transaction Entry</h4>
        <?php if ($message): ?><div class="alert alert-info"><?= htmlspecialchars($message) ?></div><?php endif; ?>
        <form method="POST">
          <input type="hidden" name="edit_id" value="<?= $edit_record['id'] ?? '' ?>">
          <input type="number" name="product_id" class="form-control mb-2" required placeholder="Product ID" value="<?= $edit_record['product_id'] ?? '' ?>">
          <input type="text" name="product_name" class="form-control mb-2" required placeholder="Product Name" value="<?= $edit_record['product_name'] ?? '' ?>">
          <input type="text" name="person_name" class="form-control mb-2" required placeholder="Person Name" value="<?= $edit_record['person_name'] ?? '' ?>">
          <input type="number" step="0.01" name="quantity" class="form-control mb-2" required placeholder="Quantity" value="<?= $edit_record['quantity'] ?? '' ?>">
          <input type="number" step="0.01" name="price" class="form-control mb-2" required placeholder="Price" value="<?= $edit_record['price'] ?? '' ?>">
          <textarea name="review" class="form-control mb-2" placeholder="Review"><?= $edit_record['review'] ?? '' ?></textarea>
          <button class="btn btn-primary"><?= $edit_record ? "Update" : "Add" ?> Product</button>
        </form>
      </div>
    </div>

    <!-- Records Table -->
    <div class="card-box">
      <h5>Transaction Records</h5>
      <table class="table table-bordered table-striped">
        <thead>
          <tr><th>ID</th><th>Name</th><th>Person</th><th>Qty</th><th>Price</th><th>Review</th><th>Actions</th></tr>
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
              <td>
                <a href="?edit_id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')">Delete</a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

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
<!-- Footer -->
<footer class="text-center py-3 mt-5" style="background-color: #343a40; color: #fff;">
  <p class="mb-0">Copyright &copy; 2025 Rabeya. All Rights Reserved.</p>
</footer>

</body>
</html>
