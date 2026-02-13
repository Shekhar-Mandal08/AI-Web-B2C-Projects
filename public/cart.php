<?php
require('../includes/db.php');
session_start();

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $product_id = intval($_POST['product_id']);

    if ($action === 'add') {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]++;
        } else {
            $_SESSION['cart'][$product_id] = 1;
        }
        $_SESSION['msg'] = "Product added to cart!";
        $_SESSION['msg_type'] = "success";
    } elseif ($action === 'remove') {
        unset($_SESSION['cart'][$product_id]);
    }
    
    // Redirect to self to prevent form resubmission issue
    header("Location: cart.php");
    exit;
}

// Fetch Cart Data
$cart_items = [];
$total_price = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    // Safe because keys are from session and we cast to int in logic or we should sanitize keys if manual.
    // Actually we should sanitize identifiers.
    $sanitized_ids = [];
    foreach(array_keys($_SESSION['cart']) as $k) $sanitized_ids[] = intval($k);
    $ids = implode(',', $sanitized_ids);

    if ($ids) {
        $sql = "SELECT * FROM products WHERE id IN ($ids)";
        $result = mysqli_query($conn, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $row['quantity'] = $_SESSION['cart'][$row['id']];
            $row['subtotal'] = $row['price'] * $row['quantity'];
            $total_price += $row['subtotal'];
            $cart_items[] = $row;
        }
    }
}

$base_url = '..';
include('../includes/header.php');
?>

<main class="container" style="padding-top: 2rem;">
    <h2 class="section-title">Your Shopping Cart</h2>

    <?php if (empty($cart_items)): ?>
        <div class="text-center" style="padding: 4rem;">
            <p>Your cart is empty.</p>
            <a href="products.php" class="btn btn-primary">Start Shopping</a>
        </div>
    <?php else: ?>
        <div class="row" style="display:flex; gap: 2rem; flex-wrap: wrap;">
            <div style="flex: 2; min-width: 300px;">
                <div class="table-responsive cart-summary">
                    <table>
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: bold;"><?php echo htmlspecialchars($item['name']); ?></div>
                                    </td>
                                    <td>Rs. <?php echo $item['price']; ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td>Rs. <?php echo $item['subtotal']; ?></td>
                                    <td>
                                        <form action="cart.php" method="POST">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <input type="hidden" name="action" value="remove">
                                            <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="flex: 1; min-width: 300px;">
                <div class="card">
                    <h3>Cart Summary</h3>
                    <div style="display:flex; justify-content:space-between; margin-bottom: 1rem; margin-top: 1rem; font-size: 1.2rem; font-weight: bold;">
                        <span>Total:</span>
                        <span style="color: var(--accent-color);">Rs. <?php echo $total_price; ?></span>
                    </div>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="checkout.php" class="btn btn-primary" style="width: 100%; text-align: center;">Proceed to Checkout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline" style="width: 100%; text-align: center;">Login to Checkout</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php include('../includes/footer.php'); ?>
