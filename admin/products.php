<?php
require('../includes/db.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: login.php");
    exit;
}

$vendor_id = $_SESSION['user_id'];
$message = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    // Verify ownership
    $check = mysqli_query($conn, "SELECT id FROM products WHERE id='$id' AND vendor_id='$vendor_id'");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "DELETE FROM products WHERE id='$id'");
        $message = "Product deleted successfully.";
    } else {
        $message = "Error: Product not found or access denied.";
    }
}

// Fetch Products
$sql = "SELECT * FROM products WHERE vendor_id = '$vendor_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

$base_url = '..';
include('../includes/header.php');
?>

<div class="container dashboard-layout">
    <aside class="sidebar">
        <h3 style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(255,255,255,0.1);">Vendor Panel</h3>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Overview</a></li>
            <li><a href="products.php" class="active">My Products</a></li>
            <li><a href="add_product.php">Add New Product</a></li>
            <li><a href="orders.php">Manage Orders</a></li>
            <li><a href="../public/logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="dashboard-content">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 1rem;">
            <h2>My Products</h2>
            <a href="add_product.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New</a>
        </div>

        <?php if($message): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td>
                                    <?php 
                                    $img = $row['image'] ? "../images/" . $row['image'] : "https://via.placeholder.com/50";
                                    ?>
                                    <img src="<?php echo htmlspecialchars($img); ?>" alt="prod" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                </td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['category']); ?></td>
                                <td>Rs. <?php echo htmlspecialchars($row['price']); ?></td>
                                <td>
                                    <?php if($row['stock'] < 10): ?>
                                        <span class="badge badge-warning">Low: <?php echo $row['stock']; ?></span>
                                    <?php else: ?>
                                        <?php echo $row['stock']; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- Edit Link could go to edit_product.php?id=... -->
                                    <a href="products.php?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No products found. Start selling by adding one!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<?php include('../includes/footer.php'); ?>
