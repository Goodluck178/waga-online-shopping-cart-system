<?php 
require "header.php";
?>

<main class="wrapper-main">

    <h1>Login Status</h1>

    <?php
    // ERROR MESSAGES
    if (isset($_GET['error'])) {

        if ($_GET['error'] == "emptyfields") {
            echo "<p class='signuperror'>Fill in all fields</p>";
        }
        else if ($_GET['error'] == 'invalidemail') {
            echo "<p class='signuperror'>Invalid email format</p>";
        }
        else if ($_GET['error'] == 'invalidmobile') {
            echo "<p class='signuperror'>Mobile number must be digits only and max 8 digits</p>";
        }
        else if ($_GET['error'] == 'shortpassword') {
            echo "<p class='signuperror'>Password must be at least 5 characters</p>";
        }
        else if ($_GET['error'] == 'wrongpassword') {
            echo "<p class='signuperror'>Incorrect password</p>";
        }
        else if ($_GET['error'] == 'nouser') {
            echo "<p class='signuperror'>No user found with that email or mobile number</p>";
        }
        else if ($_GET['error'] == 'sqlerror') {
            echo "<p class='signuperror'>Database error. Try again later.</p>";
        }
    }

    // SUCCESS MESSAGE
    else if (isset($_GET['login']) && $_GET['login'] == "success") {
        echo "<p class='signupsuccess'>Login successful</p>";
    }

    ?>

    <?php 
    // LOGIN STATUS BOX
    if (isset($_SESSION['userId'])) {
        echo "<p class='login-status'>You are logged in!</p>";
    } else {
        echo "<p class='login-status'>You are logged out!</p>";
    }
    ?>

</main>

<?php 
require "footer.php";
?>
