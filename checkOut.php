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

// 1. Calculate total from waga_cart_item + products
$sqlTotal = "SELECT SUM(p.product_price * ci.quantity) AS total_amount
             FROM waga_cart_item ci
             JOIN products p ON ci.product_id = p.id
             WHERE ci.cart_id=?";
$stmtTotal = mysqli_prepare($conn, $sqlTotal);
mysqli_stmt_bind_param($stmtTotal, "i", $cartId);
mysqli_stmt_execute($stmtTotal);
$resultTotal = mysqli_stmt_get_result($stmtTotal);
$rowTotal = mysqli_fetch_assoc($resultTotal);

$totalAmount = $rowTotal['total_amount'] ?? 0;

// 2. Update waga_cart with total_sum + mark as paid
$sqlUpdate = "UPDATE waga_cart 
              SET total_sum=?, order_status='paid' 
              WHERE id=?";
$stmtUpdate = mysqli_prepare($conn, $sqlUpdate);
mysqli_stmt_bind_param($stmtUpdate, "di", $totalAmount, $cartId);
mysqli_stmt_execute($stmtUpdate);

// 3. Redirect to order history
header("Location: orderHistory.php?success=paid");
exit;
