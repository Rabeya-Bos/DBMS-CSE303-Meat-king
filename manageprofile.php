<?php
require_once 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Fetch user details
$userSql = "SELECT * FROM users WHERE id = ?";
$userStmt = $conn->prepare($userSql);
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userResult = $userStmt->get_result();
$user = $userResult->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Update name and phone
    if (!empty($name)) {
        $updateSql = "UPDATE users SET name = ?, phone = ? WHERE id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ssi", $name, $phone, $userId);
        $updateStmt->execute();
        
        $_SESSION['user_name'] = $name;
        $_SESSION['success'] = 'Profile updated successfully!';
    }
    
    // Update password if provided
    if (!empty($currentPassword) && !empty($newPassword)) {
        if ($newPassword !== $confirmPassword) {
            $_SESSION['error'] = 'New passwords do not match';
        } else if (!password_verify($currentPassword, $user['password'])) {
            $_SESSION['error'] = 'Current password is incorrect';
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $passwordSql = "UPDATE users SET password = ? WHERE id = ?";
            $passwordStmt = $conn->prepare($passwordSql);
            $passwordStmt->bind_param("si", $hashedPassword, $userId);
            $passwordStmt->execute();
            
            $_SESSION['success'] = 'Password updated successfully!';
        }
    }
    
    header('Location: profile.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopease | My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #f8f9fa;
            border-right: 1px solid #e9ecef;
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: #495057;
            border-radius: 0;
            padding: 0.8rem 1rem;
        }
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
        }
        .sidebar .nav-link.active {
            background-color: #212529;
            color: #fff;
        }
        .sidebar .nav-link i {
            margin-right: 10px;
        }
        .main-content {
            padding: 20px;
        }
        @media (max-width: 767.98px) {
            .sidebar {
                min-height: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Main Navigation -->
    <?php include 'includes/nav.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="text-center mb-4">
                    <div class="mb-2">
                        <i class="bi bi-person-circle" style="font-size: 3rem;"></i>
                    </div>
                    <h5><?php echo htmlspecialchars($user['name']); ?></h5>
                    <p class="text-muted small"><?php echo htmlspecialchars($user['email']); ?></p>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="manageprofile.php">
                            <i class="bi bi-person"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">
                            <i class="bi bi-bag"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item mt-3">
                        <a class="nav-link text-danger" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Page header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h2">My Profile</h1>
                        <p class="text-muted">Manage your account information</p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Personal Information</h5>
                            </div>
                            <div class="card-body">
                                <form id="profile-form" method="POST" action="profile.php">
                                    <div class="mb-3">
                                        <label for="profileName" class="form-label">Full Name</label>
                                        <input type="text" class="form-control" id="profileName" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="profileEmail" class="form-label">Email Address</label>
                                        <input type="email" class="form-control" id="profileEmail" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                                        <div class="form-text">Email address cannot be changed</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="profilePhone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" id="profilePhone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                                    </div>
                                    <button type="submit" name="update_profile" class="btn btn-dark">Update Profile</button>
                                </form>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Change Password</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="profile.php">
                                    <div class="mb-3">
                                        <label for="currentPassword" class="form-label">Current Password</label>
                                        <input type="password" class="form-control" id="currentPassword" name="current_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="newPassword" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="newPassword" name="new_password" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                                    </div>
                                    <button type="submit" name="update_profile" class="btn btn-dark">Change Password</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Account Information</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Member Since:</strong> <?php echo date('F d, Y', strtotime($user['created_at'] ?? date('Y-m-d'))); ?></p>
                                <p><strong>Last Login:</strong> <?php echo date('F d, Y', strtotime($user['last_login'] ?? date('Y-m-d'))); ?></p>
                                <p><strong>Account Status:</strong> <span class="badge bg-success">Active</span></p>
                                
                                <hr>
                                
                                <div class="d-grid gap-2">
                                    <a href="orders.php" class="btn btn-outline-dark">View Order History</a>
                                    <a href="addresses.php" class="btn btn-outline-dark">Manage Addresses</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>