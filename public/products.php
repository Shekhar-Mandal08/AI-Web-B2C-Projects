<?php
require('../includes/db.php');
session_start();

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';

$sql = "SELECT * FROM products WHERE 1"; // Start with true

if ($search) {
    $sql .= " AND (name LIKE '%$search%' OR description LIKE '%$search%')";
}

if ($category && $category !== 'All') {
    $sql .= " AND category = '$category'";
}

$sql .= " ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

$base_url = '..';
include('../includes/header.php');
?>

<main class="container" style="padding-top: 2rem;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
        <h2 class="section-title" style="margin-bottom: 0; text-align: left;">Our Fresh Products</h2>
        
        <form action="products.php" method="GET" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <input type="text" name="search" class="form-control" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>" style="width: 200px;">
            <select name="category" class="form-control" style="width: 150px;">
                <option value="All">All Categories</option>
                <option value="Milk" <?php if($category=='Milk') echo 'selected'; ?>>Milk</option>
                <option value="Dairy" <?php if($category=='Dairy') echo 'selected'; ?>>Dairy Products</option>
                <option value="Yogurt" <?php if($category=='Yogurt') echo 'selected'; ?>>Yogurt</option>
                <option value="Butter" <?php if($category=='Butter') echo 'selected'; ?>>Butter/Ghee</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
        </form>
    </div>

    <div class="products-grid">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <?php 
                    $imagePath = ($row['image'] && file_exists('../images/'.$row['image'])) ? '../images/' . $row['image'] : '../images/milk.jpg';
                    // Fallback to absolute if milk.jpg not found, or use placeholder
                    if (!file_exists($imagePath) && $row['image']) $imagePath = '../images/' . $row['image']; 
                ?>
                <div class="product-card">
                    <div class="product-image">
                         <img src="<?php echo htmlspecialchars($imagePath); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>"
                            style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://via.placeholder.com/250?text=Milk+Product'">
                    </div>
                    <div class="product-info">
                        <span class="product-category"><?php echo htmlspecialchars($row['category']); ?></span>
                        <h3 class="product-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem; flex-grow: 1;">
                            <?php echo substr(htmlspecialchars($row['description']), 0, 80) . '...'; ?>
                        </p>
                        <div class="product-price">Rs. <?php echo htmlspecialchars($row['price']); ?></div>
                        
                        <div style="margin-top: auto; display: flex; gap: 0.5rem; justify-content: center;">
                             <?php if($row['stock'] > 0): ?>
                                <form action="cart.php" method="POST">
                                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="action" value="add">
                                    <button type="submit" class="btn btn-sm btn-primary">Add to Cart</button>
                                </form>
                            <?php else: ?>
                                <span class="badge badge-danger">Out of Stock</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No products found matching your criteria.</p>
        <?php endif; ?>
    </div>

</main>

<?php include('../includes/footer.php'); ?>
