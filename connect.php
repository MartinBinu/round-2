<?php
$host = "127.0.0.1";  // Change if needed
$user = "root";         // Change if needed
$pass = "SHAUN1014";             // Change if needed
$db = "farmers_db";  // Change to your database
$port = 3307;

$conn=new mysqli($host,$user,$pass,$db,$port);
if($conn->connect_error){
    echo "Failed to connect DB".$conn->connect_error;
}
?>