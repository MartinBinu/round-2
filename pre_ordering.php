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

// Fetch user's pre-orders
$ordersQuery = mysqli_query($conn, "SELECT orders.*, produce.name AS produce_name, produce.price 
                                   FROM orders 
                                   JOIN produce ON orders.produce_id = produce.id 
                                   WHERE orders.user_id = {$user['id']}");
$orders = mysqli_fetch_all($ordersQuery, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pre-ordering - Farmers' Market</title>
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
                            <a class="nav-link active" href="pre_ordering.php">
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
                    <h1 class="h2">Pre-ordering</h1>
                </div>

                <!-- Pre-orders List -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Your Pre-orders</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Total Price</th>
                                    <th>Order Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><?php echo $order['id']; ?></td>
                                        <td><?php echo $order['produce_name']; ?></td>
                                        <td><?php echo $order['quantity']; ?></td>
                                        <td>$<?php echo $order['quantity'] * $order['price']; ?></td>
                                        <td><?php echo $order['order_date']; ?></td>
                                    </tr>
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