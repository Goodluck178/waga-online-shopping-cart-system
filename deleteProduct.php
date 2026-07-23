<?php
require "config.php";
require "header.php";

// Only admin can delete products
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Ensure product ID exists
if (!isset($_GET['id'])) {
    header("Location: viewProducts.php");
    exit;
}

$productId = intval($_GET['id']);

// Fetch product image
$sql = "SELECT product_image FROM products WHERE id=?";
$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, $sql);
mysqli_stmt_bind_param($stmt, "i", $productId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {

    $image = $row['product_image'];

    // Delete image file if it exists
    if (file_exists("uploads/$image")) {
        unlink("uploads/$image");
    }

    // Delete product from database
    $sqlDelete = "DELETE FROM products WHERE id=?";
    $stmt2 = mysqli_stmt_init($conn);
    mysqli_stmt_prepare($stmt2, $sqlDelete);
    mysqli_stmt_bind_param($stmt2, "i", $productId);
    mysqli_stmt_execute($stmt2);
}

// Redirect back to product list
header("Location: viewProducts.php");
exit;
?>
