<?php
require_once 'config.php';

// Check if order number exists in session
if (!isset($_SESSION['order_number'])) {
    header('Location: index.php');
    exit;
}

$orderNumber = $_SESSION['order_number'];
unset($_SESSION['order_number']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopease | Order Confirmation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/nav.php'; ?>

    <!-- Confirmation Content -->
    <section class="py-5">
        <div class="container px-4 px-lg-5">
            <div class="card mb-4">
                <div class="card-body text-center py-5">
                    <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                    <h2 class="mt-3">Thank You for Your Order!</h2>
                    <p class="lead">Your order has been placed successfully.</p>
                    <p>Order #: <span class="fw-bold"><?php echo $orderNumber; ?></span></p>
                    <p>You will receive an email confirmation shortly.</p>
                    <div class="mt-4">
                        <a href="products.php" class="btn btn-dark">Continue Shopping</a>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <a href="profile.php" class="btn btn-outline-dark ms-2">View Your Orders</a>
                        <?php endif; ?>
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