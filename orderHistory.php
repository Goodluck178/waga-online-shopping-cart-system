<?php
require "config.php";
session_start();

if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['userId'];

// Handle Clear History button
if (isset($_GET['clear']) && $_GET['clear'] === "1") {

    // Delete only PAID orders for this user
    $sqlDelete = "DELETE FROM waga_cart WHERE user_id=? AND order_status='paid'";
    $stmtDelete = mysqli_prepare($conn, $sqlDelete);
    mysqli_stmt_bind_param($stmtDelete, "i", $userId);
    mysqli_stmt_execute($stmtDelete);

    header("Location: orderHistory.php?cleared=1");
    exit;
}

// Fetch all paid orders
$sql = "SELECT id, order_status, total_sum, created_at 
        FROM waga_cart 
        WHERE user_id=? AND order_status='paid' 
        ORDER BY created_at DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<h2>Your Orders</h2>

<?php if (isset($_GET['cleared'])): ?>
    <p style="color: green;">Order history cleared successfully.</p>
<?php endif; ?>

<!-- Clear History Button -->
<form method="GET" action="orderHistory.php" style="margin-bottom: 20px;">
    <input type="hidden" name="clear" value="1">
    <button type="submit" 
            style="padding:10px 20px; background:red; color:white; border:none; cursor:pointer;">
        Clear Order History
    </button>
</form>

<?php while ($row = mysqli_fetch_assoc($result)): ?>
    <div style="padding:10px; border:1px solid #ccc; margin-bottom:10px;">
        <strong>Order #<?= $row['id'] ?></strong><br>
        Status: <?= ucfirst($row['order_status']) ?><br>
        Date: <?= $row['created_at'] ?><br>
        <strong>Total: €<?= number_format($row['total_sum'], 2) ?></strong>
    </div>
<?php endwhile; ?>
