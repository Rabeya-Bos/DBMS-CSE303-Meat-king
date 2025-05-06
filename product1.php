<?php
// Database Connection
$host = "localhost";
$username = "root";
$password = "";
$database = "mking";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Handle Form Actions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == "add") {
        $productId = $_POST['productId'];
        $meatType = $_POST['meatType'];
        $cut = $_POST['cut'];
        $origin = $_POST['origin'];
        $seasonality = $_POST['seasonality'];
        $price = $_POST['price'];

        $query = "INSERT INTO Meat_product_t (product_id, meat_type, cut, origin, seasonality, price) 
                  VALUES ('$productId', '$meatType', '$cut', '$origin', '$seasonality', '$price')";
        if ($conn->query($query) === TRUE) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $message = "Error: " . $conn->error;
        }
    } elseif ($_POST['action'] == "edit") {
        $id = $_POST['id'];
        $productId = $_POST['productId'];
        $meatType = $_POST['meatType'];
        $cut = $_POST['cut'];
        $origin = $_POST['origin'];
        $seasonality = $_POST['seasonality'];
        $price = $_POST['price'];

        $query = "UPDATE Meat_product_t SET 
                  product_id = '$productId',
                  meat_type = '$meatType',
                  cut = '$cut',
                  origin = '$origin',
                  seasonality = '$seasonality',
                  price = '$price' 
                  WHERE id = $id";
        if ($conn->query($query) === TRUE) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $message = "Error: " . $conn->error;
        }
    } elseif ($_POST['action'] == "delete") {
        $id = $_POST['id'];
        $query = "DELETE FROM Meat_product_t WHERE id = $id";
        if ($conn->query($query) === TRUE) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

// Fetch Records
$query = "SELECT * FROM Meat_product_t";
$result = $conn->query($query);

$productForEdit = null;
if (isset($_GET['edit_id'])) {
    $id = $_GET['edit_id'];
    $query = "SELECT * FROM Meat_product_t WHERE id = $id";
    $productForEdit = $conn->query($query)->fetch_assoc();
}

$countQuery = "SELECT 
    SUM(CASE WHEN cut = 'raw' THEN 1 ELSE 0 END) AS raw_count,
    SUM(CASE WHEN cut = 'packet' THEN 1 ELSE 0 END) AS packet_count,
    COUNT(*) AS total_count
FROM Meat_product_t";
$countResult = $conn->query($countQuery);
$counts = $countResult->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Product Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
  <style>
    body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
    .sidebar { width: 250px; height: 100vh; position: fixed; background-color: #212529; color: white; padding-top: 30px; }
    .sidebar h4 { text-align: center; color: red; margin-bottom: 30px; }
    .sidebar a { display: block; padding: 12px 30px; color: #d1d1d1; text-decoration: none; }
    .sidebar a:hover, .sidebar a.active { background-color: #343a40; color: white; }
    .content { margin-left: 250px; padding: 20px 40px; }
    .topbar { display: flex; justify-content: space-between; align-items: center; background: #fff; padding: 15px 20px; border-radius: 10px; margin-bottom: 30px; }
    .dashboard-cards .card { border: none; border-radius: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); text-align: center; }
    .table-wrapper { background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 0 8px rgba(0, 0, 0, 0.05); }
  </style>
</head>
<body>

<div class="sidebar">
  <a href="#"><h3 style="color:red; font-weight:bold;">MEAT KING</h3></a>
  <a class="active" href="#"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
  <a href="product."><i class="fas fa-industry me-2"></i> Production Information</a>
  <a href="farmer_dashboard."><i class="fas fa-history me-2"></i> Historical Production Data</a>
  <a href="consumer."><i class="fas fa-users me-2"></i> Consumer Demand Data</a>
  <a href="real time supply."><i class="fas fa-shipping-fast me-2"></i> Real-time Supply</a>
  <a href="marketTrend."><i class="fas fa-chart-line me-2"></i> Market Trends</a>
  <a href="nutritionist."><i class="fas fa-apple-alt me-2"></i> Nutritionist</a>
  <a href="admin."><i class="fas fa-handshake me-2"></i>Buyer/seller</a>
  <a href="loging.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>




<div class="content">
  <div class="topbar">
    <h5>Product Information</h5>
   <span><strong>
  <img src="img/user2.jpg" alt="User" class="rounded-circle me-1" style="width: 50px; height: 50px; object-fit: cover;">
  Rabeya
</strong></span>

  </div>

  <div class="row dashboard-cards mb-4">
    <div class="col-md-4">
      <div class="card p-3">
        <h6>Raw Products</h6>
        <p class="fs-5 text-primary"><?php echo $counts['raw_count']; ?></p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3">
        <h6>Packet Products</h6>
        <p class="fs-5 text-primary"><?php echo $counts['packet_count']; ?></p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3">
        <h6>Total Products</h6>
        <p class="fs-5 text-primary"><?php echo $counts['total_count']; ?></p>
      </div>
    </div>
  </div>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Meat Products</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <h2 class="mb-4 text-center">Manage Meat Products</h2>

  <!-- Display Message -->
  <?php if (!empty($message)): ?>
    <div class="alert alert-danger"><?php echo $message; ?></div>
  <?php endif; ?>

  <!-- Form -->
  <form class="row g-3 mb-4" method="POST">
    <input type="hidden" name="id" value="<?php echo $productForEdit['id'] ?? ''; ?>">
    <input type="hidden" name="action" value="<?php echo $productForEdit ? 'edit' : 'add'; ?>">

    <div class="col-md-4">
      <input type="text" class="form-control" name="productId" value="<?php echo $productForEdit['product_id'] ?? ''; ?>" placeholder="Product ID" required>
    </div>
    <div class="col-md-4">
      <input type="text" class="form-control" name="meatType" value="<?php echo $productForEdit['meat_type'] ?? ''; ?>" placeholder="Meat Type" required>
    </div>
    <div class="col-md-4">
      <input type="text" class="form-control" name="cut" value="<?php echo $productForEdit['cut'] ?? ''; ?>" placeholder="Cut (raw/packet)" required>
    </div>
    <div class="col-md-4">
      <input type="text" class="form-control" name="origin" value="<?php echo $productForEdit['origin'] ?? ''; ?>" placeholder="Origin" required>
    </div>
    <div class="col-md-4">
      <input type="text" class="form-control" name="seasonality" value="<?php echo $productForEdit['seasonality'] ?? ''; ?>" placeholder="Seasonality" required>
    </div>
    <div class="col-md-4">
      <input type="number" step="0.01" class="form-control" name="price" value="<?php echo $productForEdit['price'] ?? ''; ?>" placeholder="Price" required>
    </div>

    <div class="col-12 text-end">
      <button type="submit" class="btn btn-primary"><?php echo $productForEdit ? 'Update Product' : 'Add Product'; ?></button>
    </div>
  </form>

  <!-- Search Input -->
  <div class="mb-3">
    <input type="text" id="searchInput" class="form-control" placeholder="Search products...">
  </div>

  <!-- Products Table -->
  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th>Product ID</th>
          <th>Meat Type</th>
          <th>Cut</th>
          <th>Origin</th>
          <th>Seasonality</th>
          <th>Price</th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody id="productTable">
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['product_id']; ?></td>
          <td><?php echo $row['meat_type']; ?></td>
          <td><?php echo $row['cut']; ?></td>
          <td><?php echo $row['origin']; ?></td>
          <td><?php echo $row['seasonality']; ?></td>
          <td><?php echo $row['price']; ?></td>
          <td class="text-center">
            <a href="?edit_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
            <form method="POST" style="display:inline;">
              <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
              <button type="submit" name="action" value="delete" class="btn btn-sm btn-danger">Delete</button>
            </form>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <!-- Summary -->
  <div class="alert alert-info mt-4">
    <strong>Total Products:</strong> <?php echo $counts['total_count']; ?> |
    <strong>Raw Cuts:</strong> <?php echo $counts['raw_count']; ?> |
    <strong>Packet Cuts:</strong> <?php echo $counts['packet_count']; ?>
  </div>

</div>

<script>
// Live search functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
  var searchValue = this.value.toLowerCase();
  var rows = document.querySelectorAll("#productTable tr");
  rows.forEach(function(row) {
    var rowText = row.innerText.toLowerCase();
    row.style.display = rowText.includes(searchValue) ? '' : 'none';
  });
});
</script>
  <footer class="text-center py-3 mt-5" style="background-color: #343a40; color: #fff;">
  <p class="mb-1">Copyright &copy; 2025 Rabeya. All Rights Reserved.</p>
</footer>
</body>
</html>
