<?php
require "config.php";
session_start();

if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit;
}

$itemId = intval($_GET['id']);

$sql = "DELETE FROM waga_cart_item WHERE id=?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $itemId);
mysqli_stmt_execute($stmt);

header("Location: cart.php?removed=1");
exit;
