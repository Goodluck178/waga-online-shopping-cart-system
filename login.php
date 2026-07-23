<?php
session_start();
require "config.php";



if (isset($_POST['login'])) {

    $identifier = trim($_POST['identifier']);
    $password = trim($_POST['pwd']);

    // 1. Required fields
    if (empty($identifier) || empty($password)) {
        header("Location: index.php?error=emptyfields");
        exit();
    }

    // 2. Password length
    if (strlen($password) < 5) {
        header("Location: index.php?error=shortpassword");
        exit();
    }

    // 3. Determine if identifier is email or mobile
    if (strpos($identifier, "@") !== false) {
        if (!filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            header("Location: index.php?error=invalidemail");
            exit();
        }
    } else {
        if (!preg_match("/^[0-9]{1,8}$/", $identifier)) {
            header("Location: index.php?error=invalidmobile");
            exit();
        }
    }

    // 4. Query user
    $sql = "SELECT * FROM users WHERE email=? OR mobileNo=?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: index.php?error=sqlerror");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $identifier, $identifier);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {

        if (!password_verify($password, $row['password'])) {
            header("Location: index.php?error=wrongpassword");
            exit();
        }

        // Login success
        $_SESSION['userId'] = $row['id'];
        $_SESSION['userEmail'] = $row['email'];
        $_SESSION['userMobileNo'] = $row['mobileNo'];
        $_SESSION['role'] = $row['role'];   // <-- IMPORTANT
        $_SESSION['profileImage'] = $row['profile_image'];
        $_SESSION['name'] = $row['name'];
        $_SESSION['surname'] = $row['surname'];

        // Redirect based on role
        if ($row['role'] === 'user') {
            header("Location: userUpload.php?login=success");
            exit();
        } 
        else if ($row['role'] === 'admin') {
            header("Location: adminUpload.php?login=success");
            exit();
        }
    }
    else {
        header("Location: index.php?error=nouser");
        exit();
    }
}

require "header.php";
?>
