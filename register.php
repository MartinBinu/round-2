<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("connect.php");

if (isset($_POST['signUp'])) {
    $firstName = $_POST['fName'];
    $lastName = $_POST['lName'];
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Hash the password

    // Check if the email already exists
    $checkEmail = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $checkEmail);

    if (!$result) {
        die("Error in query: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        // Email already exists
        echo "<script>alert('Email address already exists!'); window.location.href='index.php';</script>";
    } else {
        // Insert new user into the database
        $insertQuery = "INSERT INTO users (firstName, lastName, email, password) 
                        VALUES ('$firstName', '$lastName', '$email', '$password')";
        if (mysqli_query($conn, $insertQuery)) {
            // Registration successful, set session and redirect to dashboard
            $_SESSION['email'] = $email;
            $_SESSION['user_id'] = mysqli_insert_id($conn);
            header("Location: dashboard.php");
            exit(); // Stop further execution
        } else {
            // Error in registration
            echo "<script>alert('Error: " . mysqli_error($conn) . "'); window.location.href='index.php';</script>";
        }
    }
} else {
    // If form is not submitted, redirect to index.php
    header("Location: index.php");
    exit();
}
?>