<?php
require "config.php";
require "cart_functions.php";
session_start();

if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['userId'];
$cartId = getOrCreatePendingCart($conn, $userId);

// Fetch cart items with product details
$sql = "SELECT 
            ci.id AS item_id, 
            p.product_name, 
            p.product_price, 
            p.product_image,
            ci.quantity
        FROM waga_cart_item ci
        JOIN products p ON ci.product_id = p.id
        WHERE ci.cart_id=?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $cartId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<link rel="stylesheet" href="style.css">

<h2>Your Cart</h2>

<?php while ($row = mysqli_fetch_assoc($result)): ?>

    <?php $subtotal = $row['product_price'] * $row['quantity']; ?>

    <div class="cart-item">

        <!-- Product Image -->
        <img class="cart-img" src="uploads/<?= $row['product_image'] ?>">

        <!-- Product Details -->
        <div class="cart-details">
            <strong><?= $row['product_name'] ?></strong><br>
            Price: €<?= number_format($row['product_price'], 2) ?><br>
            Qty: <?= $row['quantity'] ?><br>
            Subtotal: <strong>€<?= number_format($subtotal, 2) ?></strong>
        </div>

        <!-- Buttons under product -->
        <div class="cart-actions">
            <button class="add-more-btn" onclick="window.location.href='viewProducts.php'">
                Add More Items
            </button>

            <button class="remove-btn"
                onclick="window.location.href='removeFromCart.php?id=<?= $row['item_id'] ?>'">
                Remove
            </button>

            <button class="checkout-btn" onclick="window.location.href='checkout.php'">
                Checkout
            </button>
        </div>

    </div>

<?php endwhile; ?>
