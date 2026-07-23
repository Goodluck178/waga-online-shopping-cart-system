<?php
require "config.php";
require "cart_functions.php";
session_start();

// Must be logged in
if (!isset($_SESSION['userId'])) {
    header("Location: login.php?error=loginrequired");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId    = $_SESSION['userId'];
    $productId = intval($_POST['product_id']);
    $quantity  = max(1, intval($_POST['quantity']));

    // Fetch product
    $sqlProd = "SELECT product_price FROM products WHERE id=?";
    $stmtProd = mysqli_prepare($conn, $sqlProd);
    mysqli_stmt_bind_param($stmtProd, "i", $productId);
    mysqli_stmt_execute($stmtProd);
    $resultProd = mysqli_stmt_get_result($stmtProd);

    if (!$product = mysqli_fetch_assoc($resultProd)) {
        header("Location: viewProducts.php?error=productnotfound");
        exit;
    }

    // Get or create pending cart
    $cartId = getOrCreatePendingCart($conn, $userId);

    // Check if item already exists
    $sqlCheck = "SELECT id, quantity FROM waga_cart_item WHERE cart_id=? AND product_id=?";
    $stmtCheck = mysqli_prepare($conn, $sqlCheck);
    mysqli_stmt_bind_param($stmtCheck, "ii", $cartId, $productId);
    mysqli_stmt_execute($stmtCheck);
    $resCheck = mysqli_stmt_get_result($stmtCheck);

    if ($item = mysqli_fetch_assoc($resCheck)) {
        // Update quantity
        $newQty = $item['quantity'] + $quantity;

        $sqlUpdate = "UPDATE waga_cart_item SET quantity=? WHERE id=?";
        $stmtUpdate = mysqli_prepare($conn, $sqlUpdate);
        mysqli_stmt_bind_param($stmtUpdate, "ii", $newQty, $item['id']);
        mysqli_stmt_execute($stmtUpdate);

    } else {
        // Insert new item
        $sqlInsert = "INSERT INTO waga_cart_item (cart_id, product_id, quantity)
                      VALUES (?, ?, ?)";
        $stmtInsert = mysqli_prepare($conn, $sqlInsert);
        mysqli_stmt_bind_param($stmtInsert, "iii", $cartId, $productId, $quantity);
        mysqli_stmt_execute($stmtInsert);
    }

    header("Location: cart.php?added=success");
    exit;
}
