<?php
require "config.php";
require "header.php";

// Prevent admin from accessing user browsing page
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: adminProducts.php");
    exit;
}

// User must be logged in
if (!isset($_SESSION['userId'])) {
    header("Location: login.php?error=loginrequired");
    exit;
}

// Fetch all products
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $sql);
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">

<main class="container py-4">

    <h2 class="mb-4 text-center">Browse Products</h2>

    <div class="row g-4">

        <?php while ($row = mysqli_fetch_assoc($result)): ?>

            <div class="col-md-4">
                <div class="card product-card shadow-sm">

                    <img class="card-img-top product-img" src="uploads/<?= $row['product_image']; ?>">

                    <div class="card-body text-center">

                        <h5 class="card-title"><?= $row['product_name']; ?></h5>
                        <p class="fw-bold">€<?= $row['product_price']; ?></p>

                        <!-- Add to Cart Form -->
                        <form method="POST" action="addToCart.php" class="d-flex justify-content-center">
                            <input type="hidden" name="product_id" value="<?= $row['id']; ?>">

                            <input type="number" 
                                   name="quantity" 
                                   value="1" 
                                   min="1" 
                                   class="form-control quantity-input me-2">

                            <button type="submit" class="btn btn-success add-cart-btn">
                                Add to Cart
                            </button>
                        </form>

                    </div>
                </div>
            </div>

        <?php endwhile; ?>

    </div>

</main>

<?php require "footer.php"; ?>
