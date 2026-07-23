<?php
require "config.php";
require "header.php";

if (!isset($_SESSION['userId'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['userId'];
$name = $_SESSION['name'];
$surname = $_SESSION['surname'];
$profileImage = $_SESSION['profileImage'];
?>

<main class="wrapper-main">

<h1>User Profile</h1>

<!-- DISPLAY PROFILE IMAGE -->
<div class="profile-container">
    <img src="uploads/<?php echo $profileImage; ?>" class="nav-profile-thumb">
    <p><strong><?php echo $name . " " . $surname; ?></strong></p>
</div>

<!-- STATUS MESSAGES -->
<?php
if (isset($_GET['upload']) && $_GET['upload'] == "success") {
    echo "<p class='signupsuccess'>Profile image updated successfully</p>";
}
?>

<!-- UPLOAD FORM OR SKIP BUTTON -->
<?php if ($profileImage == 'default.png') : ?>

    <form action="userUpload.php" method="post" enctype="multipart/form-data">
        <p>Choose a profile image:</p>
        <input type="file" name="file" required>
        <button type="submit" name="submit">Upload</button>
    </form>

<?php else : ?>

    <p>You already uploaded a profile picture.</p>

    <a href="index.php" class="skip-btn">Skip Upload</a>

    <br>  <br>

    <!--  NEW BUTTON: Continue to Shop  -->
    <a href="viewProducts.php" class="btn btn-primary mt-3">Continue to Shop</a>

<?php endif; ?>

<!-- FILE UPLOAD LOGIC -->
<?php
if (isset($_POST['submit'])) {

    $file = $_FILES['file'];

    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];

    $fileExt = explode(".", $fileName);
    $fileActualExt = strtolower(end($fileExt));

    $allowed = array("jpg", "jpeg", "png");

    if (!in_array($fileActualExt, $allowed)) {
        echo "<p class='signuperror'>You cannot upload files of this type</p>";
        exit;
    }

    if ($fileError !== 0) {
        echo "<p class='signuperror'>There was an error uploading your file</p>";
        exit;
    }

    if ($fileSize > 1000000) {
        echo "<p class='signuperror'>Your file is too big</p>";
        exit;
    }

    // Create new file name
    $fileNameNew = "profile" . $userId . "." . $fileActualExt;
    $fileDestination = 'uploads/' . $fileNameNew;

    move_uploaded_file($fileTmpName, $fileDestination);

    // UPDATE DATABASE
    $sqlUpdate = "UPDATE users SET profile_image=? WHERE id=?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sqlUpdate)) {
        echo "<p class='signuperror'>SQL Error</p>";
        exit;
    }

    mysqli_stmt_bind_param($stmt, "si", $fileNameNew, $userId);
    mysqli_stmt_execute($stmt);

    // UPDATE SESSION
    $_SESSION['profileImage'] = $fileNameNew;

    header("Location: userUpload.php?upload=success");
    exit;
}
?>

</main>

<?php
require "footer.php";
?>
