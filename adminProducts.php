<?php
require "config.php";
require "header.php";

if (!isset($_SESSION['userId']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<main class="wrapper-main" style="max-width:900px;">

<h1 class="mb-4">Manage Products</h1>

<?php
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0): ?>

<div class="row g-4">

<?php while ($row = mysqli_fetch_assoc($result)): ?>

    <div class="col-md-4">
        <div class="card shadow-sm">

            <img src="uploads/<?php echo $row['product_image']; ?>" 
                 class="card-img-top"
                 style="height:200px; object-fit:cover;">

            <div class="card-body text-center">
                <h5 class="card-title"><?php echo $row['product_name']; ?></h5>
                <p class="card-text fw-bold">€<?php echo $row['product_price']; ?></p>

                <a href="adminEditProduct.php?id=<?php echo $row['id']; ?>" 
                   class="btn btn-primary btn-sm me-2">
                   Edit
                </a>

                <a href="adminDeleteProduct.php?id=<?php echo $row['id']; ?>" 
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Delete this product?');">
                   Delete
                </a>
            </div>

        </div>
    </div>

<?php endwhile; ?>

</div>

<?php else: ?>

<p>No products found.</p>

<?php endif; ?>

</main>

<?php require "footer.php"; ?>
