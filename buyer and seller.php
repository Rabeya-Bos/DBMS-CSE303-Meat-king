<?php
include 'db.php';

// Handle Add
if (isset($_POST['add'])) {
    $stmt = $conn->prepare("INSERT INTO directory (date, name, designation, seller_or_buyer, address, price, product, quantity, livestocks_or_product) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssdiss", $_POST['date'], $_POST['name'], $_POST['designation'], $_POST['seller_or_buyer'], $_POST['address'], $_POST['price'], $_POST['product'], $_POST['quantity'], $_POST['livestocks_or_product']);
    $stmt->execute();
    header('Location: directory.php');
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM directory WHERE id=$id");
    header('Location: directory.php');
}

// Handle Edit
if (isset($_POST['edit'])) {
    $stmt = $conn->prepare("UPDATE directory SET date=?, name=?, designation=?, seller_or_buyer=?, address=?, price=?, product=?, quantity=?, livestocks_or_product=? WHERE id=?");
    $stmt->bind_param("sssssdissi", $_POST['date'], $_POST['name'], $_POST['designation'], $_POST['seller_or_buyer'], $_POST['address'], $_POST['price'], $_POST['product'], $_POST['quantity'], $_POST['livestocks_or_product'], $_POST['id']);
    $stmt->execute();
    header('Location: directory.php');
}

// Fetch all records
$records = $conn->query("SELECT * FROM directory");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Buyer and Seller Directory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-center mb-4">Buyer and Seller Directory</h2>

    <!-- Add/Edit Form -->
    <form method="POST" class="card p-4 mb-4">
        <input type="hidden" name="id" value="<?php echo isset($_GET['edit']) ? $_GET['edit'] : ''; ?>">
        <div class="row mb-2">
            <div class="col"><input type="date" name="date" class="form-control" required></div>
            <div class="col"><input type="text" name="name" class="form-control" placeholder="Name" required></div>
        </div>
        <div class="row mb-2">
            <div class="col">
                <select name="designation" class="form-control" required>
                    <option value="">Select Designation</option>
                    <option value="Consumer">Consumer</option>
                    <option value="Farmer">Farmer</option>
                    <option value="Wholesaler">Wholesaler</option>
                    <option value="Retailer">Retailer</option>
                </select>
            </div>
            <div class="col">
                <select name="seller_or_buyer" class="form-control" required>
                    <option value="">Select Type</option>
                    <option value="Seller">Seller</option>
                    <option value="Buyer">Buyer</option>
                </select>
            </div>
        </div>
        <div class="mb-2"><input type="text" name="address" class="form-control" placeholder="Address" required></div>
        <div class="row mb-2">
            <div class="col"><input type="number" step="0.01" name="price" class="form-control" placeholder="Price" required></div>
            <div class="col"><input type="text" name="product" class="form-control" placeholder="Product" required></div>
        </div>
        <div class="row mb-2">
            <div class="col"><input type="number" name="quantity" class="form-control" placeholder="Quantity" required></div>
            <div class="col">
                <select name="livestocks_or_product" class="form-control" required>
                    <option value="">Select Type</option>
                    <option value="Livestock">Livestock</option>
                    <option value="Product">Product</option>
                </select>
            </div>
        </div>
        <button type="submit" name="<?php echo isset($_GET['edit']) ? 'edit' : 'add'; ?>" class="btn btn-primary w-100">
            <?php echo isset($_GET['edit']) ? 'Update' : 'Add'; ?> Record
        </button>
    </form>

    <!-- Records Table -->
    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <th>Name</th>
                <th>Designation</th>
                <th>Seller/Buyer</th>
                <th>Address</th>
                <th>Price</th>
                <th>Product</th>
                <th>Quantity</th>
                <th>Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $records->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['date'] ?></td>
                <td><?= $row['name'] ?></td>
                <td><?= $row['designation'] ?></td>
                <td><?= $row['seller_or_buyer'] ?></td>
                <td><?= $row['address'] ?></td>
                <td><?= $row['price'] ?></td>
                <td><?= $row['product'] ?></td>
                <td><?= $row['quantity'] ?></td>
                <td><?= $row['livestocks_or_product'] ?></td>
                <td>
                    <a href="directory.php?edit=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="directory.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure to delete?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
