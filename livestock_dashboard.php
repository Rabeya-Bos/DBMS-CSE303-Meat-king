<?php
session_start();  // Only call session_start once at the beginning of the script

$farmer_id = $_SESSION['farmer_id'] ?? 1;  // Use a fallback for testing if farmer_id is not set

$mysqli = new mysqli("localhost", "root", "", "mking");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    $yield = $_POST["yield"];
    $livestock = $_POST["livestock"];
    $cost = $_POST["cost"];
    $area = $_POST["area"];
    $mysqli->query("INSERT INTO production_record_t (yield_kg, livestock_no, cost, area_acre, farmer_id) VALUES ('$yield', '$livestock', '$cost', '$area', '$farmer_id')");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $yield = $_POST['yield'];
    $livestock = $_POST['livestock'];
    $cost = $_POST['cost'];
    $area = $_POST['area'];
    $mysqli->query("UPDATE production_record_t SET yield_kg = '$yield', livestock_no = '$livestock', cost = '$cost', area_acre = '$area' WHERE id = $id AND farmer_id = $farmer_id");
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
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Farmer Dashboard - Rabeya</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f4f6f9;
    }
    .container {
      margin-top: 30px;
    }
    .form-container {
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }
    .form-group {
      margin-bottom: 1.5rem;
    }
    .form-group label {
      font-weight: bold;
    }
    .navbar, .sidebar {
      background-color: #343a40;
      color: white;
    }
  </style>
</head>
<body>

  <!-- Navbar (Topbar) -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">Farmer Dashboard</a>
  </nav>

  <!-- Sidebar -->
  <div class="d-flex">
    <div class="sidebar" style="width: 250px; height: 100vh; padding: 20px;">
      <ul class="list-unstyled">
        <li><a href="#" style="color: white;">Home</a></li>
        <li><a href="#" style="color: white;">Profile</a></li>
        <li><a href="#" style="color: white;">Logout</a></li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="container">
      <!-- Add/Edit Form -->
      <div class="form-container">
        <h3 class="text-center mb-4">Enter Production Record</h3>
        <form method="POST">
          <div class="form-group">
            <label for="yield">Yield (kg)</label>
            <input type="number" class="form-control" id="yield" name="yield" required>
          </div>
          <div class="form-group">
            <label for="livestock">Livestock</label>
            <input type="number" class="form-control" id="livestock" name="livestock" required>
          </div>
          <div class="form-group">
            <label for="cost">Cost</label>
            <input type="number" class="form-control" id="cost" name="cost" required>
          </div>
          <div class="form-group">
            <label for="area">Area (acre)</label>
            <input type="number" class="form-control" id="area" name="area" required>
          </div>
          <button type="submit" name="add" class="btn btn-primary">Add Record</button>
        </form>
      </div>

      <!-- Data Table -->
      <h4 class="text-center my-4">Production Records</h4>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <th>Yield (kg)</th>
            <th>Livestock</th>
            <th>Cost</th>
            <th>Area (acre)</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $result = $mysqli->query("SELECT * FROM production_record_t WHERE farmer_id = $farmer_id");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['id'] . "</td>";
                echo "<td>" . $row['yield_kg'] . "</td>";
                echo "<td>" . $row['livestock_no'] . "</td>";
                echo "<td>" . $row['cost'] . "</td>";
                echo "<td>" . $row['area_acre'] . "</td>";
                echo "<td>";
                echo "<a href='?edit=" . $row['id'] . "' class='btn btn-warning btn-sm'>Edit</a> ";
                echo "<a href='?delete=" . $row['id'] . "' class='btn btn-danger btn-sm'>Delete</a>";
                echo "</td>";
                echo "</tr>";
            }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
