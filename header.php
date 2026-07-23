<?php
session_start();
require "config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Waga Shop</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>

<header>
  <div class="header-login">

    <?php
    if (isset($_SESSION['userId'])) {

      $thumb = $_SESSION['profileImage'] ?? "default.png";

      echo "
        <img src='uploads/$thumb' class='nav-profile-thumb'>

        <form action='logout.php' method='post'>
          <button type='submit' name='logout' class='logout-btn'>Logout</button>
        </form>
      ";

    } else {

      echo "
        <form action='login.php' method='post'>
          <input type='text' name='identifier' autocomplete='off' placeholder='Email or Mobile Number' class='input-field'>
          <input type='password' name='pwd' autocomplete='new-password' placeholder='Password' class='input-field'>
          <button type='submit' name='login' class='login-btn'>Login</button>
        </form>

        <a href='signup.php' class='signup-btn'>Signup</a>
      ";
    }
    ?>

  </div>
</header>
