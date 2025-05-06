<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "mking");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create tables if not exists
$conn->query("CREATE TABLE IF NOT EXISTS wholesaler_storage_inventory_t (
    id INT AUTO_INCREMENT PRIMARY KEY,
    wholesaler_id VARCHAR(50),
    storage_id VARCHAR(50),
    temperature DECIMAL(5,2),
    meat_type VARCHAR(50),
    product_quantity_kg DECIMAL(10,2),
    retailer_id VARCHAR(50),
    selling_quantity_kg DECIMAL(10,2),
    status VARCHAR(20),
    date DATE
)");

$conn->query("CREATE TABLE IF NOT EXISTS retailer_storage_inventory_t (
    id INT AUTO_INCREMENT PRIMARY KEY,
    retailer_id VARCHAR(50),
    storage_id VARCHAR(50),
    temperature DECIMAL(5,2),
    product_quantity_kg DECIMAL(10,2),
    meat_type VARCHAR(50),
    wholesaler_id VARCHAR(50),
    status VARCHAR(20),
    date DATE
)");

// ========== WHOLESALER INSERT / UPDATE ==========
if (isset($_POST['save_wholesaler'])) {
    if ($_POST['id']) {
        // Update
        $stmt = $conn->prepare("UPDATE wholesaler_storage_inventory_t SET wholesaler_id=?, storage_id=?, temperature=?, meat_type=?, product_quantity_kg=?, retailer_id=?, selling_quantity_kg=?, status=?, date=? WHERE id=?");
        $stmt->bind_param("ssdsdssdsi", $_POST['wholesaler_id'], $_POST['storage_id'], $_POST['temperature'], $_POST['meat_type'], $_POST['product_quantity_kg'], $_POST['retailer_id'], $_POST['selling_quantity_kg'], $_POST['status'], $_POST['date'], $_POST['id']);
    } else {
        // Insert
        $stmt = $conn->prepare("INSERT INTO wholesaler_storage_inventory_t (wholesaler_id, storage_id, temperature, meat_type, product_quantity_kg, retailer_id, selling_quantity_kg, status, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsdsdss", $_POST['wholesaler_id'], $_POST['storage_id'], $_POST['temperature'], $_POST['meat_type'], $_POST['product_quantity_kg'], $_POST['retailer_id'], $_POST['selling_quantity_kg'], $_POST['status'], $_POST['date']);
    }
    $stmt->execute();
    $stmt->close();
    header("Location: ".$_SERVER['PHP_SELF']); exit;
}

// ========== RETAILER INSERT / UPDATE ==========
if (isset($_POST['save_retailer'])) {
    if ($_POST['id']) {
        $stmt = $conn->prepare("UPDATE retailer_storage_inventory_t SET retailer_id=?, storage_id=?, temperature=?, product_quantity_kg=?, meat_type=?, wholesaler_id=?, status=?, date=? WHERE id=?");
        $stmt->bind_param("ssddssssi", $_POST['retailer_id'], $_POST['storage_id'], $_POST['temperature'], $_POST['product_quantity_kg'], $_POST['meat_type'], $_POST['wholesaler_id'], $_POST['status'], $_POST['date'], $_POST['id']);
    } else {
        $stmt = $conn->prepare("INSERT INTO retailer_storage_inventory_t (retailer_id, storage_id, temperature, product_quantity_kg, meat_type, wholesaler_id, status, date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssddssss", $_POST['retailer_id'], $_POST['storage_id'], $_POST['temperature'], $_POST['product_quantity_kg'], $_POST['meat_type'], $_POST['wholesaler_id'], $_POST['status'], $_POST['date']);
    }
    $stmt->execute();
    $stmt->close();
    header("Location: ".$_SERVER['PHP_SELF']); exit;
}

// DELETE
if (isset($_GET['delete_wholesaler'])) $conn->query("DELETE FROM wholesaler_storage_inventory_t WHERE id=" . intval($_GET['delete_wholesaler']));
if (isset($_GET['delete_retailer'])) $conn->query("DELETE FROM retailer_storage_inventory_t WHERE id=" . intval($_GET['delete_retailer']));

