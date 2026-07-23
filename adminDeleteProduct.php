<?php
require "config.php";
session_start();

// Only admin can delete
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: adminProducts.php");
    exit;
}

$id = intval($_GET['id']);

// 1. Get product image name
$sqlImg = "SELECT product_image FROM products WHERE id=?";
$stmtImg = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmtImg, $sqlImg)) {
    header("Location: adminProducts.php?error=sqlerror");
    exit;
}

mysqli_stmt_bind_param($stmtImg, "i", $id);
mysqli_stmt_execute($stmtImg);
$result = mysqli_stmt_get_result($stmtImg);

if ($row = mysqli_fetch_assoc($result)) {
    $image = $row['product_image'];

    // Delete image file
    if (file_exists("uploads/" . $image)) {
        unlink("uploads/" . $image);
    }
}

// ⭐ NEW STEP: DELETE CART ITEMS FIRST (Fixes foreign key error)
$sqlCart = "DELETE FROM waga_cart_item WHERE product_id=?";
$stmtCart = mysqli_stmt_init($conn);

if (mysqli_stmt_prepare($stmtCart, $sqlCart)) {
    mysqli_stmt_bind_param($stmtCart, "i", $id);
    mysqli_stmt_execute($stmtCart);
}

// 2. Delete product from DB
$sqlDelete = "DELETE FROM products WHERE id=?";
$stmtDel = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmtDel, $sqlDelete)) {
    header("Location: adminProducts.php?error=sqlerror");
    exit;
}

mysqli_stmt_bind_param($stmtDel, "i", $id);
mysqli_stmt_execute($stmtDel);

header("Location: adminProducts.php?delete=success");
exit;
?>
