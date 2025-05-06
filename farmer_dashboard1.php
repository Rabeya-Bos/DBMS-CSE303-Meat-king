<?php
session_start();
$_SESSION['farmer_id'] = 1; // For testing only
$farmer_id = $_SESSION['farmer_id'];

$mysqli = new mysqli("localhost", "root", "", "mking");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    $yield = $_POST["yield"];
    $livestock = $_POST["livestock"];
    $cost = $_POST["cost"];
    $area = $_POST["area"];
    $date = $_POST["production_date"];
    $mysqli->query("INSERT INTO production_record_t (yield_kg, livestock_no, cost, area_acre, farmer_id, production_date) VALUES ('$yield', '$livestock', '$cost', '$area', '$farmer_id', '$date')");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $yield = $_POST['yield'];
    $livestock = $_POST['livestock'];
    $cost = $_POST['cost'];
    $area = $_POST['area'];
    $date = $_POST['production_date'];
    $mysqli->query("UPDATE production_record_t SET yield_kg = '$yield', livestock_no = '$livestock', cost = '$cost', area_acre = '$area', production_date = '$date' WHERE id = $id AND farmer_id = $farmer_id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $mysqli->query("DELETE FROM production_record_t WHERE id=$id AND farmer_id = $farmer_id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Farmer Dashboard - Rabeya</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6f9;
      margin: 0;
      padding: 0;
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
    body.toggled .sidebar {
      margin-left: -250px;
    }
    body.toggled .topbar,
    body.toggled .content {
      margin-left: 0;
    }
    .sidebar-toggle {
      font-size: 20px;
      color: #343a40;
      cursor: pointer;
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
  <a href="consumer.php"><i class="fas fa-users me-2"></i> Consumer Demand Data</a>
  <a href="real time supply.html"><i class="fas fa-shipping-fast me-2"></i> Real-time Supply</a>
  <a href="marketTrend.html"><i class="fas fa-chart-line me-2"></i> Market Trends</a>
  <a href="nutritionist.php"><i class="fas fa-apple-alt me-2"></i> Nutritionist</a>
  <a href="admin.php"><i class="fas fa-handshake me-2"></i>Buyer/seller</a>
  <a href="loging.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>


<!-- Topbar -->
<div class="topbar" style="background-color: black; color: white;">
  <span class="sidebar-toggle" id="sidebarToggle" style="color: white;"><i class="fa fa-bars"></i></span>
  <h5 class="mb-0" style="color: white;">Livestock Farmer Dashboard</h5>
  <div class="d-flex align-items-center">
    <img src="img/user2.jpg" alt="User" class="rounded-circle profile-pic me-2" style="width: 40px; height: 40px;">
    Rabeya
  </div>
</div>

<!-- Main Content -->
<div class="content" id="mainContent">
  <h2>Welcome to the Livestock Farmer Dashboard</h2>

  <!-- Cards -->
  <div class="row mb-4">
    <div class="col-md-3">
      <a href="livestock.php" class="card-box text-decoration-none">
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
        <h6>Real Time Supply</h6>
        <h3><i class="fa fa-map-marker-alt"></i></h3>
      </a>
    </div>
    <div class="col-md-3">
      <a href="order.html" class="card-box text-decoration-none">
        <h6>Orders</h6>
        <h3><i class="fa fa-shopping-cart"></i></h3>
      </a>
    </div>
  </div>

  <!-- Chart -->
  <div class="d-flex justify-content-center mb-4">
    <div style="width: 80%; max-width: 700px;">
      <canvas id="yieldChart"></canvas>
    </div>
  </div>

   <!-- Add Form -->
  <form method="POST" class="row g-3 mb-3">
    <div class="col-md-2"><input type="number" name="yield" class="form-control" placeholder="Yield (kg)" required></div>
    <div class="col-md-2"><input type="number" name="livestock" class="form-control" placeholder="Livestock No" required></div>
    <div class="col-md-2"><input type="number" name="cost" class="form-control" placeholder="Cost (৳)" required></div>
    <div class="col-md-3"><input type="text" name="processing data" class="form-control" placeholder="processing data" required></div>
    <div class="col-md-2"><input type="date" name="production_date" class="form-control" required></div>
    <div class="col-md-1 d-grid"><button type="submit" name="add" class="btn btn-primary">Add</button></div>
  </form> 

  <!-- Table -->
  <h2>Historical Production Data</h2>
  <table class="table table-bordered">
    <thead class="table-light">
      <tr><th>Yield</th><th>Livestock</th><th>Cost</th><th>production data</th><th>Date</th><th>Actions</th></tr>
    </thead>
    <tbody>
    <?php
    $records = $mysqli->query("SELECT * FROM production_record_t WHERE farmer_id = $farmer_id");
    while ($row = $records->fetch_assoc()) {
        echo "<tr>
            <td>{$row['yield_kg']}</td>
            <td>{$row['livestock_no']}</td>
            <td>{$row['cost']}</td>
            <td>{$row['area_acre']}</td>
            <td>{$row['production_date']}</td>
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
              <div class='modal-header'><h5>Edit Record</h5><button type='button' class='btn-close' data-bs-dismiss='modal'></button></div>
              <div class='modal-body'>
                <input type='hidden' name='id' value='{$row['id']}'>
                <input class='form-control mb-2' name='yield' value='{$row['yield_kg']}' required>
                <input class='form-control mb-2' name='livestock' value='{$row['livestock_no']}' required>
                <input class='form-control mb-2' name='cost' value='{$row['cost']}' required>
                <input class='form-control mb-2' name='area' value='{$row['area_acre']}' required>
                <input class='form-control mb-2' type='date' name='production_date' value='{$row['production_date']}' required>
              </div>
              <div class='modal-footer'><button class='btn btn-primary' name='edit'>Save</button></div>
            </form>
          </div>
        </div>
      </div>";
  }
  ?>
</div>

<!-- Chart Script -->
<script>
const ctx = document.getElementById('yieldChart').getContext('2d');
const yieldChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: [
      <?php
      $labelRes = $mysqli->query("SELECT id FROM production_record_t WHERE farmer_id = $farmer_id");
      while ($r = $labelRes->fetch_assoc()) echo "'#{$r['id']}',";
      ?>
    ],
    datasets: [{
      label: 'Yield (kg)',
      data: [
        <?php
        $dataRes = $mysqli->query("SELECT yield_kg FROM production_record_t WHERE farmer_id = $farmer_id");
        while ($r = $dataRes->fetch_assoc()) echo "{$r['yield_kg']},";
        ?>
      ],
      backgroundColor: '#4bc0c0',
    }]
  },
  options: { responsive: true, scales: { y: { beginAtZero: true } } }
});
</script>

<script>
document.getElementById('sidebarToggle').addEventListener('click', function () {
  document.body.classList.toggle('toggled');
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<footer class="text-center py-3 mt-5" style="background-color: #343a40; color: #fff;">
  <p class="mb-0">Copyright &copy; 2025 Rabeya. All Rights Reserved.</p>
</footer>
</body>
</html>
