<?php
session_start();
$_SESSION['nutritionist_id'] = 1; // For testing only
$nutritionist_id = $_SESSION['nutritionist_id'];

// Database connection
$mysqli = new mysqli("localhost", "root", "", "mking");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Handle POST request for adding a new recommendation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    // Get POST data
    $farmerid = $_POST["farmerid"];
    $itemid = $_POST["itemid"];  // Assuming this is an item_id
    $batchno = $_POST["batchno"];  // Assuming this is a batch number
    $date = $_POST["date"];
    $recommendation = $_POST["recommendation"];

    // Corrected INSERT query based on the table structure
    $mysqli->query("INSERT INTO recommendations (farmer_id, item_id, batch_no, recommendation, date) 
                    VALUES ('$farmerid', '$itemid', '$batchno', '$recommendation', '$date')");

    // Redirect back to the page after insertion
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle record deletion (if necessary)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $mysqli->query("DELETE FROM recommendations WHERE id=$id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Nutritionist Dashboard</title>
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
    body.toggled .sidebar {
      margin-left: -250px;
    }
    body.toggled .topbar,
    body.toggled .content {
      margin-left: 0;
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


<!-- Topbar -->
<div class="topbar bg-dark text-white">
  <button class="btn btn-outline-light me-3" id="sidebarToggle"><i class="fas fa-bars"></i></button>
  <h5 class="mb-0">Nutritionist Dashboard</h5>
  <div>
  </div>
</div>

<!-- Content -->
<div class="content" id="mainContent">
  <!-- Cards -->
  <div class="row mb-4">
    <div class="col-md-3">
      <a href="livestock.html" class="card-box text-decoration-none">
        <h6>Livestock</h6>
        <h3><i class="fa fa-paw"></i></h3>
      </a>
    </div>
    <div class="col-md-3">
      <a href="product.html" class="card-box text-decoration-none">
        <h6>Products</h6>
        <h3><i class="fa fa-box"></i></h3>
      </a>
    </div>
    <div class="col-md-3">
      <a href="location.html" class="card-box text-decoration-none">
        <h6>Locations</h6>
        <h3><i class="fa fa-map-marker-alt"></i></h3>
      </a>
    </div>
    <div class="col-md-3">
  <div class="card-box text-decoration-none">
    <h6>Protein Need Per Day</h6>
    <h2>0.8g / kg Body Weight</h2>
    <h3><i class="fa fa-drumstick-bite"></i></h3>
  </div>
</div>
<div class="row mb-4">
  <!-- NUTRITIONIST CARD -->
  <div class="col-md-6">
    <div class="card-box" style="background: linear-gradient(135deg, rgba(75, 192, 192, 0.7), rgba(153, 102, 255, 0.7)); color: #000;">
      <h4>Nutritionist</h4>
      <p>Today's Focus: Balanced Feed Plan for Goats</p>

      <!-- REMINDER BAR -->
      <div class="alert alert-success py-2 px-3 mt-3" style="font-size: 14px; border-left: 5px solid #28a745;">
        <strong>🥬 Tip:</strong> Ensure protein intake is adjusted with seasonal forage changes.
      </div>

      <!-- SIDE-BY-SIDE IMAGE UPLOADS -->
      <form class="mt-3" enctype="multipart/form-data">
        <div class="row">
          <div class="col-md-6 mb-3">
            <label for="nutritionImage1" class="form-label">Feed Chart</label>
            <input type="file" class="form-control" id="nutritionImage1" name="nutritionImage1" accept="image/*">
          </div>
          <div class="col-md-6 mb-3">
            <label for="nutritionImage2" class="form-label">Nutrient Report</label>
            <input type="file" class="form-control" id="nutritionImage2" name="nutritionImage2" accept="image/*">
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- IMAGE DISPLAY (RIGHT SIDE OF CARD) -->
<div class="col-md-3">
  <div class="card-box p-2 text-center">
    <h6>Sample Feed Image</h6>
    <img src="img/use5.jpg" alt="Feed Example" class="img-fluid rounded" style="width: 100%; height: auto;">
  </div>
</div>

<div class="col-md-3">
  <div class="card-box p-2 text-center">
    <h6>Nutrient Breakdown</h6>
    <img src="img/use4.jpg" alt="Nutrient Info" class="img-fluid rounded" style="width: 100%; height: auto;">
  </div>
</div>




  <!-- Add Recommendation Form -->
  <form method="POST" class="row g-3 mb-3">
    <div class="col-md-3"><input type="number" name="farmerid" class="form-control" placeholder="Farmer ID" required></div>
    <div class="col-md-3"><input type="number" name="itemid" class="form-control" placeholder="Item ID" required></div>
    <div class="col-md-3"><input type="text" name="batchno" class="form-control" placeholder="Batch Number" required></div>
    <div class="col-md-3"><input type="date" name="date" class="form-control" required></div>
    <div class="col-md-3"><textarea name="recommendation" class="form-control" placeholder="Recommendation" required></textarea></div>
    <div class="text-end"><button type="submit" name="add" class="btn btn-primary">Add Recommendation</button></div>
  </form>

  <!-- Search Bar -->
<div class="row mt-3 mb-3">
  <div class="col-12">
    <form method="GET" class="d-flex" role="search">
      <input 
        type="text" 
        name="search" 
        class="form-control me-2" 
        placeholder="" 
        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
      >
      <button type="submit" class="btn btn-primary">Search</button>
    </form>
  </div>
</div>

  <!-- Recommendation Table -->
  <table class="table table-bordered">
    <thead class="table-light">
      <tr><th>Farmer ID</th><th>Item ID</th><th>Batch Number</th><th>Date</th><th>Recommendation</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php
    $records = $mysqli->query("SELECT * FROM recommendations");
    while ($row = $records->fetch_assoc()) {
        echo "<tr>
            <td>{$row['farmer_id']}</td>
            <td>{$row['item_id']}</td>
            <td>{$row['batch_no']}</td>
            <td>{$row['date']}</td>
            <td>{$row['recommendation']}</td>
            <td>
              <a href='?delete={$row['id']}' class='btn btn-danger btn-sm'>Delete</a>
              <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editModal{$row['id']}'>Edit</button>
            </td>
          </tr>";
          
    }
    ?>
    </tbody>
  </table>

  <!-- Edit Modals -->
  <?php
  $records->data_seek(0);
  while ($row = $records->fetch_assoc()) {
      echo "<div class='modal fade' id='editModal{$row['id']}' tabindex='-1'>
        <div class='modal-dialog'>
          <div class='modal-content'>
            <form method='POST'>
              <div class='modal-header'>
                <h5 class='modal-title'>Edit Record</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
              </div>
              <div class='modal-body'>
                <input type='hidden' name='id' value='{$row['id']}'>
                <input type='number' name='farmerid' class='form-control mb-2' value='{$row['farmer_id']}' required>
                <input type='number' name='itemid' class='form-control mb-2' value='{$row['item_id']}' required>
                <input type='text' name='batchno' class='form-control mb-2' value='{$row['batch_no']}' required>
                <input type='date' name='date' class='form-control mb-2' value='{$row['date']}' required>
                <textarea name='recommendation' class='form-control mb-2' required>{$row['recommendation']}</textarea>
              </div>
              <div class='modal-footer'>
                <button type='submit' name='update' class='btn btn-success'>Update</button>
                <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Close</button>
              </div>
            </form>
          </div>
        </div>
      </div>";
  }
  ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Footer -->
<footer class="text-center py-3 mt-5" style="background-color: #343a40; color: #fff;">
  <p class="mb-0">Copyright &copy; 2025 Rabeya. All Rights Reserved.</p>
</footer>
</body>
</html>
