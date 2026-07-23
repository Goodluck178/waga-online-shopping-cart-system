<?php
require "config.php";
require "header.php";

// Only admin can access
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
?>


<?php
if (isset($_GET['add']) && $_GET['add'] == "success") {
    echo "<p class='signupsuccess successMessage'>Product added successfully</p>";
}

if (isset($_GET['error'])) {
    if ($_GET['error'] == "emptyfields") echo "<p class='signuperror'>Fill in all fields</p>";
    if ($_GET['error'] == "invalidprice") echo "<p class='signuperror'>Price must be a number</p>";
    if ($_GET['error'] == "filetype") echo "<p class='signuperror'>Invalid file type (jpg, jpeg, png only)</p>";
    if ($_GET['error'] == "fileerror") echo "<p class='signuperror'>Error uploading file</p>";
    if ($_GET['error'] == "filesize") echo "<p class='signuperror'>File too large (max 1MB)</p>";
    if ($_GET['error'] == "sqlerror") echo "<p class='signuperror'>Database error</p>";
}
?>

<?php
if (isset($_POST['submit'])) {

    $productName = trim($_POST['productName']);
    $productPrice = trim($_POST['productPrice']);
    $file = $_FILES['file'];

    if (empty($productName) || empty($productPrice) || empty($file['name'])) {
        header("Location: adminUpload.php?error=emptyfields");
        exit;
    }

    if (!is_numeric($productPrice)) {
        header("Location: adminUpload.php?error=invalidprice");
        exit;
    }

    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];

    $fileExt = explode(".", $fileName);
    $fileActualExt = strtolower(end($fileExt));

    $allowed = array("jpg", "jpeg", "png");

    if (!in_array($fileActualExt, $allowed)) {
        header("Location: adminUpload.php?error=filetype");
        exit;
    }

    if ($fileError !== 0) {
        header("Location: adminUpload.php?error=fileerror");
        exit;
    }

    if ($fileSize > 10000000) {
        header("Location: adminUpload.php?error=filesize");
        exit;
    }

    $fileNameNew = "product_" . uniqid() . "." . $fileActualExt;
    $fileDestination = 'uploads/' . $fileNameNew;

    move_uploaded_file($fileTmpName, $fileDestination);

    $sql = "INSERT INTO products (product_name, product_price, product_image) VALUES (?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: adminUpload.php?error=sqlerror");
        exit;
    }

    mysqli_stmt_bind_param($stmt, "sss", $productName, $productPrice, $fileNameNew);
    mysqli_stmt_execute($stmt);

    header("Location: adminUpload.php?add=success");
    exit;
}
?>
<link rel="stylesheet" href="style.css">
<main class="wrapper-main">

    <div class="formContainer">
        <h1>Add Products</h1>

        <form action="adminUpload.php" method="post" enctype="multipart/form-data">
            <input type="text" name="productName" placeholder="Enter Product Name">
            <input type="number" name="productPrice" step="0.01" min="0" placeholder="Enter Product Price">

            <p>Choose Product Image:</p>
            <input type="file" name="file">

            <!-- ADMIN SHOULD NOT SEE USER PAGE -->
            <a href="adminProducts.php" class="btn btn-secondary mb-3 manageProduct">
                Manage Products
            </a>

            <button type="submit" name="submit" class="btn btn-secondary mb-3 addProduct">Add Products</button>
    
        </form>
    </div>

</main>

<?php require "footer.php"; ?>