<?php
session_start();
if (!isset($_SESSION['email'])) {
    // If user is not logged in, redirect to index.php
    header("Location: index.php");
    exit();
}

include("connect.php");

// Fetch user details
$email = $_SESSION['email'];
$userQuery = mysqli_query($conn, "SELECT users.* FROM `users` WHERE users.email='$email'");
$user = mysqli_fetch_assoc($userQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Farmers' Market</title>
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
                            <a class="nav-link active" href="dashboard.php">
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
                    <h1 class="h2">Dashboard</h1>
                </div>

                <!-- Welcome Message -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Welcome, <?php echo $user['firstName'] . ' ' . $user['lastName']; ?>!</h5>
                                <p class="card-text">Here's a summary of your recent activity.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>