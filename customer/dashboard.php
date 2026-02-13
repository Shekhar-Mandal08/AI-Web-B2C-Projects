<?php
require('../includes/db.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: ../public/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch Orders
$sql = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY order_date DESC";
$result = mysqli_query($conn, $sql);

$base_url = '..';
include('../includes/header.php');
?>

<div class="container dashboard-layout">
    <aside class="sidebar">
        <h3 style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(255,255,255,0.1);">My Account</h3>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active">My Orders</a></li>
            <li><a href="../public/products.php">Shop Products</a></li>
            <li><a href="../public/cart.php">My Cart</a></li>
            <li><a href="../public/logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="dashboard-content">
        <h2>Order History</h2>
        
        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Items</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($order = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                <td>Rs. <?php echo $order['total_amount']; ?></td>
                                <td>
                                    <span class="badge <?php echo ($order['status']=='delivered'?'badge-success':($order['status']=='rejected'?'badge-danger':'badge-warning')); ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $oid = $order['id'];
                                    $isql = "SELECT p.name, oi.quantity FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE oi.order_id='$oid'";
                                    $ires = mysqli_query($conn, $isql);
                                    $items = [];
                                    while($item = mysqli_fetch_assoc($ires)){
                                        $items[] = $item['name'] . " (" . $item['quantity'] . ")";
                                    }
                                    echo implode(", ", $items);
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="card text-center">
                <p>You haven't placed any orders yet.</p>
                <a href="../public/products.php" class="btn btn-primary">Start Shopping</a>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php include('../includes/footer.php'); ?>
