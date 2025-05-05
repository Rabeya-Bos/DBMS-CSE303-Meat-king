<?php
require_once 'config.php';

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Calculate order totals
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$tax = $subtotal * 0.1; // 10% tax
$shipping = 5.99; // Flat shipping fee
$total = $subtotal + $tax + $shipping;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $country = trim($_POST['country']);
    $state = trim($_POST['state']);
    $zip = trim($_POST['zip']);
    $phone = trim($_POST['phone']);
    
    // Simple validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($address) || 
        empty($country) || empty($state) || empty($zip) || empty($phone)) {
        $_SESSION['error'] = 'Please fill in all required fields';
        header('Location: checkout.php');
        exit;
    }
    
    // If user is logged in, create order in database
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
        $orderNumber = 'ORD-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        $shippingAddress = "$firstName $lastName\n$address\n$state, $zip\n$country\nPhone: $phone";
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Insert order
            $orderSql = "INSERT INTO orders (user_id, order_number, total_amount, shipping_address) 
                         VALUES (?, ?, ?, ?)";
            $orderStmt = $conn->prepare($orderSql);
            $orderStmt->bind_param("isds", $userId, $orderNumber, $total, $shippingAddress);
            $orderStmt->execute();
            
            $orderId = $conn->insert_id;
            
            // Insert order items
            $itemSql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                         VALUES (?, ?, ?, ?)";
            $itemStmt = $conn->prepare($itemSql);
            
            foreach ($_SESSION['cart'] as $item) {
                $itemStmt->bind_param("iiid", $orderId, $item['id'], $item['quantity'], $item['price']);
                $itemStmt->execute();
            }
            
            // Commit transaction
            $conn->commit();
            
            // Clear cart
            $_SESSION['cart'] = [];
            
            // Set success message and redirect
            $_SESSION['success'] = 'Order placed successfully! Your order number is ' . $orderNumber;
            $_SESSION['order_number'] = $orderNumber;
            header('Location: order-confirmation.php');
            exit;
            
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $_SESSION['error'] = 'An error occurred while processing your order. Please try again.';
            header('Location: checkout.php');
            exit;
        }
    } else {
        // Guest checkout - just show confirmation page
        $orderNumber = 'ORD-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        $_SESSION['cart'] = [];
        $_SESSION['success'] = 'Order placed successfully! Your order number is ' . $orderNumber;
        $_SESSION['order_number'] = $orderNumber;
        header('Location: order-confirmation.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopease | Checkout</title>
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
                <h1 class="fw-bold">Checkout</h1>
                <p class="lead">Complete your order</p>
            </div>
        </div>
    </header>

    <!-- Checkout content -->
    <section class="py-5">
        <div class="container px-4 px-lg-5">
            <form method="POST" action="checkout.php">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Shipping Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="fw-bold mb-0">Shipping Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="firstName" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="firstName" name="firstName" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lastName" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lastName" name="lastName" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="address" name="address" required>
                                </div>
                                <div class="mb-3">
                                    <label for="address2" class="form-label">Address 2 (Optional)</label>
                                    <input type="text" class="form-control" id="address2" name="address2">
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-5 mb-3 mb-md-0">
                                        <label for="country" class="form-label">Country</label>
                                        <select class="form-select" id="country" name="country" required>
                                            <option value="">Choose...</option>
                                            <option value="USA">United States</option>
                                            <option value="CAN">Canada</option>
                                            <option value="UK">United Kingdom</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <label for="state" class="form-label">State</label>
                                        <select class="form-select" id="state" name="state" required>
                                            <option value="">Choose...</option>
                                            <option value="CA">California</option>
                                            <option value="NY">New York</option>
                                            <option value="TX">Texas</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="zip" class="form-label">Zip</label>
                                        <input type="text" class="form-control" id="zip" name="zip" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Payment Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="fw-bold mb-0">Payment Information</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="card-name" class="form-label">Name on Card</label>
                                    <input type="text" class="form-control" id="card-name" name="card_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="card-number" class="form-label">Card Number</label>
                                    <input type="text" class="form-control" id="card-number" name="card_number" placeholder="XXXX XXXX XXXX XXXX" required>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="card-expiry" class="form-label">Expiration Date</label>
                                        <input type="text" class="form-control" id="card-expiry" name="card_expiry" placeholder="MM/YY" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="card-cvv" class="form-label">CVV</label>
                                        <input type="text" class="form-control" id="card-cvv" name="card_cvv" placeholder="XXX" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment-method" id="payment-credit" value="credit" checked>
                                        <label class="form-check-label" for="payment-credit">
                                            Credit Card
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment-method" id="payment-paypal" value="paypal">
                                        <label class="form-check-label" for="payment-paypal">
                                            PayPal
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="fw-bold mb-0">Order Summary</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <?php foreach($_SESSION['cart'] as $item): ?>
                                        <li class="list-group-item d-flex justify-content-between lh-sm">
                                            <div>
                                                <h6 class="my-0"><?php echo $item['name']; ?></h6>
                                                <small class="text-muted">Quantity: <?php echo $item['quantity']; ?></small>
                                            </div>
                                            <span class="text-muted"><?php echo number_format($item['price'] * $item['quantity'], 2); ?> Taka</span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Subtotal</span>
                                    <span id="checkout-subtotal"><?php echo number_format($subtotal, 2); ?> Taka</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Shipping</span>
                                    <span id="checkout-shipping"><?php echo number_format($shipping, 2); ?> Taka</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Tax</span>
                                    <span id="checkout-tax"><?php echo number_format($tax, 2); ?> Taka</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3">
                                    <strong>Total</strong>
                                    <strong id="checkout-total"><?php echo number_format($total, 2); ?> Taka</strong>
                                </div>
                                <button type="submit" class="btn btn-dark w-100" id="place-order-btn">Place Order</button>
                                <a href="cart.php" class="btn btn-outline-dark w-100 mt-2">Back to Cart</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>