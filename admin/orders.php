<?php
require('../includes/db.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: login.php");
    exit;
}

$vendor_id = $_SESSION['user_id'];
$message = '';

// Handle Status Update
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    
    // Security check: Ensure this order contains vendor's product
    $check_sql = "SELECT o.id FROM orders o 
                  JOIN order_items oi ON o.id = oi.order_id 
                  JOIN products p ON oi.product_id = p.id 
                  WHERE o.id = '$order_id' AND p.vendor_id = '$vendor_id'";
    if (mysqli_num_rows(mysqli_query($conn, $check_sql)) > 0) {
        mysqli_query($conn, "UPDATE orders SET status = '$new_status' WHERE id = '$order_id'");
        $message = "Order #$order_id status updated to $new_status.";
    } else {
        $message = "Error: Permission denied.";
    }
}

// Fetch Orders
$sql = "SELECT o.id as order_id, o.order_date, o.status, o.total_amount, u.name as customer_name, u.address as shipping_address, o.latitude, o.longitude
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE p.vendor_id = '$vendor_id'
        GROUP BY o.id
        ORDER BY o.order_date DESC";
$result = mysqli_query($conn, $sql);

$base_url = '..';
include('../includes/header.php');
?>

<div class="container dashboard-layout">
    <aside class="sidebar">
        <h3 style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(255,255,255,0.1);">Vendor Panel</h3>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Overview</a></li>
            <li><a href="products.php">My Products</a></li>
            <li><a href="add_product.php">Add New Product</a></li>
            <li><a href="orders.php" class="active">Manage Orders</a></li>
             <li><a href="../public/logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="dashboard-content">
        <h2>Customer Orders</h2>
        
        <?php if($message): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while($order = mysqli_fetch_assoc($result)): ?>
                <div class="card" style="margin-bottom: 1.5rem;">
                    <div style="display:flex; justify-content:space-between; flex-wrap:wrap; border-bottom:1px solid #eee; padding-bottom:0.5rem; margin-bottom:1rem;">
                        <div>
                            <strong>Order #<?php echo $order['order_id']; ?></strong>
                            <span style="margin-left:10px; font-size:0.9rem; color:#777;"><?php echo date('M d, Y', strtotime($order['order_date'])); ?></span>
                        </div>
                        <div>
                            Status: 
                            <span class="badge <?php echo ($order['status']=='delivered'?'badge-success':($order['status']=='rejected'?'badge-danger':'badge-warning')); ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:1rem; margin-bottom:1rem;">
                        <div>
                            <h4 style="font-size:1rem;">Customer Details</h4>
                            <p style="margin-bottom:0;">Name: <?php echo htmlspecialchars($order['customer_name']); ?></p>
                            <p>Address: <?php echo htmlspecialchars($order['shipping_address']); ?></p>
                            <?php if($order['latitude'] && $order['longitude']): ?>
                                <a href="https://www.google.com/maps?q=<?php echo $order['latitude']; ?>,<?php echo $order['longitude']; ?>" target="_blank" class="btn btn-sm btn-outline" style="margin-top: 5px;">
                                    <i class="fas fa-map-marker-alt"></i> View Tracking Location
                                </a>
                            <?php else: ?>
                                <small class="text-muted">Location not available</small>
                            <?php endif; ?>
                        </div>
                        <div class="text-right">
                             <h4 style="font-size:1rem;">Order Total</h4>
                             <p class="product-price">Rs. <?php echo $order['total_amount']; ?></p>
                        </div>
                    </div>

                    <div style="background:#f9f9f9; padding: 1rem; border-radius:4px; margin-bottom:1rem;">
                        <h4 style="font-size:0.9rem; margin-bottom:0.5rem;">Items Ordered:</h4>
                        <ul style="list-style: disc; margin-left: 1.5rem;">
                            <?php
                            // Fetch items for this order linked to this vendor
                            $oid = $order['order_id'];
                            $items_sql = "SELECT p.name, oi.quantity, oi.price 
                                          FROM order_items oi 
                                          JOIN products p ON oi.product_id = p.id 
                                          WHERE oi.order_id = '$oid' AND p.vendor_id = '$vendor_id'";
                            $items_res = mysqli_query($conn, $items_sql);
                            while($item = mysqli_fetch_assoc($items_res)):
                            ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($item['name']); ?></strong> 
                                    x <?php echo $item['quantity']; ?> 
                                    (Rs. <?php echo $item['price']; ?>)
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>

                    <form action="orders.php" method="POST" style="display:flex; gap:1rem; align-items:center;">
                        <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                        <label>Update Status:</label>
                        <select name="status" class="form-control" style="width:auto; padding:0.4rem;">
                            <option value="pending" <?php if($order['status']=='pending') echo 'selected'; ?>>Pending</option>
                            <option value="approved" <?php if($order['status']=='approved') echo 'selected'; ?>>Approved</option>
                            <option value="delivered" <?php if($order['status']=='delivered') echo 'selected'; ?>>Delivered</option>
                            <option value="rejected" <?php if($order['status']=='rejected') echo 'selected'; ?>>Rejected</option>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-sm btn-primary">Update</button>
                    </form>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="card">
                <p class="text-center">No orders received yet.</p>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php include('../includes/footer.php'); ?>
