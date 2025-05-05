<?php
require_once 'config.php';

// Initialize the cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add to cart (via AJAX)
if (isset($_POST['action']) && $_POST['action'] === 'add_to_cart' && isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    
    if ($quantity < 1) $quantity = 1;
    
    // Fetch product details
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        
        // Check if product is already in cart
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $productId) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $_SESSION['cart'][] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'image' => $product['image'],
                'quantity' => $quantity
            ];
        }
        
        echo json_encode(['success' => true, 'count' => array_sum(array_column($_SESSION['cart'], 'quantity'))]);
        exit;
    }
    
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

// Handle remove from cart
if (isset($_POST['action']) && $_POST['action'] === 'remove_from_cart' && isset($_POST['product_id'])) {
    $productId = intval($_POST['product_id']);
    
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $productId) {
            unset($_SESSION['cart'][$key]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
            break;
        }
    }
    
    echo json_encode(['success' => true, 'count' => array_sum(array_column($_SESSION['cart'], 'quantity'))]);
    exit;
}

// Handle update quantity
if (isset($_POST['action']) && $_POST['action'] === 'update_quantity' && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $productId = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    
    if ($quantity < 1) {
        // Remove item if quantity is less than 1
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $productId) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
                break;
            }
        }
    } else {
        // Update quantity
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $productId) {
                $item['quantity'] = $quantity;
                break;
            }
        }
    }
    
    echo json_encode([
        'success' => true, 
        'count' => array_sum(array_column($_SESSION['cart'], 'quantity')),
        'subtotal' => calculateSubtotal()
    ]);
    exit;
}

// Handle clear cart
if (isset($_POST['action']) && $_POST['action'] === 'clear_cart') {
    $_SESSION['cart'] = [];
    echo json_encode(['success' => true]);
    exit;
}

// Calculate cart subtotal
function calculateSubtotal() {
    $subtotal = 0;
    foreach ($_SESSION['cart'] as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }
    return $subtotal;
}

$subtotal = calculateSubtotal();
$tax = $subtotal * 0.1; // 10% tax
$shipping = $subtotal > 0 ? 5.99 : 0; // Flat shipping fee
$total = $subtotal + $tax + $shipping;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopease | Shopping Cart</title>
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
                <h1 class="fw-bold">Shopping Cart</h1>
                <p class="lead">Review your items and proceed to checkout</p>
            </div>
        </div>
    </header>

    <!-- Cart content -->
    <section class="py-5">
        <div class="container px-4 px-lg-5">
            <div class="row">
                <div class="col-lg-8" id="cart-items">
                    <?php if (empty($_SESSION['cart'])): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
                            <h3 class="mt-3">Your cart is empty</h3>
                            <p>Add some products to your cart to see them here.</p>
                            <a href="products.php" class="btn btn-dark mt-3">Continue Shopping</a>
                        </div>
                    <?php else: ?>
                        <div class="card mb-4">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Cart Items (<?php echo count($_SESSION['cart']); ?>)</h5>
                                    <button class="btn btn-sm btn-outline-danger" id="clear-cart">Clear Cart</button>
                                </div>
                            </div>
                            <div class="card-body">
                                <?php foreach($_SESSION['cart'] as $index => $item): ?>
                                    <div class="row mb-4 cart-item" data-id="<?php echo $item['id']; ?>">
                                        <div class="col-md-2 col-4">
                                            <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="img-fluid rounded cart-item-img">
                                        </div>
                                        <div class="col-md-6 col-8">
                                            <h5><?php echo $item['name']; ?></h5>
                                            <p class="text-muted mb-0"><?php echo number_format($item['price'], 2); ?> Taka</p>
                                        </div>
                                        <div class="col-md-2 col-6 mt-3 mt-md-0">
                                            <div class="d-flex align-items-center">
                                                <button class="btn btn-sm btn-outline-dark quantity-btn" data-action="decrease" data-id="<?php echo $item['id']; ?>">-</button>
                                                <input type="text" class="form-control form-control-sm mx-2 text-center quantity-input" value="<?php echo $item['quantity']; ?>" readonly>
                                                <button class="btn btn-sm btn-outline-dark quantity-btn" data-action="increase" data-id="<?php echo $item['id']; ?>">+</button>
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-6 mt-3 mt-md-0 text-end">
                                            <div class="d-flex flex-column align-items-end">
                                                <span class="fw-bold"><?php echo number_format($item['price'] * $item['quantity'], 2); ?> Taka</span>
                                                <button class="btn btn-sm text-danger remove-item mt-2" data-id="<?php echo $item['id']; ?>">
                                                    <i class="bi bi-trash"></i> Remove
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if ($index < count($_SESSION['cart']) - 1): ?>
                                        <hr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                            <div class="card-footer">
                                <a href="products.php" class="btn btn-outline-dark">
                                    <i class="bi bi-arrow-left me-1"></i> Continue Shopping
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="fw-bold mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span>Subtotal</span>
                                <span id="cart-subtotal"><?php echo number_format($subtotal, 2); ?> Taka</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Shipping</span>
                                <span id="cart-shipping"><?php echo number_format($shipping, 2); ?> Taka</span>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <span>Tax</span>
                                <span id="cart-tax"><?php echo number_format($tax, 2); ?> Taka</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total</strong>
                                <strong id="cart-total"><?php echo number_format($total, 2); ?> Taka</strong>
                            </div>
                            <a href="checkout.php" class="btn btn-dark w-100 <?php echo empty($_SESSION['cart']) ? 'disabled' : ''; ?>" id="checkout-btn">Proceed to Checkout</a>
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
    <script>
        $(document).ready(function() {
            // Handle increase/decrease quantity
            $('.quantity-btn').on('click', function() {
                const id = $(this).data('id');
                const action = $(this).data('action');
                const quantityInput = $(this).closest('.cart-item').find('.quantity-input');
                let quantity = parseInt(quantityInput.val());
                
                if (action === 'increase') {
                    quantity += 1;
                } else if (action === 'decrease') {
                    quantity = Math.max(1, quantity - 1);
                }
                
                updateQuantity(id, quantity);
            });
            
            // Handle remove item
            $('.remove-item').on('click', function() {
                const id = $(this).data('id');
                removeFromCart(id);
            });
            
            // Handle clear cart
            $('#clear-cart').on('click', function() {
                if (confirm('Are you sure you want to clear your cart?')) {
                    clearCart();
                }
            });
            
            // Function to update quantity
            function updateQuantity(productId, quantity) {
                $.ajax({
                    url: 'cart.php',
                    type: 'POST',
                    data: {
                        action: 'update_quantity',
                        product_id: productId,
                        quantity: quantity
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    }
                });
            }
            
            // Function to remove from cart
            function removeFromCart(productId) {
                $.ajax({
                    url: 'cart.php',
                    type: 'POST',
                    data: {
                        action: 'remove_from_cart',
                        product_id: productId
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    }
                });
            }
            
            // Function to clear cart
            function clearCart() {
                $.ajax({
                    url: 'cart.php',
                    type: 'POST',
                    data: {
                        action: 'clear_cart'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>