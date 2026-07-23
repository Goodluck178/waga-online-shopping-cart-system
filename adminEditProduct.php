<?php
require "config.php";
require "header.php";

// Only admin can access
if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: adminProducts.php");
    exit;
}

$id = intval($_GET['id']);

// Fetch product
$sql = "SELECT * FROM products WHERE id=?";
$stmt = mysqli_stmt_init($conn);

if (!mysqli_stmt_prepare($stmt, $sql)) {
    echo "<p class='signuperror'>SQL Error</p>";
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (!$product = mysqli_fetch_assoc($result)) {
    echo "<p class='signuperror'>Product not found</p>";
    exit;
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<main class="wrapper-main" style="max-width:600px;">

<h1>Edit Product</h1>

<?php if (isset($_GET['update']) && $_GET['update'] == "success"): ?>
    <div class="alert alert-success">
        Product updated successfully
    </div>

    <!-- ⭐ NEW BUTTONS ADDED HERE ⭐ -->
    <a href="adminProducts.php" class="btn btn-secondary mb-3">Back to Product List</a>
    <a href="viewProducts.php" class="btn btn-primary mb-3">View as User</a>
<?php endif; ?>

<form action="adminEditProduct.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">

    <label>Product Name:</label>
    <input type="text" name="productName" class="form-control mb-3"
           value="<?php echo $product['product_name']; ?>">

    <label>Product Price:</label>
    <input type="text" name="productPrice" class="form-control mb-3"
           value="<?php echo $product['product_price']; ?>">

    <label>Current Image:</label><br>
    <img src="uploads/<?php echo $product['product_image']; ?>" width="150" class="mb-3">

    <label>Upload New Image (optional):</label>
    <input type="file" name="file" class="form-control mb-3">

    <button type="submit" name="update" class="btn btn-primary">Update Product</button>
</form>

</main>

<?php
// UPDATE LOGIC
if (isset($_POST['update'])) {

    $name = trim($_POST['productName']);
    $price = trim($_POST['productPrice']);

    if (empty($name) || empty($price)) {
        echo "<p class='signuperror'>Fill in all fields</p>";
        exit;
    }

    $newImage = $product['product_image'];

    // If new image uploaded
    if (!empty($_FILES['file']['name'])) {

        $file = $_FILES['file'];
        $fileName = $file['name'];
        $fileTmp = $file['tmp_name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowed = ['jpg','jpeg','png'];

        if (!in_array($fileExt, $allowed)) {
            echo "<p class='signuperror'>Invalid file type</p>";
            exit;
        }

        $newImage = "product_" . uniqid() . "." . $fileExt;
        move_uploaded_file($fileTmp, "uploads/" . $newImage);

        // Delete old image
        if (file_exists("uploads/" . $product['product_image'])) {
            unlink("uploads/" . $product['product_image']);
        }
    }

    // Update DB
    $sqlUpdate = "UPDATE products SET product_name=?, product_price=?, product_image=? WHERE id=?";
    $stmt2 = mysqli_stmt_init($conn);

    if (!mysqli_stmt_prepare($stmt2, $sqlUpdate)) {
        echo "<p class='signuperror'>SQL Error</p>";
        exit;
    }

    mysqli_stmt_bind_param($stmt2, "sssi", $name, $price, $newImage, $id);
    mysqli_stmt_execute($stmt2);

    header("Location: adminEditProduct.php?id=$id&update=success");
    exit;
}

require "footer.php";
?>
