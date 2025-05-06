<?php
session_start();
$_SESSION['coldstorageid'] = 1; // For testing purposes
$coldstorageid = $_SESSION['coldstorageid'];

// DB connection
$mysqli = new mysqli("localhost", "root", "", "mking");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $capacity = $_POST['capacity'];
    $retailer = $_POST['retailerstorage_id'];
    $wholesaler = $_POST['wholesalerstorage_id'];
    $quantity = $_POST['quantity'];
    $take = $_POST['take'];
    $date = $_POST['date'];

    $mysqli->query("INSERT INTO coldstorage (capacity, retailerstorage_id, wholesalerstorage_id, quantity, take, date) 
                    VALUES ('$capacity', '$retailer', '$wholesaler', '$quantity', '$take', '$date')");
    header("Location: coldstoragemanager.php");
    exit();
}

// Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $mysqli->query("DELETE FROM coldstorage WHERE coldstorageid=$id");
    header("Location: coldstoragemanager.php");
    exit();
}

// Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $id = $_POST['coldstorageid'];
    $capacity = $_POST['capacity'];
    $retailer = $_POST['retailerstorage_id'];
    $wholesaler = $_POST['wholesalerstorage_id'];
    $quantity = $_POST['quantity'];
    $take = $_POST['take'];
    $date = $_POST['date'];

    $mysqli->query("UPDATE coldstorage SET capacity='$capacity', retailerstorage_id='$retailer', wholesalerstorage_id='$wholesaler', 
                    quantity='$quantity', take='$take', date='$date' WHERE coldstorageid='$id'");
    header("Location: coldstoragemanager.php");
    exit();
}
?>

<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>ColdStorage Manager - Rabeya</title>
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
    /* Sidebar Toggle */
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
  <a href="index.html"><h3 style="color:red; font-weight:bold;">MEAT KING</h3></a>
  <a class="active" href="#"><i class="fa fa-home me-2"></i> Dashboard</a>
  <a href="order.html"><i class="fa fa-shopping-cart me-2"></i> Orders</a>
  <a href="loging.php">"><i class="fa fa-sign-out-alt me-2"></i> Logout</a>
</div>

<!-- Topbar -->
<div class="topbar bg-dark text-white">
  <button class="btn btn-outline-light me-3" id="sidebarToggle"><i class="fas fa-bars"></i></button>
  <h5 class="mb-0">ColdStorage Manager panel</h5>
  <div>
    <img src="img/user.jpg" alt="User",class="rounded-circle me-2" style="width:40px;"> Ashikur
  </div>
</div>

<!-- Content -->
<div class="content" id="mainContent">
  <!-- Cards -->
  <div class="row mb-4">
    <div class="col-md-3">
      <a href="livestock.html" class="card-box text-decoration-none">
        <h6>Retailer</h6>
        <h3><i class="fa fa-paw"></i></h3>
      </a>
    </div>
    <div class="col-md-3">
      <a href="product.html" class="card-box text-decoration-none">
        <h6>Wholesaler</h6>
        <h3><i class="fa fa-box"></i></h3>
      </a>
    </div>
    <div class="col-md-3">
      <a href="location.html" class="card-box text-decoration-none">
        <h6>Capacity</h6>
        <h3><i class="fa fa-map-marker-alt"></i></h3>
      </a>
    </div>
    <div class="col-md-3">
      <a href="order.html" class="card-box text-decoration-none">
        <h6>Stored\Quantity</h6>
        <h3><i class="fa fa-shopping-cart"></i></h3>
      </a>
    </div>
  </div>
<body class="bg-light">
<div class="container py-5">
  <h2 class="mb-4">Cold Storage Management</h2>

  <!-- Add Form -->
  <form method="POST" class="row g-3 mb-4 bg-white p-4 rounded shadow-sm">
    <div class="col-md-4"><input type="number" name="capacity" class="form-control" placeholder="Capacity" required></div>
    <div class="col-md-4"><input type="number" name="retailerstorage_id" class="form-control" placeholder="Retailer Storage ID" required></div>
    <div class="col-md-4"><input type="number" name="wholesalerstorage_id" class="form-control" placeholder="Wholesaler Storage ID" required></div>
    <div class="col-md-4"><input type="number" name="quantity" class="form-control" placeholder="Quantity" required></div>
    <div class="col-md-4"><input type="number" name="take" class="form-control" placeholder="Take" required></div>
    <div class="col-md-4"><input type="date" name="date" class="form-control" required></div>
    <div class="col-12 text-end"><button type="submit" name="add" class="btn btn-primary">Add Entry</button></div>
  </form>

  <!-- Table -->
  <table class="table table-bordered table-striped">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Capacity</th>
        <th>Retailer Storage ID</th>
        <th>Wholesaler Storage ID</th>
        <th>Quantity</th>
        <th>Take</th>
        <th>Date</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $results = $mysqli->query("SELECT * FROM coldstorage");
    while ($row = $results->fetch_assoc()) {
        echo "<tr>
            <td>{$row['coldstorageid']}</td>
            <td>{$row['capacity']}</td>
            <td>{$row['retailerstorage_id']}</td>
            <td>{$row['wholesalerstorage_id']}</td>
            <td>{$row['quantity']}</td>
            <td>{$row['take']}</td>
            <td>{$row['date']}</td>
            <td>
              <a href='?delete={$row['coldstorageid']}' class='btn btn-danger btn-sm'>Delete</a>
              <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#edit{$row['coldstorageid']}'>Edit</button>
            </td>
          </tr>";
    }
    ?>
    </tbody>
  </table>

  <!-- Edit Modals -->
  <?php
  $results->data_seek(0);
  while ($row = $results->fetch_assoc()) {
    echo "<div class='modal fade' id='edit{$row['coldstorageid']}' tabindex='-1'>
            <div class='modal-dialog'>
              <form method='POST' class='modal-content'>
                <div class='modal-header'>
                  <h5 class='modal-title'>Edit Cold Storage Entry</h5>
                  <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                </div>
                <div class='modal-body'>
                  <input type='hidden' name='coldstorageid' value='{$row['coldstorageid']}'>
                  <input type='number' name='capacity' class='form-control mb-2' value='{$row['capacity']}' required>
                  <input type='number' name='retailerstorage_id' class='form-control mb-2' value='{$row['retailerstorage_id']}' required>
                  <input type='number' name='wholesalerstorage_id' class='form-control mb-2' value='{$row['wholesalerstorage_id']}' required>
                  <input type='number' name='quantity' class='form-control mb-2' value='{$row['quantity']}' required>
                  <input type='number' name='take' class='form-control mb-2' value='{$row['take']}' required>
                  <input type='date' name='date' class='form-control mb-2' value='{$row['date']}' required>
                </div>
                <div class='modal-footer'>
                  <button type='submit' name='update' class='btn btn-success'>Update</button>
                  <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancel</button>
                </div>
              </form>
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
