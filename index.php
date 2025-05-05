<?php
require_once 'config.php';

// Fetch featured products
$sql = "SELECT * FROM products ORDER BY rating DESC LIMIT 4";
$result = $conn->query($sql);
$featuredProducts = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $featuredProducts[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meat Item | Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <!-- Navigation -->
    <?php include 'includes/nav.php'; ?>

    <!-- Hero Section -->
    <header class="py-5 bg-light">
        <div class="container px-4 px-lg-5">
            <div class="row gx-4 gx-lg-5 align-items-center">
                <div class="col-md-6">
                    <h1 class="display-5 fw-bold">Shop with Easy</h1>
                    <p class="lead">Discover quality products at affordable prices. Your one-stop shop for all your needs.</p>
                    <a class="btn btn-dark" href="products.php">Shop Now</a>
                </div>
                <div class="col-md-6">
                    <img class="img-fluid rounded" src="https://placehold.co/600x400?text=Shop+Now" alt="Shop">
                </div>
            </div>
        </div>
    </header>

    <!-- Featured Products -->
    <section class="py-5">
        <div class="container px-4 px-lg-5">
            <h2 class="fw-bold mb-4">Featured Products</h2>
            <div class="row gx-4 gx-lg-5 row-cols-1 row-cols-md-2 row-cols-xl-4" id="featured-products">
                <?php foreach($featuredProducts as $product): ?>
                    <div class="col mb-5">
                        <div class="card h-100 product-card">
                            <img class="card-img-top" src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" />
                            <div class="card-body p-4">
                                <div class="text-center">
                                    <h5 class="fw-bolder"><?php echo $product['name']; ?></h5>
                                    <div class="rating mb-2">
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
                                        <span class="ms-1 text-muted small">(<?php echo $product['reviews']; ?>)</span>
                                    </div>
                                    <span class="product-price"><?php echo number_format($product['price'], 2); ?> Taka per kg</span>
                                </div>
                            </div>
                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent">
                                <div class="text-center">
                                    <button class="btn btn-outline-dark mt-auto add-to-cart" data-id="<?php echo $product['id']; ?>"><i class="bi bi-cart-check-fill"></i></button>
                                    <a class="btn btn-dark mt-auto" href="product-detail.php?id=<?php echo $product['id']; ?>">View details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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