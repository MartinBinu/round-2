<?php
session_start();
include("connect.php");

// Redirect to login if user is not logged in
if (!isset($_SESSION['email'])) {
    header("location: index.php");
    exit();
}

// Fetch user details
$email = $_SESSION['email'];
$userQuery = mysqli_query($conn, "SELECT users.* FROM `users` WHERE users.email='$email'");
$user = mysqli_fetch_assoc($userQuery);

// Fetch products for the selected market
$marketId = $_GET['market_id'];
$marketQuery = mysqli_query($conn, "SELECT * FROM markets WHERE id = $marketId");
$market = mysqli_fetch_assoc($marketQuery);

$productsQuery = mysqli_query($conn, "SELECT * FROM produce WHERE market_id = $marketId");
$products = mysqli_fetch_all($productsQuery, MYSQLI_ASSOC);

// Handle pre-ordering
if (isset($_POST['pre_order'])) {
    $produceId = $_POST['produce_id'];
    $quantity = $_POST['quantity'];

    // Insert order into the database
    $insertOrderQuery = "INSERT INTO orders (user_id, produce_id, quantity, order_date) 
                         VALUES ({$user['id']}, $produceId, $quantity, NOW())";
    if (mysqli_query($conn, $insertOrderQuery)) {
        echo "<script>alert('Order placed successfully!');</script>";
    } else {
        echo "<script>alert('Error placing order: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produce Listings - Farmers' Market</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="market_search.php">
                                <i class="fas fa-search"></i> Market Search
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="produce_listings.php">
                                <i class="fas fa-shopping-basket"></i> Produce Listings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="pre_ordering.php">
                                <i class="fas fa-cart-plus"></i> Pre-ordering
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="vendor_tracking.php">
                                <i class="fas fa-store"></i> Vendor Tracking
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="community_reviews.php">
                                <i class="fas fa-comments"></i> Community Reviews
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Produce Listings at <?php echo $market['name']; ?></h1>
                </div>

                <!-- Product Listings -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Available Products</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo $product['name']; ?></td>
                                        <td>$<?php echo $product['price']; ?></td>
                                        <td><?php echo $product['stock']; ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#orderModal<?php echo $product['id']; ?>">
                                                Pre-order
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Pre-order Modal -->
                                    <div class="modal fade" id="orderModal<?php echo $product['id']; ?>" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="orderModalLabel">Pre-order <?php echo $product['name']; ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <form method="post" action="">
                                                        <input type="hidden" name="produce_id" value="<?php echo $product['id']; ?>">
                                                        <div class="form-group">
                                                            <label for="quantity">Quantity</label>
                                                            <input type="number" class="form-control" id="quantity" name="quantity" min="1" max="<?php echo $product['stock']; ?>" required>
                                                        </div>
                                                        <button type="submit" name="pre_order" class="btn btn-primary mt-3">Place Order</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>