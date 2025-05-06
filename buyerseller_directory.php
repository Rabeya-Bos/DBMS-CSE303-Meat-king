<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "mking";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission (Add or Update)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $edit_id = $_POST['edit_id'] ?? '';
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $person_name = $_POST['person_name'];
    $type = $_POST['type'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $review = $_POST['review'];

    if ($edit_id) {
        $stmt = $conn->prepare("UPDATE product_transactions SET product_id=?, product_name=?, person_name=?, type=?, quantity=?, price=?, review=? WHERE id=?");
        $stmt->bind_param("ssssidsi", $product_id, $product_name, $person_name, $type, $quantity, $price, $review, $edit_id);
        $stmt->execute();
    } else {
        $stmt = $conn->prepare("INSERT INTO product_transactions (product_id, product_name, person_name, type, quantity, price, review) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssids", $product_id, $product_name, $person_name, $type, $quantity, $price, $review);
        $stmt->execute();
    }

    header("Location: buyerseller_directory.php");
    exit;
}

// Handle delete
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $conn->query("DELETE FROM product_transactions WHERE id = $delete_id");
    header("Location: buyerseller_directory.php");
    exit;
}

// Fetch edit data
$edit_id = $_GET['edit_id'] ?? '';
$product_id = $product_name = $person_name = $type = $quantity = $price = $review = '';

if ($edit_id) {
    $result = $conn->query("SELECT * FROM product_transactions WHERE id = $edit_id");
    if ($row = $result->fetch_assoc()) {
        $product_id = $row['product_id'];
        $product_name = $row['product_name'];
        $person_name = $row['person_name'];
        $type = $row['type'];
        $quantity = $row['quantity'];
        $price = $row['price'];
        $review = $row['review'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Buyer and Seller Directory</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #121212;
            color: #fff;
        }
        h2 {
            color: #f39c12;
            font-family: 'Arial', sans-serif;
        }
        .form-control, .btn {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .form-control:focus {
            border-color: #f39c12;
            box-shadow: 0 0 0 0.25rem rgba(243, 156, 18, 0.25);
        }
        .btn-primary {
            background-color: #f39c12;
            border-color: #e67e22;
        }
        .btn-primary:hover {
            background-color: #e67e22;
            border-color: #d35400;
        }
        .table {
            background-color: #2c3e50;
            color: #ecf0f1;
        }
        .table th, .table td {
            border-color: #34495e;
        }
        .table-hover tbody tr:hover {
            background-color: #34495e;
        }
        .table-dark {
            background-color: #1c2833;
        }
    </style>
</head>
<body class="container py-4">

    <h2 class="mb-4 text-center">Buyer and Seller Directory</h2>

    <!-- Form -->
    <form method="POST" action="" class="mb-4">
        <input type="hidden" name="edit_id" value="<?= $edit_id ?>">

        <div class="mb-3">
            <label for="product_id" class="form-label">Product ID</label>
            <input type="text" id="product_id" name="product_id" class="form-control" required value="<?= htmlspecialchars($product_id) ?>">
        </div>

        <div class="mb-3">
            <label for="product_name" class="form-label">Product Name</label>
            <input type="text" id="product_name" name="product_name" class="form-control" required value="<?= htmlspecialchars($product_name) ?>">
        </div>

        <div class="mb-3">
            <label for="person_name" class="form-label">Customer</label>
            <input type="text" id="person_name" name="person_name" class="form-control" required value="<?= htmlspecialchars($person_name) ?>">
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select name="type" id="type" class="form-control" required>
                <option value="">Select Type</option>
                <option value="Buyer" <?= ($type == 'Buyer') ? 'selected' : '' ?>>Buyer</option>
                <option value="Seller" <?= ($type == 'Seller') ? 'selected' : '' ?>>Seller</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" id="quantity" name="quantity" class="form-control" required value="<?= htmlspecialchars($quantity) ?>">
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price</label>
            <input type="number" step="0.01" id="price" name="price" class="form-control" required value="<?= htmlspecialchars($price) ?>">
        </div>

        <div class="mb-3">
            <label for="review" class="form-label">Address</label>
            <input type="text" id="review" name="review" class="form-control" value="<?= htmlspecialchars($review) ?>">
        </div>

        <button type="submit" class="btn btn-primary w-100"><?= $edit_id ? 'Update' : 'Add' ?> Entry</button>
    </form>

    <!-- Table -->
    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>Customer</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM product_transactions ORDER BY id DESC");
            while ($row = $result->fetch_assoc()) {
            ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['product_id']) ?></td>
                    <td><?= htmlspecialchars($row['product_name']) ?></td>
                    <td><?= htmlspecialchars($row['person_name']) ?></td>
                    <td><?= htmlspecialchars($row['type']) ?></td>
                    <td><?= htmlspecialchars($row['quantity']) ?></td>
                    <td><?= htmlspecialchars($row['price']) ?></td>
                    <td><?= htmlspecialchars($row['review']) ?></td>
                    <td>
                        <a href="?edit_id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</body>
</html>
