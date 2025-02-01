<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include("connect.php");

// Handle Registration
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
        echo "<script>alert('Email address already exists!');</script>";
    } else {
        // Insert new user into the database
        $insertQuery = "INSERT INTO users (firstName, lastName, email, password) 
                        VALUES ('$firstName', '$lastName', '$email', '$password')";
        if (mysqli_query($conn, $insertQuery)) {
            // Registration successful, refresh the page
            echo "<script>alert('Registration successful!'); window.location.href='index.php';</script>";
        } else {
            // Error in registration
            echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
        }
    }
}

// Handle Login
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
        echo "<script>alert('Invalid email or password.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title text-center">Farmers' Market</h1>
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="true">Login</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab" aria-controls="register" aria-selected="false">Register</button>
                            </li>
                        </ul>
                        <div class="tab-content mt-3" id="myTabContent">
                            <!-- Login Form -->
                            <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">
                                <form method="post" action="">
                                    <div class="mb-3">
                                        <label for="loginEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="loginEmail" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="loginPassword" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="loginPassword" name="password" required>
                                    </div>
                                    <button type="submit" name="signIn" class="btn btn-primary w-100">Login</button>
                                </form>
                            </div>
                            <!-- Register Form -->
                            <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                                <form method="post" action="">
                                    <div class="mb-3">
                                        <label for="firstName" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="firstName" name="fName" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="lastName" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lastName" name="lName" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="registerEmail" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="registerEmail" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="registerPassword" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="registerPassword" name="password" required>
                                    </div>
                                    <button type="submit" name="signUp" class="btn btn-success w-100">Register</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>