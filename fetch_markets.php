<?php
include("connect.php");

// Get the latitude, longitude, and radius from the request
$lat = $_GET['lat'];
$lng = $_GET['lng'];
$radius = $_GET['radius']; // Radius in kilometers

// Calculate the bounding box for the search
$earthRadius = 6371; // Earth's radius in kilometers
$maxLat = $lat + rad2deg($radius / $earthRadius);
$minLat = $lat - rad2deg($radius / $earthRadius);
$maxLng = $lng + rad2deg($radius / $earthRadius / cos(deg2rad($lat)));
$minLng = $lng - rad2deg($radius / $earthRadius / cos(deg2rad($lat)));

// Fetch markets within the bounding box
$query = "SELECT * FROM markets 
          WHERE latitude BETWEEN $minLat AND $maxLat 
          AND longitude BETWEEN $minLng AND $maxLng";
$result = mysqli_query($conn, $query);

$markets = [];
while ($row = mysqli_fetch_assoc($result)) {
    $markets[] = $row;
}

// Return the markets as JSON
header('Content-Type: application/json');
echo json_encode($markets);
?>