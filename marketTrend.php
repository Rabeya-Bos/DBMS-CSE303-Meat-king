<?php
// Database connection
$conn = mysqli_connect("localhost", "root", "", "mking");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle Add
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    $meat_type = $_POST['meatType'];
    $product_id = $_POST['productId'];
    $historical_date = $_POST['historicalDate'];
    $historical_price = $_POST['historicalPrice'];
    $current_price = $_POST['currentPrice'];
    $trend = (($current_price - $historical_price) / $historical_price) * 100;

    $sql = "INSERT INTO Trends (meat_type, product_id, historical_date, historical_price, current_price, trend)
            VALUES ('$meat_type', '$product_id', '$historical_date', '$historical_price', '$current_price', '$trend')";
    mysqli_query($conn, $sql);
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM Trends WHERE id=$id");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Handle Edit Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $id = $_POST['id'];
    $meat_type = $_POST['meatType'];
    $product_id = $_POST['productId'];
    $historical_date = $_POST['historicalDate'];
    $historical_price = $_POST['historicalPrice'];
    $current_price = $_POST['currentPrice'];
    $trend = (($current_price - $historical_price) / $historical_price) * 100;

    $sql = "UPDATE Trends SET
            meat_type = '$meat_type',
            product_id = '$product_id',
            historical_date = '$historical_date',
            historical_price = '$historical_price',
            current_price = '$current_price',
            trend = '$trend'
            WHERE id = $id";
    mysqli_query($conn, $sql);
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

// Fetch for edit
$edit_mode = false;
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $edit_id = $_GET['edit'];
    $edit_result = mysqli_query($conn, "SELECT * FROM Trends WHERE id=$edit_id");
    $edit_data = mysqli_fetch_assoc($edit_result);
}

