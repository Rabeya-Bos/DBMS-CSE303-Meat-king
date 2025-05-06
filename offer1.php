<?php
// offer.php
$servername = "localhost";
$username = "root";
$password = "";
$database = "mking";

// Connect to database
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch existing offers
$offers = [];
$result = $conn->query("SELECT * FROM historical_data");
if ($result && $result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $offers[] = $row;
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Retailer/Wholesaler - Offers | MEAT KING</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    body { font-family: 'Segoe UI'; background: #f4f6f9; margin: 0; min-height: 100vh; display: flex; flex-direction: column; }
    .sidebar { width: 250px; height: 100vh; position: fixed; background: #2c3e50; color: #fff; padding-top: 20px; }
    .sidebar a { color: #fff; padding: 15px 25px; display: block; text-decoration: none; }
    .sidebar a:hover, .sidebar .active { background: #34495e; }
    .topbar { margin-left: 250px; height: 60px; background: #34495e; color: #fff; display: flex; justify-content: space-between; align-items: center; padding: 0 30px; }
    .content { margin-left: 250px; padding: 30px; flex: 1; }
    .card-box { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 30px; }
    .profile-pic { width: 40px; height: 40px; object-fit: cover; border-radius: 50%; }
    .footer { background: #2c3e50; color: #fff; text-align: center; padding: 15px 0; margin-top: auto; }
    table th, table td { vertical-align: middle; }
  </style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
  <a href="#"><h3 style="color:red; font-weight:bold;">MEAT KING</h3></a>
  <a class="active" href="#"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
  <a href="farmer_dashboard.php"><i class="fas fa-paw me-2"></i> Livestock Farmer</a>
  <a href="Nutritionist.php"><i class="fas fa-apple-alt me-2"></i> Nutritionist</a>
  <a href="retailer.php"><i class="fas fa-store me-2"></i> Retailer</a>
  <a href="wholesaler.php"><i class="fas fa-industry me-2"></i> Wholesaler</a>
  <a href="coldstorage.php"><i class="fas fa-warehouse me-2"></i> Coldstorage Manager</a>
  <a href="consumer.php"><i class="fas fa-users me-2"></i> Consumer</a>
  <a href="login.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>

<!-- Topbar -->
<div class="topbar">
  <h5 class="mb-0">Retailer / Wholesaler - Offers</h5>
  <div class="d-flex align-items-center">
    <img src="img/user2.jpg" alt="User" class="profile-pic me-2"> Rabeya
  </div>
</div>

<!-- Main Content -->
<div class="content">
  <h2 class="mb-4">Manage Product Offers</h2>

  <!-- Search Input -->
  <div class="mb-4">
    <input type="text" id="searchInput" class="form-control" placeholder="Search Products..." onkeyup="filterTable()">
  </div>

  <!-- Add Offer Form -->
  <form action="add_offer.php" method="POST" class="card-box mb-5">
    <div class="row g-3">
      <div class="col-md-4">
        <input type="text" name="product_name" class="form-control" placeholder="Product Name" required>
      </div>
      <div class="col-md-3">
        <input type="number" step="0.01" name="current_price" class="form-control" placeholder="Current Price" required>
      </div>
      <div class="col-md-3">
        <input type="number" step="0.01" name="offer_price" class="form-control" placeholder="Offer Price" required>
      </div>
      <div class="col-md-2 d-grid">
        <button type="submit" class="btn btn-primary">Add Offer</button>
      </div>
    </div>
  </form>

  <!-- Offers Table -->
  <div class="card-box">
    <table class="table table-hover table-bordered" id="offersTable">
      <thead class="table-dark">
        <tr>
          <th>Product Name</th>
          <th>Current Price ($)</th>
          <th>Offer Price ($)</th>
        </tr>
      </thead>
      <tbody id="offersTableBody">
        <?php foreach ($offers as $offer): ?>
          <tr>
            <td><?= htmlspecialchars($offer['product_name']) ?></td>
            <td><?= number_format($offer['current_price'], 2) ?></td>
            <td><?= number_format($offer['offer_price'], 2) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>

<!-- Footer -->
<footer class="footer">
  <p class="mb-0">Copyright © 2025 MEAT KING. All Rights Reserved.</p>
</footer>

<!-- JavaScript Section -->
<script>
  function filterTable() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    document.querySelectorAll('#offersTableBody tr').forEach(row => {
      const product = row.cells[0].innerText.toLowerCase();
      row.style.display = product.includes(search) ? '' : 'none';
    });
  }
</script>

</body>
</html>
