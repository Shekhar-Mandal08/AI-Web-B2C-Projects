<?php
require('../includes/db.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'vendor') {
    header("Location: login.php");
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vendor_id = $_SESSION['user_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    
    // Image Upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../images/';
        // Ensure unique name
        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid('prod_') . '.' . $file_ext;
        $target_file = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image = $file_name;
        } else {
            $error = "Failed to upload image.";
        }
    }

    if (!$error) {
        $sql = "INSERT INTO products (vendor_id, name, description, price, stock, category, image) 
                VALUES ('$vendor_id', '$name', '$description', '$price', '$stock', '$category', '$image')";
        
        if (mysqli_query($conn, $sql)) {
            $message = "Product added successfully!";
        } else {
            $error = "Database Error: " . mysqli_error($conn);
        }
    }
}

$base_url = '..';
include('../includes/header.php');
?>

<div class="container dashboard-layout">
    <aside class="sidebar">
        <h3 style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(255,255,255,0.1);">Vendor Panel</h3>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Overview</a></li>
            <li><a href="products.php">My Products</a></li>
            <li><a href="add_product.php" class="active">Add New Product</a></li>
            <li><a href="orders.php">Manage Orders</a></li>
             <li><a href="../public/logout.php">Logout</a></li>
        </ul>
    </aside>

    <main class="dashboard-content">
        <h2>Add New Product</h2>
        
        <?php if($message): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <?php if($error): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <form action="add_product.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" class="form-control" required>
                        <option value="Milk">Milk</option>
                        <option value="Dairy">Dairy Products</option>
                        <option value="Yogurt">Yogurt</option>
                        <option value="Butter">Butter/Ghee</option>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="price">Price (Rs.)</label>
                        <input type="number" id="price" name="price" step="0.01" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock Quantity</label>
                        <input type="number" id="stock" name="stock" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4"></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Product Image</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*">
                </div>

                <button type="submit" class="btn btn-primary">Add Product</button>
            </form>
        </div>
    </main>
</div>

<?php include('../includes/footer.php'); ?>
