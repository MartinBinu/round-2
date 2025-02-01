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

// Fetch favorite vendors
$favoriteVendorsQuery = mysqli_query($conn, "SELECT vendors.* FROM vendors 
                                             JOIN favorite_vendors ON vendors.id = favorite_vendors.vendor_id 
                                             WHERE favorite_vendors.user_id = {$user['id']}");
$favoriteVendors = mysqli_fetch_all($favoriteVendorsQuery, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendor Tracking - Farmers' Market</title>
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
                            <a class="nav-link" href="pre_ordering.php">
                                <i class="fas fa-cart-plus"></i> Pre-ordering
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="vendor_tracking.php">
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
                    <h1 class="h2">Vendor Tracking</h1>
                </div>

                <!-- Favorite Vendors -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Your Favorite Vendors</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Vendor Name</th>
                                    <th>Location</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($favoriteVendors as $vendor): ?>
                                    <tr>
                                        <td><?php echo $vendor['name']; ?></td>
                                        <td><?php echo $vendor['location']; ?></td>
                                        <td>
                                            <a href="vendor_products.php?vendor_id=<?php echo $vendor['id']; ?>" class="btn btn-sm btn-primary">View Products</a>
                                        </td>
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