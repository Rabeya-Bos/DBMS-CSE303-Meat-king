<?php
require_once 'config.php';

$category = isset($_GET['category']) ? $_GET['category'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'rating-desc';

// Build the SQL query based on filters
$sql = "SELECT * FROM products WHERE 1=1";

if ($category !== 'all') {
    $sql .= " AND category = '" . $conn->real_escape_string($category) . "'";
}

if (!empty($search)) {
    $sql .= " AND (name LIKE '%" . $conn->real_escape_string($search) . "%' OR 
                   description LIKE '%" . $conn->real_escape_string($search) . "%' OR
                   category LIKE '%" . $conn->real_escape_string($search) . "%')";
}

// Add sorting
switch ($sort) {
    case 'price-asc':
        $sql .= " ORDER BY price ASC";
        break;
    case 'price-desc':
        $sql .= " ORDER BY price DESC";
        break;
    case 'name-asc':
        $sql .= " ORDER BY name ASC";
        break;
    case 'rating-desc':
    default:
        $sql .= " ORDER BY rating DESC";
        break;
}

$result = $conn->query($sql);
$products = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Get unique categories for the filter
$categorySql = "SELECT DISTINCT category FROM products";
$categoryResult = $conn->query($categorySql);
$categories = [];
if ($categoryResult->num_rows > 0) {
    while($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row['category'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopease | Products</title>
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
                <h1 class="fw-bold">Our Products</h1>
                <p class="lead">Browse our collection of quality products</p>
            </div>
        </div>
    </header>

    <!-- Product Filter and Search -->
    <section class="py-3">
        <div class="container px-4 px-lg-5">
            <form method="GET" action="products.php">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
                            <button class="btn btn-dark" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" name="category" onchange="this.form.submit()">
                            <option value="all" <?php echo $category === 'all' ? 'selected' : ''; ?>>All Categories</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat; ?>" <?php echo $category === $cat ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" name="sort" onchange="this.form.submit()">
                            <option value="price-asc" <?php echo $sort === 'price-asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                            <option value="price-desc" <?php echo $sort === 'price-desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                            <option value="rating-desc" <?php echo $sort === 'rating-desc' ? 'selected' : ''; ?>>Top Rated</option>
                            <option value="name-asc" <?php echo $sort === 'name-asc' ? 'selected' : ''; ?>>Name: A to Z</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Product Listing -->
    <section class="py-5">
        <div class="container px-4 px-lg-5">
            <div class="row gx-4 gx-lg-5 row-cols-1 row-cols-md-2 row-cols-xl-4" id="product-list">
                <?php if (count($products) > 0): ?>
                    <?php foreach($products as $product): ?>
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
                <?php else: ?>
                    <div class="col-12 text-center py-5">
                        <h3>No products found</h3>
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