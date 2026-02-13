<?php
require('../includes/db.php');
session_start();

// Handle Form Submission
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Check if email already exists
    $check_sql = "SELECT id FROM users WHERE email = '$email'";
    $check_result = mysqli_query($conn, $check_sql);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error = "Email already registered.";
    } else {
        // Hash Password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert Customer
        $sql = "INSERT INTO users (name, email, password, role, phone, address) 
                VALUES ('$name', '$email', '$hashed_password', 'customer', '$phone', '$address')";
        
        if (mysqli_query($conn, $sql)) {
            // Auto Login or Redirect
             $_SESSION['msg'] = "Registration successful! Please login.";
             $_SESSION['msg_type'] = "success";
            header("Location: login.php");
            exit;
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}

$base_url = '..';
include('../includes/header.php');
?>

<main class="container">
    <div class="auth-wrapper">
        <div class="auth-card">
            <h2 class="text-center" style="margin-bottom: 2rem;">Create Account</h2>
            
            <?php if($error): ?>
                <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" class="form-control" required>
                </div>
                 <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
            </form>
            <p class="text-center" style="margin-top: 1rem;">
                Already have an account? <a href="login.php" style="color: var(--primary-color);">Login Here</a>
            </p>
        </div>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
