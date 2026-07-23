<?php
require "config.php";
require "header.php";

if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['order'])) {
    header("Location: index.php");
    exit;
}

$orderId = intval($_GET['order']);
?>

<main class="wrapper-main" style="max-width:600px; text-align:center;">

    <h1>Order Successful </h1>

    <p>Your order has been placed successfully.</p>

    <h3>Order Number: <strong>#<?php echo $orderId; ?></strong></h3>

    <a href="orderHistory.php" class="btn btn-primary mt-4">View Order History</a>
    <br><br>
    <a href="viewProducts.php" class="btn btn-secondary">Continue Shopping</a>

</main>

<?php require "footer.php"; ?>
