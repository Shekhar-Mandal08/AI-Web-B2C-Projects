<?php
require('../includes/db.php');
session_start();

// Handle Form Submission
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Check user in database
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        // Verify Password
        if (password_verify($password, $user['password'])) {
            // Set Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['msg'] = "Welcome back, " . $user['name'] . "!";
            $_SESSION['msg_type'] = "success";

            // Redirect based on role
            if ($user['role'] === 'vendor') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../customer/dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with this email.";
    }
}

$base_url = '..';
include('../includes/header.php');
?>

<main class="container">
    <div class="auth-wrapper">
        <div class="auth-card">
            <h2 class="text-center" style="margin-bottom: 2rem;">Customer Login</h2>
            
            <?php if($error): ?>
                <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            </form>
            <p class="text-center" style="margin-top: 1rem;">
                Don't have an account? <a href="register.php" style="color: var(--primary-color);">Register Here</a>
            </p>
        </div>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
