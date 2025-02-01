<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("connect.php");

if (isset($_POST['signIn'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Hash the password for comparison

    // Check if the user exists
    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        die("Error in query: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        // User found, set session and redirect to dashboard
        $row = mysqli_fetch_assoc($result);
        $_SESSION['email'] = $row['email'];
        $_SESSION['user_id'] = $row['id'];
        header("Location: dashboard.php");
        exit(); // Stop further execution
    } else {
        // Invalid credentials
        echo "<script>alert('Invalid email or password.'); window.location.href='index.php';</script>";
    }
} else {
    // If form is not submitted, redirect to index.php
    header("Location: index.php");
    exit();
}
?>