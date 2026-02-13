<?php
require('../includes/db.php');
session_start();

// Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: login.php");
    exit;
}

$vendor_id = $_SESSION['user_id'];

// Fetch Stats
// 1. Total Products
$prod_sql = "SELECT COUNT(*) as count FROM products WHERE vendor_id = '$vendor_id'";
$prod_res = mysqli_fetch_assoc(mysqli_query($conn, $prod_sql));
$total_products = $prod_res['count'];

// 2. Total Orders (Items sold by this vendor)
// We need to join order_items -> products (filtered by vendor) -> orders
$order_sql = "SELECT COUNT(DISTINCT oi.order_id) as count 
              FROM order_items oi
              JOIN products p ON oi.product_id = p.id
              WHERE p.vendor_id = '$vendor_id'";
$order_res = mysqli_fetch_assoc(mysqli_query($conn, $order_sql));
$total_orders = $order_res['count'];

// 3. Total Earnings
$earn_sql = "SELECT SUM(oi.quantity * oi.price) as earnings 
             FROM order_items oi
             JOIN products p ON oi.product_id = p.id
             WHERE p.vendor_id = '$vendor_id'";
$earn_res = mysqli_fetch_assoc(mysqli_query($conn, $earn_sql));
$total_earnings = $earn_res['earnings'] ? $earn_res['earnings'] : 0;

// 4. Fetch Customers with Location Data (Last logged/cart activity)
$loc_sql = "SELECT DISTINCT u.name, u.email, u.latitude, u.longitude, u.address 
            FROM users u 
            WHERE u.latitude IS NOT NULL AND u.role = 'customer' 
            LIMIT 5";
$loc_res = mysqli_query($conn, $loc_sql);

$base_url = '..';
include('../includes/header.php');
?>

<div class="container dashboard-layout">
    
    <!-- Sidebar -->
    <aside class="sidebar">
        <h3 style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(255,255,255,0.1);">Vendor Panel</h3>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active">Overview</a></li>
            <li><a href="products.php">My Products</a></li>
            <li><a href="add_product.php">Add New Product</a></li>
            <li><a href="orders.php">Manage Orders</a></li>
            <li><a href="reports.php">Reports</a></li>
            <li><a href="../public/logout.php">Logout</a></li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="dashboard-content">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></h2>
        <p>Here is your daily performance overview.</p>

        <div class="stat-cards">
            <div class="stat-card">
                <h3>Products</h3>
                <div class="stat-number"><?php echo $total_products; ?></div>
                <p>Live in Store</p>
                <a href="products.php" class="btn btn-sm btn-outline">Manage</a>
            </div>
            <div class="stat-card">
                <h3>Orders</h3>
                <div class="stat-number"><?php echo $total_orders; ?></div>
                <p>Pending / Completed</p>
                <a href="orders.php" class="btn btn-sm btn-outline">View</a>
            </div>
            <div class="stat-card">
                <h3>Earnings</h3>
                <div class="stat-number">Rs. <?php echo number_format($total_earnings, 0); ?></div>
                <p>Total Revenue</p>
            </div>
        </div>

        <!-- Recent Activity / Low Stock Warning could go here -->
        <!-- Customer Tracking Section -->
        <div class="card" style="margin-top: 2rem;">
            <h3>Customer Tracking (Last Known Locations)</h3>
            <p style="font-size: 0.9rem; color: #666; margin-bottom: 1rem;">Tracking locations of customers when they add items to cart or place orders.</p>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Customer Name</th>
                            <th>Email</th>
                            <th>Last Known Location</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($loc_res) > 0): ?>
                            <?php while($user = mysqli_fetch_assoc($loc_res)): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <a href="https://www.google.com/maps?q=<?php echo $user['latitude']; ?>,<?php echo $user['longitude']; ?>" target="_blank" class="btn btn-sm btn-outline">
                                            <i class="fas fa-map-marker-alt"></i> View on Map
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">No location tracking data available yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

         <div class="card" style="margin-top: 2rem;">
            <h3>Quick Actions</h3>
            <div style="display:flex; gap: 1rem; margin-top: 1rem;">
                <a href="add_product.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Product</a>
                <a href="orders.php" class="btn btn-outline"><i class="fas fa-list"></i> View Orders</a>
            </div>
        </div>

    </main>
</div>

<?php include('../includes/footer.php'); ?>
