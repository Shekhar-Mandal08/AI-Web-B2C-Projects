<?php
require('includes/db.php');
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nepal Milk Dairy - Fresh & Organic</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

    <!-- Header / Navigation -->
    <header>
        <div class="container navbar">
            <div class="logo">
                <a href="index.php">Nepal Milk Dairy</a>
            </div>
            <div class="menu-toggle">
                <i class="fas fa-bars"></i>
            </div>
            <nav>
                <ul class="nav-links">
                    <li><a href="index.php" class="active">Home</a></li>
                    <li><a href="public/products.php">Products</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['role'] === 'vendor'): ?>
                            <li><a href="admin/dashboard.php">Vendor Panel</a></li>
                        <?php else: ?>
                             <li><a href="customer/dashboard.php" class="btn btn-sm btn-outline">My Account</a></li>
                        <?php endif; ?>
                        <li><a href="public/logout.php">Logout</a></li>
                    <?php else: ?>
                        <li><a href="public/login.php">Login</a></li>
                        <li><a href="public/register.php">Register</a></li>
                        <li><a href="admin/login.php">Admin</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Freshness You Can Taste</h1>
            <p>Experience the purest organic milk and dairy products delivered straight from the farm to your doorstep
                in Nepal.</p>
            <div class="hero-buttons">
                <a href="public/products.php" class="btn btn-primary">Shop Now</a>
                <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="public/register.php" class="btn btn-outline" style="color:white; border-color:white;">Join Us</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Main Content: Featured -->
    <main class="container">

        <section class="featured-products">
            <h2 class="section-title">Featured Products</h2>
            <div class="products-grid">

                <?php
                // Fetch 4 products for display
                $sql = "SELECT * FROM products LIMIT 4";
                $result = mysqli_query($conn, $sql);

                // AUTO-FIX: If no products, attempt to seed them
                if (!$result || mysqli_num_rows($result) == 0) {
                    
                     // Ensure Vendor Exists (using 'users' table as per database.sql)
                    $vendor_email = 'vendor@nepalmilkdairy.com';
                    $check_vendor = mysqli_query($conn, "SELECT id FROM users WHERE email = '$vendor_email'");
                    
                    $vendor_id = 0;
                    if (mysqli_num_rows($check_vendor) > 0) {
                        $vendor_row = mysqli_fetch_assoc($check_vendor);
                        $vendor_id = $vendor_row['id'];
                    } else {
                        // Create a default vendor if missing
                        $hashed = password_hash('vendor123', PASSWORD_DEFAULT); 
                        // Note: Ensure name, email, password, role are correct columns
                        $insert_vendor_sql = "INSERT INTO users (name, email, password, role, phone, address) 
                                              VALUES ('Main Vendor', '$vendor_email', '$hashed', 'vendor', '9800000000', 'Kathmandu')";
                        if(mysqli_query($conn, $insert_vendor_sql)){
                             $vendor_id = mysqli_insert_id($conn);
                        }
                    }

                    // Seed Products if we have a vendor
                    if ($vendor_id) {
                        $products = [
                            ['Organic Whole Milk', 'Fresh organic whole milk from happy cows.', 120.00, 'milk.jpg', 'Milk', 50],
                            ['Low Fat Milk', 'Healthy low fat milk for diet conscious people.', 110.00, 'lowfat.jpg', 'Milk', 45],
                            ['Fresh Yogurt', 'Creamy and thick fresh yogurt.', 80.00, 'yogurt.jpg', 'Dairy', 30],
                            ['Butter', 'Pure homemade style butter.', 500.00, 'butter.jpg', 'Dairy', 20]
                        ];
                        foreach ($products as $prod) {
                             $name = $prod[0]; $desc = $prod[1]; $price = $prod[2]; $img = $prod[3]; $cat = $prod[4]; $stock = $prod[5];
                             
                             // Check if product already exists to avoid duplicates if re-running
                             $check_prod = mysqli_query($conn, "SELECT id FROM products WHERE name='$name'");
                             if(mysqli_num_rows($check_prod) == 0) {
                                  mysqli_query($conn, "INSERT INTO products (vendor_id, name, description, price, image, category, stock) 
                                                  VALUES ('$vendor_id', '$name', '$desc', '$price', '$img', '$cat', '$stock')");
                             }
                        }
                    }
                    
                    // Re-fetch after seeding
                    $result = mysqli_query($conn, "SELECT * FROM products LIMIT 4");
                }

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Fallback image logic if file check is complex mostly implies checking if file exists
                        // For now we just use the name from DB. 
                        // Note: User can add images manually later.
                        $imagePath = 'images/' . ($row['image'] ? $row['image'] : 'milk.jpg');
                        ?>
                        <div class="product-card">
                            <div class="product-image">
                                <!-- Ideally, check if file exists, if not use a placeholder -->
                                <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>"
                                    style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://via.placeholder.com/250?text=No+Image'">
                            </div>
                            <div class="product-info">
                                <span class="product-category"><?php echo htmlspecialchars($row['category']); ?></span>
                                <h3 class="product-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                                <div class="product-price">Rs. <?php echo htmlspecialchars($row['price']); ?></div>
                                <div class="product-btn">
                                    <form action="public/cart.php" method="POST">
                                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="action" value="add">
                                        <button type="submit" class="btn btn-sm btn-primary">Add to Cart</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>No products available at the moment. (System initialization failed)</p>";
                }
                ?>

            </div>

            <div class="text-center">
                <a href="public/products.php" class="btn btn-outline"
                    style="color:var(--primary-color); border-color:var(--primary-color);">View All Products</a>
            </div>

        </section>

        <!-- About / Info Section -->
        <section class="about-section"
            style="margin: 4rem 0; padding: 2rem; background: var(--white); border-radius: 8px;">
            <div class="row" style="display:flex; align-items:center; gap: 2rem; flex-wrap: wrap;">
                <div class="col" style="flex:1; min-width: 300px;">
                    <h2>Why Choose Nepal Milk Dairy?</h2>
                    <p>We are dedicated to providing the highest quality dairy products. Our milk is sourced from local
                        farmers who treat their cattle with care.</p>
                    <ul style="margin-left: 1.5rem; list-style-type: disc; color: var(--text-secondary);">
                        <li>100% Organic & Fresh</li>
                        <li>No Preservatives Added</li>
                        <li>Support Local Farmers</li>
                        <li>Hygienic Processing</li>
                    </ul>
                </div>
                <div class="col" style="flex:1; min-width: 300px; text-align: center;">
                    <i class="fas fa-cow fa-5x" style="color: var(--primary-color); opacity: 0.2;"></i>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer>
        <div class="container footer-content">
            <div class="social-links" style="margin-bottom: 1rem;">
                <a href="#" style="margin: 0 10px;"><i class="fab fa-facebook"></i></a>
                <a href="#" style="margin: 0 10px;"><i class="fab fa-instagram"></i></a>
                <a href="#" style="margin: 0 10px;"><i class="fab fa-twitter"></i></a>
            </div>
            <p>&copy; 2026 Nepal Milk Dairy. Milk_Group.</p>
            <p>Pure Goodness in Every Drop</p>
            <p>Contact: shekhar@nepalmilkdairy.com | +977-9767754238</p>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>

</html>
