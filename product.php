<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "mking";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == "add") {
        $meatType = $_POST['meatType'];
        $cut = $_POST['cut'];
        $origin = $_POST['origin'];
        $seasonality = $_POST['seasonality'];
        $price = $_POST['price'];

        $query = "INSERT INTO meat_product_t (meat_type, cut, origin, seasonality, price) 
                  VALUES ('$meatType', '$cut', '$origin', '$seasonality', '$price')";
        if ($conn->query($query) === TRUE) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $message = "Error: " . $conn->error;
        }
    } elseif ($_POST['action'] == "edit") {
        $id = $_POST['id'];
        $meatType = $_POST['meatType'];
        $cut = $_POST['cut'];
        $origin = $_POST['origin'];
        $seasonality = $_POST['seasonality'];
        $price = $_POST['price'];

        $query = "UPDATE meat_product_t SET meat_type = '$meatType', cut = '$cut', origin = '$origin', 
                  seasonality = '$seasonality', price = '$price' WHERE id = $id";
        if ($conn->query($query) === TRUE) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $message = "Error: " . $conn->error;
        }
    } elseif ($_POST['action'] == "delete") {
        $id = $_POST['id'];
        $query = "DELETE FROM meat_product_t WHERE id = $id";
        if ($conn->query($query) === TRUE) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

$query = "SELECT * FROM meat_product_t";
$result = $conn->query($query);

$productForEdit = null;
if (isset($_GET['edit_id'])) {
    $id = $_GET['edit_id'];
    $query = "SELECT * FROM meat_product_t WHERE id = $id";
    $productForEdit = $conn->query($query)->fetch_assoc();
}

$countQuery = "SELECT 
    SUM(CASE WHEN cut = 'raw' THEN 1 ELSE 0 END) AS raw_count,
    SUM(CASE WHEN cut = 'packet' THEN 1 ELSE 0 END) AS packet_count,
    COUNT(*) AS total_count
FROM meat_product_t";
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
  <h4>MEAT KING</h4>
  <a href="#">Dashboard</a>
  <a href="#">Livestock</a>
  <a href="#">Order</a>
  <a href="#">Location</a>
</div>

<div class="content">
  <div class="topbar">
    <h5>Product Information</h5>
    <span><strong>Rabeya</strong></span>
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

  <div class="table-wrapper">
    <form class="row g-3 mb-4" method="POST">
      <input type="hidden" name="id" value="<?php echo $productForEdit['id'] ?? ''; ?>">
      <input type="hidden" name="action" value="<?php echo $productForEdit ? 'edit' : 'add'; ?>">
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
        <input type="number" class="form-control" name="price" value="<?php echo $productForEdit['price'] ?? ''; ?>" placeholder="Price" required>
      </div>
      <div class="col-12 text-end">
        <button type="submit" class="btn btn-primary"><?php echo $productForEdit ? 'Update' : 'Submit'; ?></button>
      </div>
    </form>

    <table class="table table-bordered">
      <thead class="table-light">
        <tr>
          <th>Meat Type</th>
          <th>Cut</th>
          <th>Origin</th>
          <th>Seasonality</th>
          <th>Price</th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
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
</div>

</body>
</html>
<?php $conn->close(); ?>