// Fetch all data
$result = mysqli_query($conn, "SELECT * FROM Trends ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>MeatKing - Meat Product Analysis</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            background-color: #f4f4f4;
        }

        .navbar {
            background-color: #800000;
            padding: 15px 30px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .navbar ul {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        .navbar ul li a {
            color: white;
            text-decoration: none;
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            text-align: center;
            color: #800000;
        }

        .form-container input, .form-container button {
            padding: 8px;
            margin: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-container button {
            background-color: #800000;
            color: white;
            border: none;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #800000;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        canvas {
            margin: 30px 0;
            max-width: 100%;
            height: 350px;
        }
        regionalDemandChart {
    width: 10px !important;
    height: 50px !important;
    display: block;
    margin: 30px 0;
}

    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <div class="logo">MeatKing</div>
    <ul>
        <li><a href="#">Dashboard</a></li>
        <li><a href="#">Analysis</a></li>
        <li><a href="#">Production</a></li>
        <li><a href="#">Sales</a></li>
        <li><a href="#">Logout</a></li>
    </ul>
</div>

<div class="container">
    <h1>MeatKing Dashboard</h1>
    <h2>Bangladesh - Demand & Supply Analysis</h2>

    <canvas id="demandSupplyChart"></canvas>
    <canvas id="liveFluctuationChart"></canvas>
    <canvas id="regionalChart"></canvas>


    <h2><?= $edit_mode ? "Edit Trend" : "Add New Trend" ?></h2>
    <form method="POST">
        <?php if ($edit_mode): ?>
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
        <?php endif; ?>
        <div class="form-container">
            <input type="text" name="meatType" placeholder="Meat Type" required value="<?= $edit_mode ? $edit_data['meat_type'] : '' ?>">
            <input type="text" name="productId" placeholder="Product ID" required value="<?= $edit_mode ? $edit_data['product_id'] : '' ?>">
            <input type="date" name="historicalDate" required value="<?= $edit_mode ? $edit_data['historical_date'] : '' ?>">
            <input type="number" step="0.01" name="historicalPrice" placeholder="Historical Price" required value="<?= $edit_mode ? $edit_data['historical_price'] : '' ?>">
            <input type="number" step="0.01" name="currentPrice" placeholder="Current Price" required value="<?= $edit_mode ? $edit_data['current_price'] : '' ?>">
            <button type="submit" name="<?= $edit_mode ? 'update' : 'add' ?>"><?= $edit_mode ? 'Update Trend' : 'Add Trend' ?></button>
            <?php if ($edit_mode): ?>
                <a href="<?= $_SERVER['PHP_SELF'] ?>">Cancel</a>
            <?php endif; ?>
        </div>
    </form>
    <input type="text" id="searchInput" placeholder="Search by Meat Type">

    <h2>Trend Records</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Meat Type</th>
            <th>Product ID</th>
            <th>Date</th>
            <th>Historical Price</th>
            <th>Current Price</th>
            <th>Trend (%)</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['meat_type']) ?></td>
                <td><?= htmlspecialchars($row['product_id']) ?></td>
                <td><?= $row['historical_date'] ?></td>
                <td><?= $row['historical_price'] ?></td>
                <td><?= $row['current_price'] ?></td>
                <td><?= round($row['trend'], 2) ?>%</td>
                <td>
                    <a href="?edit=<?= $row['id'] ?>">Edit</a> |
                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>

<script>
    const ctx1 = document.getElementById('demandSupplyChart').getContext('2d');
    const gradient1 = ctx1.createLinearGradient(0, 0, 0, 400);
    gradient1.addColorStop(0, 'rgba(128, 0, 0, 0.7)');
    gradient1.addColorStop(1, 'rgba(128, 0, 0, 0.2)');

    const gradient2 = ctx1.createLinearGradient(0, 0, 0, 400);
    gradient2.addColorStop(0, 'rgba(218, 165, 32, 0.7)');
    gradient2.addColorStop(1, 'rgba(218, 165, 32, 0.2)');

    new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: ['Beef', 'Chicken', 'Mutton', 'Duck', 'Pigeon', 'Turkey', 'Goat', 'Fish'],
            datasets: [
                {
                    label: 'Demand (tons)',
                    data: [1250, 1800, 750, 320, 160, 180, 880, 110],
                    backgroundColor: gradient1,
                    borderColor: '#800000',
                    borderWidth: 1
                },
                {
                    label: 'Supply (tons)',
                    data: [1000, 1600, 650, 260, 120, 170, 700, 90],
                    backgroundColor: gradient2,
                    borderColor: '#DAA520',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const ctx2 = document.getElementById('liveFluctuationChart').getContext('2d');
    const fluctuationData = Array.from({ length: 20 }, () => 100 + Math.random() * 30);
    const fluctuationLabels = Array.from({ length: 20 }, (_, i) => `T${i + 1}`);

    const liveFluctuationChart = new Chart(ctx2, {
        type: 'line',
        data: {
            labels: fluctuationLabels,
            datasets: [{
                label: 'Live Production (kg)',
                data: fluctuationData,
                borderColor: '#800000',
                backgroundColor: 'rgba(128,0,0,0.2)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            animation: { duration: 0 },
            scales: {
                y: { beginAtZero: true }
            },
            interaction: { mode: 'nearest', axis: 'x', intersect: false },
        }
    });

    setInterval(() => {
        fluctuationData.shift();
        fluctuationData.push(100 + Math.random() * 30);
        liveFluctuationChart.update();
    }, 1200);

    function generateRandomData(baseValues, variation) {
        return baseValues.map(val => val + Math.floor(Math.random() * variation));
    }

    const regionalChart = new Chart(document.getElementById('regionalChart'), {
        type: 'doughnut',
        data: {
            labels: ['Dhaka', 'Chittagong', 'Khulna', 'Rajshahi', 'Sylhet', 'Barisal', 'Rangpur'],
            datasets: [{
                label: 'Demand (tons)',
                data: generateRandomData([500, 400, 300, 250, 200, 150, 180], 120),
                backgroundColor: ['#800000', '#A52A2A', '#B22222', '#DC143C', '#8B0000', '#CD5C5C', '#E9967A'],
                borderColor: '#fff',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            animation: {
                duration: 1200,
                easing: 'easeInOutCirc'
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#800000',
                        font: {
                            size: 14
                        }
                    }
                }
            }
        }
    });

    // Meat Type Search Filter
    document.getElementById('searchInput').addEventListener('input', function () {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll("table tr:not(:first-child)");

        rows.forEach(row => {
            const meatType = row.cells[1].textContent.toLowerCase();
            if (meatType.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

</body>
</html>
