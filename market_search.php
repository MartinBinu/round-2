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
$userQuery = mysqli_query($conn, "SELECT * FROM `users` WHERE email='$email'");
$user = mysqli_fetch_assoc($userQuery);

// Default location (Mumbai)
$latitude = 19.0760;
$longitude = 72.8777;
$markets = [];

// Function to get coordinates using OpenStreetMap Nominatim
function getCoordinates($location) {
    $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($location);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36");

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// Clear search cache if the page is loaded without a search request
if (!isset($_GET['search'])) {
    unset($_GET['location']);
    $markets = []; // Clear markets array
}

// Handle search
if (isset($_GET['search'])) {
    $location = $_GET['location'];
    $geo_json = getCoordinates($location);

    if (!empty($geo_json)) {
        $latitude = $geo_json[0]['lat'];
        $longitude = $geo_json[0]['lon'];

        // Fetch markets within a 3000-meter radius
        $radius = 3000; // Radius in meters
        $earthRadius = 6371000; // Earth's radius in meters

        // Haversine formula to calculate distance
        $query = "SELECT *, 
                  ($earthRadius * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians(longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians(latitude)))) AS distance 
                  FROM markets 
                  HAVING distance < $radius 
                  ORDER BY distance";

        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            $markets[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Market Search - Farmers' Market</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link active" href="market_search.php"><i class="fas fa-search"></i> Market Search</a></li>
                        <li class="nav-item"><a class="nav-link" href="pre_ordering.php"><i class="fas fa-cart-plus"></i> Pre-ordering</a></li>
                        <li class="nav-item"><a class="nav-link" href="vendor_tracking.php"><i class="fas fa-store"></i> Vendor Tracking</a></li>
                        <li class="nav-item"><a class="nav-link" href="community_reviews.php"><i class="fas fa-comments"></i> Community Reviews</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </div>
            </nav>

            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Market Search</h1>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Search for Markets</h5>
                        <form method="GET" action="market_search.php">
                            <div class="form-group">
                                <label for="location">Enter Location or Pincode</label>
                                <input type="text" class="form-control" id="location" name="location" placeholder="e.g., Mumbai or 400001" required>
                            </div>
                            <button type="submit" name="search" class="btn btn-primary mt-3">Search</button>
                        </form>
                    </div>
                </div>

                <!-- Market List Container -->
                <?php if (!empty($markets)): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Markets Near You</h5>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Market Name</th>
                                        <th>Location</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($markets as $market): ?>
                                        <tr>
                                            <td><?php echo $market['name']; ?></td>
                                            <td><?php echo $market['location']; ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="highlightMarket(<?php echo $market['latitude']; ?>, <?php echo $market['longitude']; ?>, '<?php echo $market['name']; ?>')">
                                                    Highlight
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Interactive Map -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Interactive Map</h5>
                        <div id="map" style="height: 400px;"></div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://maps.gomaps.pro/maps/api/js?key=AlzaSyetgSJYRghDLoVh_Pve88LAmiqWAC0ufSl"></script>
    <script>
        let map;
        let markers = [];
        let searchRadius;

        function initMap() {
            const centerLocation = { lat: <?php echo $latitude; ?>, lng: <?php echo $longitude; ?> };

            // Initialize Map
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 12,
                center: centerLocation
            });

            // Add Radius Circle Only if a Search Has Been Performed
            <?php if (isset($_GET['search'])): ?>
                searchRadius = new google.maps.Circle({
                    strokeColor: "#FF0000",
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: "#FF0000",
                    fillOpacity: 0.2, // Semi-transparent fill
                    map: map,
                    center: centerLocation,
                    radius: 3000 // 3000 meters
                });
            <?php endif; ?>

            // Add Markers for Markets (initially hidden)
            const markets = [
                <?php foreach ($markets as $market): ?>
                    { lat: <?php echo $market['latitude']; ?>, lng: <?php echo $market['longitude']; ?>, name: "<?php echo $market['name']; ?>" },
                <?php endforeach; ?>
            ];

            markets.forEach(market => {
                const marker = new google.maps.Marker({
                    position: { lat: market.lat, lng: market.lng },
                    map: null, // Initially hidden
                    title: market.name
                });
                markers.push(marker);
            });
        }

        // Function to highlight a specific market
        function highlightMarket(lat, lng, name) {
            // Hide all markers
            markers.forEach(marker => marker.setMap(null));

            // Show the selected market's marker
            const selectedMarker = markers.find(marker => 
                marker.getPosition().lat() === lat && marker.getPosition().lng() === lng
            );
            if (selectedMarker) {
                selectedMarker.setMap(map);
                map.setCenter({ lat: lat, lng: lng });
            }
        }

        // Load map when page loads
        window.onload = initMap;
    </script>
</body>
</html>