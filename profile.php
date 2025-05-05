<?php
require_once 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user details
$userSql = "SELECT * FROM users WHERE id = ?";
$userStmt = $conn->prepare($userSql);
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userResult = $userStmt->get_result();
$user = $userResult->fetch_assoc();

// Fetch user's orders
$ordersSql = "SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC";
$ordersStmt = $conn->prepare($ordersSql);
$ordersStmt->bind_param("i", $userId);
$ordersStmt->execute();
$ordersResult = $ordersStmt->get_result();
$orders = [];
while ($row = $ordersResult->fetch_assoc()) {
    $orders[] = $row;
}

// Calculate dashboard statistics
$totalProducts = 0;
$totalExpense = 0;
$dailyPurchases = [];
$monthlyPurchases = [];

// Initialize daily purchase data for the last 7 days
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $dailyPurchases[$date] = 0;
}

// Initialize monthly purchase data for the last 6 months
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    $monthlyPurchases[$month] = 0;
}

// Process order data for statistics
if (!empty($orders)) {
    foreach ($orders as $order) {
        $totalExpense += $order['total_amount'];
        
        // Get order date
        $orderDate = date('Y-m-d', strtotime($order['order_date']));
        $orderMonth = date('Y-m', strtotime($order['order_date']));
        
        // Add to daily purchases if within the last 7 days
        if (isset($dailyPurchases[$orderDate])) {
            $dailyPurchases[$orderDate] += $order['total_amount'];
        }
        
        // Add to monthly purchases if within the last 6 months
        if (isset($monthlyPurchases[$orderMonth])) {
            $monthlyPurchases[$orderMonth] += $order['total_amount'];
        }
        
        // Fetch items in this order
        $itemsSql = "SELECT SUM(quantity) as total_items FROM order_items WHERE order_id = ?";
        $itemsStmt = $conn->prepare($itemsSql);
        $itemsStmt->bind_param("i", $order['id']);
        $itemsStmt->execute();
        $itemsResult = $itemsStmt->get_result();
        $items = $itemsResult->fetch_assoc();
        $totalProducts += $items['total_items'];
    }
}

// Convert daily purchases to JSON for the chart
$dailyLabels = array_keys($dailyPurchases);
$dailyData = array_values($dailyPurchases);
$dailyLabelsJson = json_encode(array_map(function($date) {
    return date('M d', strtotime($date));
}, $dailyLabels));
$dailyDataJson = json_encode($dailyData);

// Convert monthly purchases to JSON for the chart
$monthlyLabels = array_keys($monthlyPurchases);
$monthlyData = array_values($monthlyPurchases);
$monthlyLabelsJson = json_encode(array_map(function($month) {
    return date('M Y', strtotime($month));
}, $monthlyLabels));
$monthlyDataJson = json_encode($monthlyData);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopease | My Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #f8f9fa;
            border-right: 1px solid #e9ecef;
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: #495057;
            border-radius: 0;
            padding: 0.8rem 1rem;
        }
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
        }
        .sidebar .nav-link.active {
            background-color: #212529;
            color: #fff;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .main-content {
            padding: 20px;
        }
        @media (max-width: 767.98px) {
            .sidebar {
                min-height: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Main Navigation -->
    <?php include 'includes/nav.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="text-center mb-4">
                    <div class="mb-2">
                        <i class="bi bi-person-circle" style="font-size: 3rem;"></i>
                    </div>
                    <h5><?php echo htmlspecialchars($user['name']); ?></h5>
                    <p class="text-muted small"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manageprofile.php">
                            <i class="bi bi-person"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">
                            <i class="bi bi-bag"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item mt-3">
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Page header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h2">My Dashboard</h1>
                        <p class="text-muted">Welcome back, <?php echo htmlspecialchars($user['name']); ?>!</p>
                    </div>
                    <div>
                        <a href="products.php" class="btn btn-outline-dark">
                            <i class="bi bi-cart-plus"></i> Continue Shopping
                        </a>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-4 mb-4">
                    <div class="col">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title">Total Buys</h5>
                                        <h2 class="display-4 mb-0"><?php echo count($orders); ?></h2>
                                    </div>
                                    <i class="bi bi-cart-check fs-1"></i>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <small class="text-white">All time purchases</small>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title">Total Products</h5>
                                        <h2 class="display-4 mb-0"><?php echo $totalProducts; ?></h2>
                                    </div>
                                    <i class="bi bi-box fs-1"></i>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <small class="text-white">Items purchased</small>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-warning text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title">Total Orders</h5>
                                        <h2 class="display-4 mb-0"><?php echo count($orders); ?></h2>
                                    </div>
                                    <i class="bi bi-bag fs-1"></i>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <small class="text-white">Completed orders</small>
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="card bg-info text-white h-100">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="card-title">Total Expense</h5>
                                        <h2 class="display-4 mb-0"><?php echo number_format($totalExpense, 0); ?></h2>
                                    </div>
                                    <i class="bi bi-currency-dollar fs-1"></i>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-0">
                                <small class="text-white">Taka spent</small>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Purchase History Graphs -->
                <div class="row">
                    <!-- Daily Purchase Graph -->
                    <div class="col-lg-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Daily Purchase Activity</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="dailyPurchaseChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Monthly Purchase Graph -->
                    <div class="col-lg-6">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Monthly Purchase Activity</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="monthlyPurchaseChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Orders -->
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Orders</h5>
                        <a href="orders.php" class="btn btn-sm btn-outline-dark">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($orders)): ?>
                            <div class="text-center py-5" id="no-orders">
                                <i class="bi bi-box-seam" style="font-size: 3rem;"></i>
                                <h4 class="mt-3">No Orders Yet</h4>
                                <p>You haven't placed any orders yet.</p>
                                <a href="products.php" class="btn btn-dark mt-2">Start Shopping</a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        // Display only the 5 most recent orders
                                        $recentOrders = array_slice($orders, 0, 5);
                                        foreach($recentOrders as $order): 
                                        ?>
                                            <tr>
                                                <td><?php echo $order['order_number']; ?></td>
                                                <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php echo $order['status'] === 'Delivered' ? 'success' : ($order['status'] === 'Pending' ? 'warning' : 'info'); ?>">
                                                        <?php echo $order['status']; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo number_format($order['total_amount'], 2); ?> Taka</td>
                                                <td>
                                                    <a href="order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-dark">View</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize daily purchase chart
            const dailyCtx = document.getElementById('dailyPurchaseChart').getContext('2d');
            const dailyLabels = <?php echo $dailyLabelsJson; ?>;
            const dailyData = <?php echo $dailyDataJson; ?>;
            
            new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: dailyLabels,
                    datasets: [{
                        label: 'Daily Purchases (Taka)',
                        data: dailyData,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        tension: 0.1,
                        fill: true,
                        pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Last 7 Days',
                            font: {
                                size: 14
                            }
                        },
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' Taka';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + ' Tk';
                                }
                            }
                        }
                    }
                }
            });
            
            // Initialize monthly purchase chart
            const monthlyCtx = document.getElementById('monthlyPurchaseChart').getContext('2d');
            const monthlyLabels = <?php echo $monthlyLabelsJson; ?>;
            const monthlyData = <?php echo $monthlyDataJson; ?>;
            
            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: monthlyLabels,
                    datasets: [{
                        label: 'Monthly Purchases (Taka)',
                        data: monthlyData,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Last 6 Months',
                            font: {
                                size: 14
                            }
                        },
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' Taka';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + ' Tk';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>