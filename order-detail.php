<?php
require_once 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check if order ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: profile.php');
    exit;
}

$orderId = intval($_GET['id']);
$userId = $_SESSION['user_id'];

// Fetch order details
$orderSql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$orderStmt = $conn->prepare($orderSql);
$orderStmt->bind_param("ii", $orderId, $userId);
$orderStmt->execute();
$orderResult = $orderStmt->get_result();

if ($orderResult->num_rows === 0) {
    $_SESSION['error'] = 'Order not found';
    header('Location: profile.php');
    exit;
}

$order = $orderResult->fetch_assoc();

// Fetch order items
$itemsSql = "SELECT oi.*, p.name, p.image FROM order_items oi 
             JOIN products p ON oi.product_id = p.id 
             WHERE oi.order_id = ?";
$itemsStmt = $conn->prepare($itemsSql);
$itemsStmt->bind_param("i", $orderId);
$itemsStmt->execute();
$itemsResult = $itemsStmt->get_result();
$items = [];
while ($row = $itemsResult->fetch_assoc()) {
    $items[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopease | Order Detail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/nav.php'; ?>

    <!-- Page header -->
    <header class="bg-light py-5">
        <div class="container px-4 px-lg-5">
            <div class="text-center">
                <h1 class="fw-bold">Order Details</h1>
                <p class="lead">Order #: <?php echo $order['order_number']; ?></p>
            </div>
        </div>
    </header>

    <!-- Order Details Section -->
    <section class="py-5">
        <div class="container px-4 px-lg-5">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="fw-bold mb-0">Items Ordered</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach($items as $item): ?>
                                <div class="row mb-3">
                                    <div class="col-md-2 col-4">
                                        <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="img-fluid rounded">
                                    </div>
                                    <div class="col-md-6 col-8">
                                        <h5><?php echo $item['name']; ?></h5>
                                        <p class="text-muted mb-0">Price: <?php echo number_format($item['price'], 2); ?> Taka</p>
                                        <p class="text-muted mb-0">Quantity: <?php echo $item['quantity']; ?></p>
                                    </div>
                                    <div class="col-md-4 col-12 mt-3 mt-md-0 text-md-end">
                                        <h6>Subtotal: <?php echo number_format($item['price'] * $item['quantity'], 2); ?> Taka</h6>
                                    </div>
                                </div>
                                <?php if (array_search($item, $items) < count($items) - 1): ?>
                                    <hr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="fw-bold mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span>Order Date:</span>
                                <span><?php echo date('M d, Y', strtotime($order['order_date'])); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Status:</span>
                                <span class="badge bg-<?php echo $order['status'] === 'Delivered' ? 'success' : ($order['status'] === 'Pending' ? 'warning' : 'info'); ?>"><?php echo $order['status']; ?></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total:</strong>
                                <strong><?php echo number_format($order['total_amount'], 2); ?> Taka</strong>
                            </div>
                            <a href="profile.php" class="btn btn-outline-dark w-100">Back to Orders</a>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="fw-bold mb-0">Shipping Address</h5>
                        </div>
                        <div class="card-body">
                            <address>
                                <?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?>
                            </address>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>