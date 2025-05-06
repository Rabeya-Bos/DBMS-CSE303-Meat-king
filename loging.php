<?php
session_start();
include 'config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle Sign Up
    if (isset($_POST['signup'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $designation = $_POST['designation'];

        $stmt = $conn->prepare("INSERT INTO singing (username, email, password, designation) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $password, $designation);

        if ($stmt->execute()) {
            $error = "Signup successful. You can now log in.";
        } else {
            $error = "Signup failed: " . $stmt->error;
        }
        $stmt->close();
    }

    // Handle Sign In
    if (isset($_POST['signin'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $designation = $_POST['designation'];

        // Check if the user exists in the singing table first
        $stmt = $conn->prepare("SELECT id, username, password, designation FROM singing WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password']) && $user['designation'] === $designation) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['designation'] = $user['designation'];

                // Redirect based on designation
                switch ($user['designation']) {
                    case 'admin':
                        header("Location: admin.php");
                        break;
                    case 'retailer':
                        header("Location: retailer.php");
                        break;
                    case 'livestock farmer':
                        header("Location: farmer_dashboard.php");
                        break;
                    case 'wholesaler':
                        header("Location: wholesaler.php");
                        break;
                    case 'coldstorage manager':
                        header("Location: coldstorage_d.php");
                        break;
                    case 'nutritionist':
                        header("Location: nutritionist.php");
                        break;
                    case 'consumer':
                        header("Location: consumer.php");
                        break;
                    default:
                        $error = "Unknown designation.";
                }
                exit;
            } else {
                $error = "Invalid email/password/designation combination.";
            }
        } else {
            // Check if the user exists in the admin_t table if they are an admin
            $stmt = $conn->prepare("SELECT id, email, password, name, role FROM admin_t WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($admin = $result->fetch_assoc()) {
                if (password_verify($password, $admin['password'])) {
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_name'] = $admin['name'];
                    $_SESSION['admin_role'] = $admin['role'];

                    // Redirect to the admin dashboard
                    header("Location: admin_dashboard.php");
                    exit;
                } else {
                    $error = "Invalid email/password combination for admin.";
                }
            } else {
                $error = "Admin not found.";
            }
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>MEAT KING - Sign In / Sign Up</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        body {
            background: #1e1e2f;
            color: #fff;
            font-family: 'Roboto', sans-serif;
        }
        .form-container {
            max-width: 500px;
            margin: 5% auto;
            background: #2e2e3e;
            padding: 2rem;
            border-radius: 12px;
        }
        .toggle-link {
            color: #dc3545;
            cursor: pointer;
        }
        .form-floating > label {
            color: #ccc;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h3 class="text-center text-danger mb-3">🥩 MEAT KING</h3>
        <p class="text-danger text-center"><?= $error ?></p>

        <!-- Sign In Form -->
        <form method="POST" id="signinForm">
            <h4 class="mb-3">Sign In</h4>
            <div class="form-floating mb-3">
                <input type="email" name="email" class="form-control" id="signinEmail" placeholder="name@example.com" required>
                <label for="signinEmail">Email address</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" name="password" class="form-control" id="signinPassword" placeholder="Password" required>
                <label for="signinPassword">Password</label>
            </div>
            <div class="form-floating mb-3">
                <select name="designation" class="form-select" id="signinDesignation" required>
                    <option disabled selected>Select your designation</option>
                    <option value="admin">Admin</option>
                    <option value="retailer">Retailer</option>
                    <option value="livestock farmer">Livestock Farmer</option>
                    <option value="wholesaler">Wholesaler</option>
                    <option value="coldstorage manager">Cold Storage Manager</option>
                    <option value="nutritionist">Nutritionist</option>
                    <option value="consumer">Consumer</option>
                </select>
                <label for="signinDesignation">Designation</label>
            </div>
            <button type="submit" name="signin" class="btn btn-danger w-100">Sign In</button>
            <p class="mt-3 text-center">Don't have an account? <span class="toggle-link" onclick="toggleForms()">Sign Up</span></p>
        </form>

        <!-- Sign Up Form -->
        <form method="POST" id="signupForm" style="display: none;">
            <h4 class="mb-3">Sign Up</h4>
            <div class="form-floating mb-3">
                <input type="text" name="username" class="form-control" id="signupUsername" placeholder="Username" required>
                <label for="signupUsername">Username</label>
            </div>
            <div class="form-floating mb-3">
                <input type="email" name="email" class="form-control" id="signupEmail" placeholder="name@example.com" required>
                <label for="signupEmail">Email address</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" name="password" class="form-control" id="signupPassword" placeholder="Password" required>
                <label for="signupPassword">Password</label>
            </div>
            <div class="form-floating mb-3">
                <select name="designation" class="form-select" id="signupDesignation" required>
                    <option disabled selected>Select your designation</option>
                    <option value="admin">Admin</option>
                    <option value="retailer">Retailer</option>
                    <option value="livestock farmer">Livestock Farmer</option>
                    <option value="wholesaler">Wholesaler</option>
                    <option value="coldstorage manager">Cold Storage Manager</option>
                    <option value="nutritionist">Nutritionist</option>
                    <option value="consumer">Consumer</option>
                </select>
                <label for="signupDesignation">Designation</label>
            </div>
            <button type="submit" name="signup" class="btn btn-danger w-100">Sign Up</button>
            <p class="mt-3 text-center">Already have an account? <span class="toggle-link" onclick="toggleForms()">Sign In</span></p>
        </form>
    </div>

    <script>
        function toggleForms() {
            const signinForm = document.getElementById("signinForm");
            const signupForm = document.getElementById("signupForm");
            signinForm.style.display = signinForm.style.display === "none" ? "block" : "none";
            signupForm.style.display = signupForm.style.display === "none" ? "block" : "none";
        }
    </script>
</body>
</html>