// FETCH edit data
$wholesaler_edit = $retailer_edit = null;
if (isset($_GET['edit_wholesaler'])) {
    $res = $conn->query("SELECT * FROM wholesaler_storage_inventory_t WHERE id=" . intval($_GET['edit_wholesaler']));
    $wholesaler_edit = $res->fetch_assoc();
}
if (isset($_GET['edit_retailer'])) {
    $res = $conn->query("SELECT * FROM retailer_storage_inventory_t WHERE id=" . intval($_GET['edit_retailer']));
    $retailer_edit = $res->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>realtime-supply</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { display: flex; min-height: 100vh; margin: 0; }
        .sidebar { width: 220px; background: #000; color: #fff; padding: 20px; }
        .sidebar h2 { color: red; }
        .sidebar a { color: #fff; text-decoration: none; display: block; margin: 15px 0; }
        .content { flex: 1; padding: 20px; background: #f8f9fa; }
        .search-bar { margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="sidebar">
  <a href="#"><h3 style="color:red; font-weight:bold;">MEAT KING</h3></a>
  <a class="active" href="#"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
  <a href="product1.php"><i class="fas fa-industry me-2"></i> Production Information</a>
  <a href="farmer_dashboard1.php"><i class="fas fa-history me-2"></i> Historical Production Data</a>
  <a href="consumer12.php"><i class="fas fa-users me-2"></i> Consumer Demand Data</a>
  <a href="marketTrend.html"><i class="fas fa-chart-line me-2"></i> Market Trends</a>
  <a href="nutritionist.php"><i class="fas fa-apple-alt me-2"></i> Nutritionist</a>
  <a href="admin.php"><i class="fas fa-handshake me-2"></i>Buyer/seller</a>
  <a href="loging.php"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
</div>

</div>
<div class="content">
    <!-- Search Bar -->
    <div class="search-bar">
        <input type="text" id="searchWholesaler" class="form-control" placeholder="Search Wholesalers">
        <input type="text" id="searchRetailer" class="form-control mt-2" placeholder="Search Retailers">
    </div>

    <!-- Chart -->
    <div class="mb-4">
        <canvas id="inventoryChart"></canvas>
    </div>

    <h3>Wholesaler Inventory</h3>
    <form method="post" class="mb-3">
        <input type="hidden" name="save_wholesaler" value="1">
        <input type="hidden" name="id" value="<?= $wholesaler_edit['id'] ?? '' ?>">
        <div class="row g-2">
            <?php function wf($key) { global $wholesaler_edit; return $wholesaler_edit[$key] ?? ''; } ?>
            <div class="col"><input type="text" name="wholesaler_id" class="form-control" value="<?= wf('wholesaler_id') ?>" placeholder="Wholesaler ID" required></div>
            <div class="col"><input type="text" name="storage_id" class="form-control" value="<?= wf('storage_id') ?>" placeholder="Storage ID" required></div>
            <div class="col"><input type="number" step="0.1" name="temperature" class="form-control" value="<?= wf('temperature') ?>" placeholder="Temperature" required></div>
            <div class="col"><input type="text" name="meat_type" class="form-control" value="<?= wf('meat_type') ?>" placeholder="Meat Type" required></div>
            <div class="col"><input type="number" step="0.01" name="product_quantity_kg" class="form-control" value="<?= wf('product_quantity_kg') ?>" placeholder="Product Quantity" required></div>
            <div class="col"><input type="text" name="retailer_id" class="form-control" value="<?= wf('retailer_id') ?>" placeholder="Retailer ID" required></div>
            <div class="col"><input type="number" step="0.01" name="selling_quantity_kg" class="form-control" value="<?= wf('selling_quantity_kg') ?>" placeholder="Selling Qty" required></div>
            <div class="col"><input type="text" name="status" class="form-control" value="<?= wf('status') ?>" placeholder="Status" required></div>
            <div class="col"><input type="date" name="date" class="form-control" value="<?= wf('date') ?>" required></div>
            <div class="col-auto"><button class="btn btn-<?= $wholesaler_edit ? 'primary' : 'success' ?>"><?= $wholesaler_edit ? 'Update' : 'Add' ?></button></div>
        </div>
    </form>
    <table class="table table-bordered" id="wholesalerTable">
        <thead><tr><th>ID</th><th>Wholesaler ID</th><th>Storage ID</th><th>Temp</th><th>Meat Type</th><th>Qty</th><th>Retailer ID</th><th>Selling Qty</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM wholesaler_storage_inventory_t");
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['id']}</td><td>{$row['wholesaler_id']}</td><td>{$row['storage_id']}</td><td>{$row['temperature']}</td><td>{$row['meat_type']}</td><td>{$row['product_quantity_kg']}</td><td>{$row['retailer_id']}</td><td>{$row['selling_quantity_kg']}</td><td>{$row['status']}</td><td>{$row['date']}</td><td>
                <a href='?edit_wholesaler={$row['id']}'>Edit</a> | <a href='?delete_wholesaler={$row['id']}'>Delete</a></td></tr>";
            }
            ?>
        </tbody>
    </table>

    <h3>Retailer Inventory</h3>
    <form method="post" class="mb-3">
        <input type="hidden" name="save_retailer" value="1">
        <input type="hidden" name="id" value="<?= $retailer_edit['id'] ?? '' ?>">
        <div class="row g-2">
            <?php function rf($key) { global $retailer_edit; return $retailer_edit[$key] ?? ''; } ?>
            <div class="col"><input type="text" name="retailer_id" class="form-control" value="<?= rf('retailer_id') ?>" placeholder="Retailer ID" required></div>
            <div class="col"><input type="text" name="storage_id" class="form-control" value="<?= rf('storage_id') ?>" placeholder="Storage ID" required></div>
            <div class="col"><input type="number" step="0.1" name="temperature" class="form-control" value="<?= rf('temperature') ?>" placeholder="Temperature" required></div>
            <div class="col"><input type="text" name="meat_type" class="form-control" value="<?= rf('meat_type') ?>" placeholder="Meat Type" required></div>
            <div class="col"><input type="number" step="0.01" name="product_quantity_kg" class="form-control" value="<?= rf('product_quantity_kg') ?>" placeholder="Product Quantity" required></div>
            <div class="col"><input type="text" name="wholesaler_id" class="form-control" value="<?= rf('wholesaler_id') ?>" placeholder="Wholesaler ID" required></div>
            <div class="col"><input type="text" name="status" class="form-control" value="<?= rf('status') ?>" placeholder="Status" required></div>
            <div class="col"><input type="date" name="date" class="form-control" value="<?= rf('date') ?>" required></div>
            <div class="col-auto"><button class="btn btn-<?= $retailer_edit ? 'primary' : 'success' ?>"><?= $retailer_edit ? 'Update' : 'Add' ?></button></div>
        </div>
    </form>
    <table class="table table-bordered" id="retailerTable">
        <thead><tr><th>ID</th><th>Retailer ID</th><th>Storage ID</th><th>Temp</th><th>Meat Type</th><th>Qty</th><th>Wholesaler ID</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM retailer_storage_inventory_t");
            while ($row = $result->fetch_assoc()) {
                echo "<tr><td>{$row['id']}</td><td>{$row['retailer_id']}</td><td>{$row['storage_id']}</td><td>{$row['temperature']}</td><td>{$row['meat_type']}</td><td>{$row['product_quantity_kg']}</td><td>{$row['wholesaler_id']}</td><td>{$row['status']}</td><td>{$row['date']}</td><td>
                <a href='?edit_retailer={$row['id']}'>Edit</a> | <a href='?delete_retailer={$row['id']}'>Delete</a></td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
    // Chart.js configuration
    const ctx = document.getElementById('inventoryChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Beef', 'fish', 'Chicken', 'Lamb'],
            datasets: [{
                label: 'Total Inventory (kg)',
                data: [2500, 1500, 2200, 1800],
                backgroundColor: '#4e73df',
                borderColor: '#4e73df',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Search functionality for Wholesalers
    document.getElementById("searchWholesaler").addEventListener("input", function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll("#wholesalerTable tbody tr");
        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });

    // Search functionality for Retailers
    document.getElementById("searchRetailer").addEventListener("input", function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll("#retailerTable tbody tr");
        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });

</script>
</body>
</html>

