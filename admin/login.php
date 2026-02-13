<?php
require('../includes/db.php');
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Check user in database
    $sql = "SELECT * FROM users WHERE email = '$email' AND role = 'vendor'";
    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Access Denied. You are not a registered vendor.";
    }
}

$base_url = '..';
include('../includes/header.php');
?>

<main class="container">
    <div class="auth-wrapper">
        <div class="auth-card" style="border-top: 5px solid var(--logo-color);">
            <h2 class="text-center" style="margin-bottom: 2rem; color: var(--logo-color);">Vendor Login</h2>
            
            <?php if($error): ?>
                <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="form-group">
                    <label for="email">Vendor Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%; background-color: var(--logo-color);">Login to Dashboard</button>
            </form>
             <p class="text-center" style="margin-top: 1rem;">
                <a href="../public/index.php" style="color: var(--text-secondary);">Back to Home</a>
            </p>
        </div>
    </div>
</main>

<?php include('../includes/footer.php'); ?>
