<?php
function getOrCreatePendingCart(mysqli $conn, int $userId): int {
    // Check for existing pending cart
    $sql = "SELECT id FROM waga_cart WHERE user_id=? AND order_status='pending' LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        return (int)$row['id'];
    }

    // Create new pending cart
    $sqlInsert = "INSERT INTO waga_cart (user_id, order_status) VALUES (?, 'pending')";
    $stmt2 = mysqli_prepare($conn, $sqlInsert);
    mysqli_stmt_bind_param($stmt2, "i", $userId);
    mysqli_stmt_execute($stmt2);

    return mysqli_insert_id($conn);
}
