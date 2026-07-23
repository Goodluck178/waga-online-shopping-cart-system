<?php
require "config.php";
require "header.php";

// Only admin can access
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// Ensure product ID exists
if (!isset($_GET['id'])) {
    header("Location: viewProducts.php");
    exit;
}

$productId = intval($_GET['id']);

// Fetch product
$sql = "SELECT * FROM products WHERE id=?";
$stmt = mysqli_stmt_init($conn);
mysqli_stmt_prepare($stmt, $sql);
mysqli_stmt_bind_param($stmt, "i", $productId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$row = mysqli_fetch_assoc($result)) {
    header("Location: viewProducts.php");
    exit;
}

$productName = $row['product_name'];
$productPrice = $row['product_price'];
$productImage = $row['product_image'];
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<main class="wrapper-main" style="max-width:600px;">

<h1 class="mb-4">Edit Product</h1>

<!-- SUCCESS MESSAGE -->
<?php if (isset($_GET['update']) && $_GET['update'] == "success"): ?>
    <p class="signupsuccess">Product updated successfully</p>
<?php endif; ?>

<!-- ERROR MESSAGES -->
<?php if (isset($_GET['error'])): ?>
    <p class="signuperror">
        <?php
        switch ($_GET['error']) {
            case "emptyfields": echo "Fill in all fields"; break;
            case "invalidprice": echo "Price must be a number"; break;
            case "filetype": echo "Invalid file type (jpg, jpeg, png only)"; break;
            case "fileerror": echo "Error uploading file"; break;
            case "filesize": echo "File too large (max 1MB)"; break;
            case "sqlerror": echo "Database error"; break;
        }
        ?>
    </p>
<?php endif; ?>

<form action="editProduct.php?id=<?php echo $productId; ?>" method="post" enctype="multipart/form-data">

    <label class="fw-bold">Product Name</label>
    <input type="text" name="productName" class="form-control mb-3"
           value="<?php echo htmlspecialchars($productName); ?>">

    <label class="fw-bold">Product Price (€)</label>
    <input type="text" name="productPrice" class="form-control mb-3"
           value="<?php echo htmlspecialchars($productPrice); ?>">

    <label class="fw-bold ">Current Image</label><br>
    <img class="img" src="uploads/<?php echo $productImage; ?>" >
         

    <label class="fw-bold">Upload New Image (optional)</label>
    <input type="file" name="file" class="form-control mb-3">

    <button type="submit" name="update" class="btn btn-primary w-100">Update Product</button>
</form>

</main>

<?php
// UPDATE LOGIC
if (isset($_POST['update'])) {

    $newName = trim($_POST['productName']);
    $newPrice = trim($_POST['productPrice']);

    if (empty($newName) || empty($newPrice)) {
        header("Location: editProduct.php?id=$productId&error=emptyfields");
        exit;
    }

    if (!is_numeric($newPrice)) {
        header("Location: editProduct.php?id=$productId&error=invalidprice");
        exit;
    }

    // If a new image is uploaded
    if (!empty($_FILES['file']['name'])) {

        $file = $_FILES['file'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];

        $fileExt = explode(".", $fileName);
        $fileActualExt = strtolower(end($fileExt));

        $allowed = array("jpg", "jpeg", "png");

        if (!in_array($fileActualExt, $allowed)) {
            header("Location: editProduct.php?id=$productId&error=filetype");
            exit;
        }

        if ($fileError !== 0) {
            header("Location: editProduct.php?id=$productId&error=fileerror");
            exit;
        }

        if ($fileSize > 1000000) {
            header("Location: editProduct.php?id=$productId&error=filesize");
            exit;
        }

        // Create new file name
        $fileNameNew = "product_" . uniqid() . "." . $fileActualExt;
        $fileDestination = 'uploads/' . $fileNameNew;

        move_uploaded_file($fileTmpName, $fileDestination);

        // Delete old image
        if (file_exists("uploads/$productImage")) {
            unlink("uploads/$productImage");
        }

        $productImage = $fileNameNew;
    }

    // Update database
    $sqlUpdate = "UPDATE products SET product_name=?, product_price=?, product_image=? WHERE id=?";
    $stmt = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt, $sqlUpdate)) {
        header("Location: editProduct.php?id=$productId&error=sqlerror");
        exit;
    }

    mysqli_stmt_bind_param($stmt, "sssi", $newName, $newPrice, $productImage, $productId);
    mysqli_stmt_execute($stmt);

    header("Location: editProduct.php?id=$productId&update=success");
    exit;
}
?>

<?php require "footer.php"; ?>
