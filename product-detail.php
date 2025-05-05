<?php
require_once 'config.php';

// Check if product ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    error_redirect('Invalid product ID', 'products.php');
}

$productId = intval($_GET['id']);

// Fetch product details
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    error_redirect('Product not found', 'products.php');
}

$product = $result->fetch_assoc();

// Fetch related products (same category but different ID)
$relatedSql = "SELECT * FROM products WHERE category = ? AND id != ? LIMIT 4";
$relatedStmt = $conn->prepare($relatedSql);
$relatedStmt->bind_param("si", $product['category'], $productId);
$relatedStmt->execute();
$relatedResult = $relatedStmt->get_result();
$relatedProducts = [];

if ($relatedResult->num_rows > 0) {
    while($row = $relatedResult->fetch_assoc()) {
        $relatedProducts[] = $row;
    }
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isset($_SESSION['user_id'])) {
        error_redirect('Please login to submit a review', 'login.php');
    }
    
    $rating = intval($_POST['rating']);
    $reviewText = trim($_POST['review_text']);
    $userId = $_SESSION['user_id'];
    
    if ($rating < 1 || $rating > 5) {
        error_redirect('Invalid rating', 'product-detail.php?id=' . $productId);
    }
    
    // Check if user already submitted a review for this product
    $checkSql = "SELECT id FROM reviews WHERE product_id = ? AND user_id = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("ii", $productId, $userId);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        // Update existing review
        $reviewId = $checkResult->fetch_assoc()['id'];
        $updateSql = "UPDATE reviews SET rating = ?, review = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("isi", $rating, $reviewText, $reviewId);
        $updateStmt->execute();
    } else {
        // Insert new review
        $insertSql = "INSERT INTO reviews (product_id, user_id, rating, review) VALUES (?, ?, ?, ?)";
        $insertStmt = $conn->prepare($insertSql);
        $insertStmt->bind_param("iiis", $productId, $userId, $rating, $reviewText);
        $insertStmt->execute();
    }
    
    // Update product's average rating and review count
    $ratingUpdateSql = "UPDATE products SET 
                        rating = (SELECT AVG(rating) FROM reviews WHERE product_id = ?),
                        reviews = (SELECT COUNT(*) FROM reviews WHERE product_id = ?)
                        WHERE id = ?";
    $ratingUpdateStmt = $conn->prepare($ratingUpdateSql);
    $ratingUpdateStmt->bind_param("iii", $productId, $productId, $productId);
    $ratingUpdateStmt->execute();
    
    success_redirect('Review submitted successfully', 'product-detail.php?id=' . $productId);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopease | <?php echo $product['name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/nav.php'; ?>

    <!-- Product Section -->
    <section class="py-5">
        <div class="container px-4 px-lg-5">
            <div class="row gx-4 gx-lg-5">
                <div class="col-md-6">
                    <img class="card-img-top mb-5 mb-md-0 product-detail-img" src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" />
                </div>
                <div class="col-md-6">
                    <div class="small mb-1">SKU: <?php echo $product['id'].$product['id'].$product['id']; ?></div>
                    <h1 class="display-5 fw-bolder"><?php echo $product['name']; ?></h1>
                    <div class="fs-5 mb-2">
                        <span class="product-price"><?php echo number_format($product['price'], 2); ?> Taka</span>
                    </div>
                    <div class="d-flex mb-4">
                        <div class="rating me-2">
                            <?php
                            $fullStars = floor($product['rating']);
                            $halfStar = $product['rating'] - $fullStars >= 0.5;
                            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                            
                            for ($i = 0; $i < $fullStars; $i++) {
                                echo '<i class="bi bi-star-fill"></i>';
                            }
                            if ($halfStar) {
                                echo '<i class="bi bi-star-half"></i>';
                            }
                            for ($i = 0; $i < $emptyStars; $i++) {
                                echo '<i class="bi bi-star"></i>';
                            }
                            ?>
                        </div>
                        <span class="text-muted small">(<?php echo $product['reviews']; ?> reviews)</span>
                    </div>
                    <p class="lead"><?php echo $product['description']; ?></p>
                    <div class="d-flex">
                        <input class="form-control text-center me-3 quantity-selector" id="product-quantity" type="number" value="1" min="1" max="10" />
                        <button class="btn btn-dark flex-shrink-0 add-to-cart" data-id="<?php echo $product['id']; ?>">
                            <i class="bi bi-cart-plus me-1"></i>
                            Add to cart
                        </button>
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- User Rating Section -->
                    <div class="mt-4">
                        <h4>Rate this product</h4>
                        <form method="POST" action="product-detail.php?id=<?php echo $product['id']; ?>">
                            <div class="rating-selector mb-3">
                                <div class="stars">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                    <input class="star star-<?php echo $i; ?>" id="star-<?php echo $i; ?>" type="radio" name="rating" value="<?php echo $i; ?>" required />
                                    <label class="star star-<?php echo $i; ?>" for="star-<?php echo $i; ?>"><i class="bi bi-star-fill"></i></label>
                                    <?php endfor; ?>
                                </div>
                                <span class="ms-2" id="rating-text">Select Rating</span>
                            </div>
                            <div class="mb-3">
                                <textarea class="form-control" id="review-text" name="review_text" rows="3" placeholder="Write your review (optional)"></textarea>
                            </div>
                            <button type="submit" name="submit_review" class="btn btn-outline-dark">Submit Review</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Related Products -->
    <section class="py-5 bg-light">
        <div class="container px-4 px-lg-5">
            <h2 class="fw-bold mb-4">Related Products</h2>
            <div class="row gx-4 gx-lg-5 row-cols-1 row-cols-md-2 row-cols-xl-4" id="related-products">
                <?php if (count($relatedProducts) > 0): ?>
                    <?php foreach($relatedProducts as $relProduct): ?>
                        <div class="col mb-5">
                            <div class="card h-100 product-card">
                                <img class="card-img-top" src="<?php echo $relProduct['image']; ?>" alt="<?php echo $relProduct['name']; ?>" />
                                <div class="card-body p-4">
                                    <div class="text-center">
                                        <h5 class="fw-bolder"><?php echo $relProduct['name']; ?></h5>
                                        <div class="rating mb-2">
                                            <?php
                                            $fullStars = floor($relProduct['rating']);
                                            $halfStar = $relProduct['rating'] - $fullStars >= 0.5;
                                            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                            
                                            for ($i = 0; $i < $fullStars; $i++) {
                                                echo '<i class="bi bi-star-fill"></i>';
                                            }
                                            if ($halfStar) {
                                                echo '<i class="bi bi-star-half"></i>';
                                            }
                                            for ($i = 0; $i < $emptyStars; $i++) {
                                                echo '<i class="bi bi-star"></i>';
                                            }
                                            ?>
                                            <span class="ms-1 text-muted small">(<?php echo $relProduct['reviews']; ?>)</span>
                                        </div>
                                        <span class="product-price"><?php echo number_format($relProduct['price'], 2); ?> Taka per kg</span>
                                    </div>
                                </div>
                                <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                    <div class="text-center">
                                        <button class="btn btn-outline-dark mt-auto add-to-cart" data-id="<?php echo $relProduct['id']; ?>"><i class="bi bi-cart-check-fill"></i></button>
                                        <a class="btn btn-dark mt-auto" href="product-detail.php?id=<?php echo $relProduct['id']; ?>">View details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p>No related products found</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/app.js"></script>
</body>
</html>