<?php
session_start();
$_SESSION['farmer_id'] = 1; // For testing only
$farmer_id = $_SESSION['farmer_id'];

$host = "localhost";
$username = "root";
$password = ""; // default is empty for XAMPP
$database = "mking";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add"])) {
    $season = $_POST["season"];
    $marketage = $_POST["marketage"];
    $feed_type = $_POST["feed_type"];
    $conn->query("INSERT INTO livestock (season, marketage, feed_type) VALUES ('$season', '$marketage', '$feed_type')");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['edit_save'])) {
    $id = $_POST['id'];
    $season = $_POST['season'];
    $marketage = $_POST['marketage'];
    $feed_type = $_POST['feed_type'];
    $conn->query("UPDATE livestock SET season = '$season', marketage = '$marketage', feed_type = '$feed_type' WHERE livestock_batch_id = $id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM livestock WHERE livestock_batch_id = $id");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Get max and count data for dashboard cards
$maxSeason = '';
$maxAge = 0;
$maxFeed = '';
$entryCount = 0;
$result = $conn->query("SELECT * FROM livestock");
if ($result) {
    $entryCount = $result->num_rows;
    while ($row = $result->fetch_assoc()) {
        if ($row['marketage'] > $maxAge) {
            $maxAge = $row['marketage'];
            $maxSeason = $row['season'];
            $maxFeed = $row['feed_type'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Livestock - Farmer Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f6f9;
      font-family: 'Segoe UI', sans-serif;
    }

    .sidebar {
      width: 250px;
      height: 100vh;
      position: fixed;
      background-color: #212529;
      color: white;
      padding-top: 30px;
    }

    .sidebar h4 {
      text-align: center;
      margin-bottom: 30px;
      font-weight: bold;
      color: red;
    }

    .sidebar a {
      display: block;
      padding: 12px 30px;
      color: #d1d1d1;
      text-decoration: none;
    }

    .sidebar a:hover,
    .sidebar a.active {
      background-color: #343a40;
      color: white;
    }

    .content {
      margin-left: 250px;
      padding: 20px 40px;
    }

    .topbar {
      display: flex;
      justify-content: space-between;
      background: #000;
      color: #fff;
      padding: 15px 20px;
      border-radius: 10px;
      box-shadow: 0 1px 5px rgba(0,0,0,0.2);
      margin-bottom: 30px;
    }

    .dashboard-cards .card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      text-align: center;
    }

    .table-wrapper {
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 0 8px rgba(0, 0, 0, 0.05);
    }
  </style>
</head>
<body>
<div class="sidebar">
  <h4>MEAT KING</h4>
  <a href="farmer.html">Dashboard</a>
</div>

<div class="content">
  <div class="topbar">
    <h5>Livestock Information</h5>
    <span><strong>Rabeya</strong>
      <img src="file:///C:/Users/User/Desktop/dbms/MEAT%20KING/img/user2.jpg" alt="Rabeya" class="rounded-circle me-lg-2" style="width: 40px; height: 40px;">
    </span>
  </div>

  <div class="row dashboard-cards mb-4">
    <div class="col-md-3">
      <div class="card p-3">
        <h6>Breeding Season</h6>
        <p class="fs-5 text-primary" id="maxSeason"><?php echo $maxSeason; ?></p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3">
        <h6>Market Age</h6>
        <p class="fs-5 text-success" id="maxAge"><?php echo $maxAge; ?> Weeks</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3">
        <h6>Feed Type</h6>
        <p class="fs-5 text-warning" id="maxFeed"><?php echo $maxFeed; ?></p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3">
        <h6>Total Entries</h6>
        <p class="fs-5 text-danger" id="entryCount"><?php echo $entryCount; ?></p>
      </div>
    </div>
  </div>

  <div class="table-wrapper">
    <h4 class="mb-3">Livestock Records</h4>
    <form method="post" class="row g-3 mb-4" id="livestockForm">
      <input type="hidden" name="id" id="id">
      <div class="col-md-4">
        <input type="text" name="season" id="season" class="form-control" placeholder="Breeding Season" required>
      </div>
      <div class="col-md-4">
        <input type="number" name="marketage" id="marketage" class="form-control" placeholder="Market Age" required>
      </div>
      <div class="col-md-4">
        <input type="text" name="feed_type" id="feed_type" class="form-control" placeholder="Feed Type" required>
      </div>
      <div class="col-12">
        <button type="submit" name="add" id="addBtn" class="btn btn-primary">Add</button>
        <button type="submit" name="edit_save" id="editBtn" class="btn btn-success d-none">Save Changes</button>
        <button type="button" id="cancelEdit" class="btn btn-secondary d-none">Cancel</button>
      </div>
    </form>

    <table class="table table-bordered">
      <thead>
        <tr>
          <th>ID</th>
          <th>Season</th>
          <th>Market Age</th>
          <th>Feed Type</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $result = $conn->query("SELECT * FROM livestock");
        while ($row = $result->fetch_assoc()):
        ?>
        <tr>
          <td><?= $row['livestock_batch_id'] ?></td>
          <td><?= htmlspecialchars($row['season']) ?></td>
          <td><?= $row['marketage'] ?></td>
          <td><?= htmlspecialchars($row['feed_type']) ?></td>
          <td>
            <button class="btn btn-sm btn-warning editBtn"
              data-id="<?= $row['livestock_batch_id'] ?>"
              data-season="<?= htmlspecialchars($row['season']) ?>"
              data-marketage="<?= $row['marketage'] ?>"
              data-feed_type="<?= htmlspecialchars($row['feed_type']) ?>">Edit</button>
            <a href="?delete=<?= $row['livestock_batch_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')">Delete</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
  document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.getElementById('id').value = btn.dataset.id;
      document.getElementById('season').value = btn.dataset.season;
      document.getElementById('marketage').value = btn.dataset.marketage;
      document.getElementById('feed_type').value = btn.dataset.feed_type;

      document.getElementById('addBtn').classList.add('d-none');
      document.getElementById('editBtn').classList.remove('d-none');
      document.getElementById('cancelEdit').classList.remove('d-none');
    });
  });

  document.getElementById('cancelEdit').addEventListener('click', () => {
    document.getElementById('id').value = '';
    document.getElementById('season').value = '';
    document.getElementById('marketage').value = '';
    document.getElementById('feed_type').value = '';

    document.getElementById('addBtn').classList.remove('d-none');
    document.getElementById('editBtn').classList.add('d-none');
    document.getElementById('cancelEdit').classList.add('d-none');
  });
</script>
</body>
</html>
