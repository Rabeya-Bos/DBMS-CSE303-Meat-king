<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: signin.html");
    exit;
}
echo "Welcome, " . $_SESSION['user']['username'] . " (" . $_SESSION['user']['designation'] . ")";
?>

<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: signin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Meat King</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark px-3">
        <span class="navbar-brand">Welcome, <?php echo $_SESSION['username']; ?> (<?php echo $_SESSION['designation']; ?>)</span>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </nav>

    <div class="container mt-5">
        <h1>Dashboard</h1>
        <p>This page is protected and only accessible after login.</p>
    </div>
</body>
</html>
