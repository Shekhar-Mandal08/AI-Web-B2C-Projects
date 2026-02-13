<?php
// Expects $base_url to be set by the calling script, e.g., '.' or '..'
if (!isset($base_url)) {
    $base_url = '.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nepal Milk Dairy</title>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Location Tracker -->
    <script src="<?php echo $base_url; ?>/js/tracker.js"></script>
</head>
<body>

<?php
// Display Toast Messages if they exist in session
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    $type = isset($_SESSION['msg_type']) ? $_SESSION['msg_type'] : 'success';
    echo "
    <script>
        Swal.fire({
            icon: '$type',
            title: '$msg',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    </script>
    ";
    unset($_SESSION['msg']);
    unset($_SESSION['msg_type']);
}
?>

<!-- Header / Navigation -->
<header>
    <div class="container navbar">
        <div class="logo">
            <a href="<?php echo $base_url; ?>/index.php">Nepal Milk Dairy</a>
        </div>
        <div class="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
        <nav>
            <ul class="nav-links">
                <li><a href="<?php echo $base_url; ?>/index.php">Home</a></li>
                <li><a href="<?php echo $base_url; ?>/public/products.php">Products</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] === 'vendor'): ?>
                        <li><a href="<?php echo $base_url; ?>/admin/dashboard.php">Vendor Panel</a></li>
                    <?php else: ?>
                         <li><a href="<?php echo $base_url; ?>/customer/dashboard.php" class="btn btn-sm btn-outline">My Account</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo $base_url; ?>/public/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?php echo $base_url; ?>/public/login.php">Login</a></li>
                    <li><a href="<?php echo $base_url; ?>/public/register.php">Register</a></li>
                    <li><a href="<?php echo $base_url; ?>/admin/login.php">Vendor</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>
