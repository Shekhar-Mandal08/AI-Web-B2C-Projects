<?php
require('../includes/db.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: login.php");
    exit;
}

$vendor_id = $_SESSION['user_id'];
$base_url = '..';
include('../includes/header.php');

// Helper function to get sales
function get_sales_report($conn, $vendor_id, $group_by, $limit) {
    $format = "";
    if($group_by == 'daily') $format = "%Y-%m-%d";
    elseif($group_by == 'monthly') $format = "%Y-%m";
    elseif($group_by == 'weekly') $format = "%Y Week %u";
    
    $sql = "SELECT DATE_FORMAT(o.order_date, '$format') as period, 
                   COUNT(DISTINCT o.id) as total_orders,
                   SUM(oi.quantity) as items_sold,
                   SUM(oi.quantity * oi.price) as revenue
            FROM order_items oi
            JOIN orders o ON oi.order_id = o.id
            JOIN products p ON oi.product_id = p.id
            WHERE p.vendor_id = '$vendor_id'
            GROUP BY period
            ORDER BY period DESC
            LIMIT $limit";
    return mysqli_query($conn, $sql);
}

$daily = get_sales_report($conn, $vendor_id, 'daily', 7);
$monthly = get_sales_report($conn, $vendor_id, 'monthly', 12);

?>

<div class="container dashboard-layout">
    <aside class="sidebar">
        <h3 style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(255,255,255,0.1);">Vendor Panel</h3>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Overview</a></li>
            <li><a href="products.php">My Products</a></li>
            <li><a href="add_product.php">Add New Product</a></li>
            <li><a href="orders.php">Manage Orders</a></li>
            <li><a href="reports.php" class="active">Reports</a></li>
            <li><a href="../public/logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="dashboard-content">
        <h2>Sales Reports</h2>
        
        <div class="card" style="margin-bottom: 2rem;">
            <h3>Daily Sales (Last 7 Days)</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Orders</th>
                            <th>Items Sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($daily)): ?>
                            <tr>
                                <td><?php echo $row['period']; ?></td>
                                <td><?php echo $row['total_orders']; ?></td>
                                <td><?php echo $row['items_sold']; ?></td>
                                <td>Rs. <?php echo number_format($row['revenue'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h3>Monthly Sales (Last 1 Year)</h3>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Orders</th>
                            <th>Items Sold</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($monthly)): ?>
                            <tr>
                                <td><?php echo $row['period']; ?></td>
                                <td><?php echo $row['total_orders']; ?></td>
                                <td><?php echo $row['items_sold']; ?></td>
                                <td>Rs. <?php echo number_format($row['revenue'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<?php include('../includes/footer.php'); ?>
