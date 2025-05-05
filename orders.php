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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopease | My Orders</title>
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
        .order-card {
            transition: all 0.3s ease;
        }
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
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
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manageprofile.php">
                            <i class="bi bi-person"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="orders.php">
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
                        <h1 class="h2">My Orders</h1>
                        <p class="text-muted">View and track your order history</p>
                    </div>
                    <div>
                        <a href="products.php" class="btn btn-outline-dark">
                            <i class="bi bi-cart-plus"></i> Continue Shopping
                        </a>
                    </div>
                </div>

                <!-- Orders List -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Order History</h5>
                            <div>
                                <select class="form-select form-select-sm" id="order-filter">
                                    <option value="all">All Orders</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Processing">Processing</option>
                                    <option value="Shipped">Shipped</option>
                                    <option value="Delivered">Delivered</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if (empty($orders)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-bag-x" style="font-size: 4rem;"></i>
                                <h4 class="mt-3">No Orders Found</h4>
                                <p>You haven't placed any orders yet.</p>
                                <a href="products.php" class="btn btn-dark mt-2">Start Shopping</a>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach($orders as $order): ?>
                                    <div class="col-lg-6 mb-4 order-item" data-status="<?php echo $order['status']; ?>">
                                        <div class="card order-card h-100">
                                            <div class="card-header bg-light">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold">Order #<?php echo $order['order_number']; ?></span>
                                                    <span class="badge bg-<?php echo $order['status'] === 'Delivered' ? 'success' : ($order['status'] === 'Pending' ? 'warning' : 'info'); ?>">
                                                        <?php echo $order['status']; ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between mb-3">
                                                    <div>
                                                        <p class="text-muted mb-0">Order Date</p>
                                                        <p class="mb-0"><?php echo date('M d, Y', strtotime($order['order_date'])); ?></p>
                                                    </div>
                                                    <div class="text-end">
                                                        <p class="text-muted mb-0">Total Amount</p>
                                                        <p class="fw-bold mb-0"><?php echo number_format($order['total_amount'], 2); ?> Taka</p>
                                                    </div>
                                                </div>
                                                
                                                <?php
                                                // Get some items from this order
                                                $itemsSql = "SELECT oi.*, p.name FROM order_items oi 
                                                            JOIN products p ON oi.product_id = p.id 
                                                            WHERE oi.order_id = ? LIMIT 2";
                                                $itemsStmt = $conn->prepare($itemsSql);
                                                $itemsStmt->bind_param("i", $order['id']);
                                                $itemsStmt->execute();
                                                $itemsResult = $itemsStmt->get_result();
                                                $items = [];
                                                while ($row = $itemsResult->fetch_assoc()) {
                                                    $items[] = $row;
                                                }
                                                
                                                // Get total item count
                                                $countSql = "SELECT COUNT(*) as count FROM order_items WHERE order_id = ?";
                                                $countStmt = $conn->prepare($countSql);
                                                $countStmt->bind_param("i", $order['id']);
                                                $countStmt->execute();
                                                $countResult = $countStmt->get_result();
                                                $totalItems = $countResult->fetch_assoc()['count'];
                                                ?>
                                                
                                                <div class="mt-3">
                                                    <p class="fw-bold mb-2">Items:</p>
                                                    <ul class="list-unstyled">
                                                        <?php foreach($items as $item): ?>
                                                            <li>
                                                                <i class="bi bi-dot"></i> <?php echo $item['name']; ?> 
                                                                <span class="text-muted">(<?php echo $item['quantity']; ?> × <?php echo number_format($item['price'], 2); ?> Taka)</span>
                                                            </li>
                                                        <?php endforeach; ?>
                                                        
                                                        <?php if($totalItems > 2): ?>
                                                            <li class="text-muted">
                                                                <i class="bi bi-three-dots"></i> and <?php echo $totalItems - 2; ?> more item(s)
                                                            </li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="card-footer bg-transparent">
                                                <a href="order-detail.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-dark w-100">View Order Details</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
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
    <script>
        $(document).ready(function() {
            // Order filtering
            $('#order-filter').on('change', function() {
                const status = $(this).val();
                
                if (status === 'all') {
                    $('.order-item').show();
                } else {
                    $('.order-item').hide();
                    $(`.order-item[data-status="${status}"]`).show();
                }
            });
        });
    </script>
</body>
</html>