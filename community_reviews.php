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

// Fetch all reviews
$reviewsQuery = mysqli_query($conn, "SELECT reviews.*, users.firstName, users.lastName 
                                     FROM reviews 
                                     JOIN users ON reviews.user_id = users.id");
$reviews = mysqli_fetch_all($reviewsQuery, MYSQLI_ASSOC);

// Handle review submission
if (isset($_POST['submit_review'])) {
    $marketId = $_POST['market_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Insert review into the database
    $insertReviewQuery = "INSERT INTO reviews (user_id, market_id, rating, comment, review_date) 
                          VALUES ({$user['id']}, $marketId, $rating, '$comment', NOW())";
    if (mysqli_query($conn, $insertReviewQuery)) {
        echo "<script>alert('Review submitted successfully!');</script>";
    } else {
        echo "<script>alert('Error submitting review: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Community Reviews - Farmers' Market</title>
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
                            <a class="nav-link" href="vendor_tracking.php">
                                <i class="fas fa-store"></i> Vendor Tracking
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="community_reviews.php">
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
                    <h1 class="h2">Community Reviews</h1>
                </div>

                <!-- Write a Review -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Write a Review</h5>
                        <form method="post" action="">
                            <div class="form-group">
                                <label for="marketSelect">Select Market</label>
                                <select class="form-control" id="marketSelect" name="market_id" required>
                                    <?php
                                    $marketsQuery = mysqli_query($conn, "SELECT id, name FROM markets");
                                    while ($row = mysqli_fetch_assoc($marketsQuery)) {
                                        echo "<option value='{$row['id']}'>{$row['name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="rating">Rating</label>
                                <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" required>
                            </div>
                            <div class="form-group">
                                <label for="comment">Comment</label>
                                <textarea class="form-control" id="comment" name="comment" rows="3" required></textarea>
                            </div>
                            <button type="submit" name="submit_review" class="btn btn-primary mt-3">Submit Review</button>
                        </form>
                    </div>
                </div>

                <!-- Recent Reviews -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Recent Reviews</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Market</th>
                                    <th>Rating</th>
                                    <th>Comment</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reviews as $review): ?>
                                    <tr>
                                        <td><?php echo $review['firstName'] . ' ' . $review['lastName']; ?></td>
                                        <td><?php echo $review['market_id']; ?></td>
                                        <td><?php echo $review['rating']; ?>/5</td>
                                        <td><?php echo $review['comment']; ?></td>
                                        <td><?php echo $review['review_date']; ?></td>
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